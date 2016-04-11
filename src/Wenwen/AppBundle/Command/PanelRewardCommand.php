<?php
namespace Wenwen\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Jili\ApiBundle\Utility\SopUtil;

abstract class PanelRewardCommand extends ContainerAwareCommand
{
    protected $logger;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getArgument('date');

        $env = $this->getContainer()->get('kernel')->getEnvironment();

        // options
        $definitive = ($input->hasOption('definitive')) ? true : false;

        $this->log('Start executing');
        $this->log('    definitive= ' . ($definitive ? 'true' : 'false'));
        $this->log('    date=' . $date);

        // configs
        $url = $this->url();
        $auth = $this->sop_configure['auth'];

        // get data from SOP API
        $this->log('request URL: ' . $url);

        $history_list = $this->requestSOP($url, $date, $date, $auth['app_id'], $auth['app_secret']);
        $this->log("history_list: " . print_r($history_list, 1));

        // initialize the database connection
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();

        // transaction start
        $dbh->beginTransaction();

        //start inserting
        try {
            foreach ($history_list as $history) {

                if ($this->skipReward($history)) {
                    continue;
                }

                // get respondent
                $respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(array (
                    'id' => $history['app_mid']
                ));
                if (!$respondent) {
                    throw new \Exception('No SopRespondent for: ' . $history['app_mid'], 1);
                }

                // get panelist
                $user = $em->getRepository('JiliApiBundle:User')->findOneBy(array (
                    'id' => $respondent->getUserId()
                ));
                if (!$user) {
                    // maybe panelist withdrew
                    $this->log('No User. Skip user_id: ' . $respondent->getPanelistId());
                    continue;
                }

                // insert participation history
                $this->createParticipationHistory($history);

                // insert point history, task history, user points
                $this->getContainer()->get('points_manager')->updatePoints($respondent->getUserId(),
                  $this->point($history),
                  $this->type($history), // ad_category_id or point.exec_type
                  $this->task($history), //task_type_id
                  $this->comment($history));// task_name
            }
        } catch (\Exception $e) {
            $dbh->rollBack();
            $output->writeln($e->getMessage());
            $this->log('rollback');
            $this->log($e->getMessage());
            throw $e;
        }

        // rollBack or commit
        if ($definitive) {
            $this->log('commit');
            $dbh->commit();
        } else {
            $this->log('rollback');
            $dbh->rollBack();
        }
        $this->log('Finish executing');
    }

    public function requestSOP($url, $from_date, $to_date, $app_id, $secret)
    {
        // create sig
        $sop_params = array (
            'app_id' => $app_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'time' => time()
        );

        // request
        $sop_params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($sop_params, $secret);

        $response = $this->getContainer()->get('sop_api.client')->get($url . '?' . http_build_query($sop_params));

        // invalid request. Httpful retrun array if request are valid.
        if (!is_array($response->body)) {

            $content = 'failed to request SOP API: ' . $response->raw_body;

            //log
            $this->log($content);

            // slack notice
            $content = $content . '        request URL:' . $url;
            $this->getContainer()->get('alert_to_slack')->sendAlertToSlack($content);

            //emai notice
            $alertTo = $this->getContainer()->getParameter('cron_alertTo_contacts');
            $alertSubject = 'failed to request SOP API';

            $this->getContainer()->get('send_mail')->sendMails($alertSubject, $alertTo, $content);

            throw new \Exception($content);
        }

        $body = $response->body;

        // no data
        if (count($body) <= 2) {
            return null;
        }

        // csv => hash in array;
        $header = $body[0];
        $rtn_array = array ();
        $data_len = count($body) - 1;
        for ($i = 1; $i < $data_len; $i++) {
            $record = $body[$i];
            $hash_record = array ();
            for ($idx_in_record = 0; $idx_in_record < count($header); $idx_in_record++) {
                $hash_record[$header[$idx_in_record]] = $record[$idx_in_record];
                if ($header[$idx_in_record] == 'extra_info' && $hash_record[$header[$idx_in_record]]) {
                    $hash_record[$header[$idx_in_record]] = json_decode($hash_record[$header[$idx_in_record]], true);
                }
            }
            array_push($rtn_array, $hash_record);
        }

        // validate
        $required_array = $this->requiredFields();
        foreach ($rtn_array as $rec) {
            foreach ($required_array as $key) {
                if (!isset($rec[$key])) {
                    throw new \Exception($key . ' not exist', 1);
                }
                if ($key == 'extra_info') {
                    foreach ($this->extraInfoKeys() as $extra_info_key) {
                        if (!isset($rec['extra_info'][$extra_info_key])) {
                            throw new Exception("extra_info.$extra_info_key not exist", 1);
                        }
                    }
                }
            }
        }
        return $rtn_array;
    }

    abstract protected function point($history);

    protected function extraInfoKeys()
    {
        return array (
            'point',
            'point_type'
        );
    }

    abstract protected function type($history);

    abstract protected function task($history);

    abstract protected function comment($history);

    abstract protected function url();

    abstract protected function requiredFields();

    abstract protected function skipReward($history);

    abstract protected function createParticipationHistory($history);

    protected function log($msg)
    {
        $this->logger->info($msg);
    }

    protected function setLogger($domain)
    {
        $log_dir = $this->getContainer()->getParameter('jili_app.logs_dir');
        $log_dir .= '/' . $domain . '/' . date('Ym/');

        $fs = new Filesystem();
        if (!$fs->exists($log_dir)) {
            $fs->mkdir($log_dir);
        }
        $log_path = $log_dir . date('d') . '.log';

        $stream = new StreamHandler($log_path);
        $logger = new Logger('command');
        $logger->pushHandler($stream, Logger::INFO);
        $this->logger = $logger;
    }
}
