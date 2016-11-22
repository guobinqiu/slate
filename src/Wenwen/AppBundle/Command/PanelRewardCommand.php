<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

abstract class PanelRewardCommand extends ContainerAwareCommand
{
    protected $logger;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getArgument('date');;

        // configs
        $url = $this->url();
        $auth = $this->sop_configure['auth'];
        $history_list = $this->requestSOP($url, $date, $date, $auth['app_id'], $auth['app_secret']);
        $this->logger->info(count($history_list));
        $this->logger->info(var_export($history_list, true));


        // initialize the database connection
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();
        $dbh->getConfiguration()->setSQLLogger(null);

        $hasErrors = false;

        $successMessages = array();
        $success = 0;

        $errorMessages = array();
        $error = 0;

        //start inserting
        foreach ($history_list as $history) {
            $survey_id = null;
            if (isset($history['survey_id'])) {
                $survey_id = $history['survey_id'];
            }

            if ($this->skipReward($history)) {
                $info = 'Skip reward';
                array_push($successMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $info));
                $success += 1;
                continue;
            }

            if ($this->skipRewardAlreadyExisted($history)) {
                $info = 'Skip reward, already existed: app_mid: ' . $history['app_mid'];
                array_push($successMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $info));
                $success += 1;
                continue;
            }

            // get respondent
            $respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(array (
                'id' => $history['app_mid']
            ));
            if (!$respondent) {
                $info = 'Skip reward, No SopRespondent for: ' . $history['app_mid'];
                array_push($successMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $info));
                $success += 1;
                continue;
            }

            // get panelist
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array (
                'id' => $respondent->getUserId()
            ));
            if (!$user) {
                // maybe panelist withdrew
                $info = 'Skip reward, No User. Skip user_id: ' . $respondent->getUserId();
                array_push($successMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $info));
                $success += 1;
                continue;
            }

            // transaction start
            $dbh->beginTransaction();

            try {
                // insert participation history
                $this->createParticipationHistory($history);

                $pointService = $this->getContainer()->get('app.point_service');

                // 给当前用户加积分
                $pointService->addPoints(
                    $user,
                    $this->point($history),
                    $this->type($history),
                    $this->task($history),
                    $this->comment($history)
                );

                // 同时给邀请人加积分(10%)
                $pointService->addPointsForInviter(
                    $user,
                    $this->point($history) * 0.1,
                    CategoryType::EVENT_INVITE_SURVEY,
                    TaskType::RENTENTION,
                    '您的好友' . $user->getNick() . '回答了一份' . $this->getVendorName() . '商业问卷'
                );

                $dbh->commit();

            } catch (\Exception $e) {
                array_push($errorMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $e->getMessage()));
                $error += 1;
                $hasErrors = true;
                $dbh->rollBack();
            }

            if (!$hasErrors) {
                $info = null;
                if (in_array($this->type($history), CategoryType::$cost_types)) {
                    // 给奖池注入积分(5%)
                    $injectPoints = intval($this->point($history) * 0.05);
                    $this->getContainer()->get('app.prize_service')->addPointBalance($injectPoints);
                    $info = '给奖池注入积分' . $injectPoints;
                }
                array_push($successMessages, sprintf('%s, %s, %s, %s', $survey_id, $history['app_mid'], $this->point($history), $info));
                $success += 1;
            }
        } // end for

        $content = 'Date: ' . date('Y-m-d H:i:s');
        $content .= '<br/>Total: ' . count($history_list);
        $content .= '<br/>Success: ' . $success;
        $content .= '<br/>Error:' . $error;
        if ($error > 0) {
            $content .= '<br/>----- Error details -----';
            $content .= '<br/>id, survey_id, app_mid, points, error';
            foreach($errorMessages as $i => $errorMessage) {
                $content .= '<br/>' . sprintf('%s, %s', $i + 1, $errorMessage);
            }
        }
        if ($success > 0) {
            $content .= '<br/>----- Success details -----';
            $content .= '<br/>id, survey_id, app_mid, points, info';
            foreach($successMessages as $i => $successMessage) {
                $content .= '<br/>' . sprintf('%s, %s', $i + 1, $successMessage);
            }
        }
        $subject = 'Report of panel ['. $this->getVendorName() .'] reward points';
        $this->notice($content, $subject);

        $this->logger->info("memory_get_usage: " .round(memory_get_usage() / 1024 / 1024, 2) . 'MB');
        $this->logger->info("memory_get_peak_usage: " .round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB');
        $this->logger->info('Finish executing');
        $output->writeln('end panel:reward-point: '.date('Y-m-d H:i:s'));
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
            $this->logger->error($content);

            //notice
            $content = $content . '<br>request URL:' . $url;
            $subject = 'failed to request SOP API';
            $this->notice($content, $subject);

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
                            throw new \Exception("extra_info.$extra_info_key not exist", 1);
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

    abstract protected function skipRewardAlreadyExisted($history);

    abstract protected function createParticipationHistory($history);

    abstract protected function getVendorName();

    protected function notice($content, $subject)
    {
        // slack notice
        $this->getContainer()->get('alert_to_slack')->sendAlertToSlack($content);

        //emai notice
        $alertTo = $this->getContainer()->getParameter('cron_alertTo_contacts');
        $this->getContainer()->get('send_mail')->sendMails($subject, $alertTo, $content);
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
