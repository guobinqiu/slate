<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

abstract class PanelRewardCommand extends ContainerAwareCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(date('Y-m-d H:i:s') . ' start ' . $this->getName());

        try{
            $date = $input->getArgument('date');
            $definitive = $input->getOption('definitive');
            $resultNotification = $input->getOption('resultNotification');

            $logger = $this->getLogger();
            $logger->info(__METHOD__ . ' START ' . $this->getName() . ' date=' . $date . ' definitive=' . $definitive . ' resultNotification=' . $resultNotification);
            $memoryStart = memory_get_usage();

            // request to sop
            $url = $this->url();
            $auth = $this->getContainer()->getParameter('sop')['auth'];
            $history_list = $this->requestSOP($url, $date, $date, $auth['app_id'], $auth['app_secret']);

            $em = $this->getContainer()->get('doctrine')->getManager();
            $dbh = $em->getConnection();
            $dbh->getConfiguration()->setSQLLogger(null);

            $pointService = $this->getContainer()->get('app.point_service');

            $successMessages = array();
            $skipMessages = array();
            $errorMessages = array();

            $this->preHandle($history_list);

            $msg = sprintf(' %s %s', 'Ready to reward total_count=', count($history_list));
            $logger->info(__METHOD__ . $msg);

            $memoryLast = memory_get_usage();
            $memoryCurrent = $memoryLast;
            //start inserting
            foreach ($history_list as $history) {
                $memoryLast = $memoryCurrent;
                $memoryCurrent = memory_get_usage();

                $em->clear();

                $memoryAfterEmClear = memory_get_usage();
                $logger->debug(__METHOD__ . ' memory Last=' . $memoryLast . ' Current='. $memoryCurrent . ' clear=' . $memoryAfterEmClear . ' ' . ($memoryCurrent - $memoryLast) . ' ' . ($memoryAfterEmClear - $memoryCurrent));

                $survey_id = '';
                if (isset($history['survey_id'])) {
                    $survey_id = $history['survey_id'];
                }

                if ($this->skipReward($history)) {
                    $msg = sprintf(' %s, %s', 'Skip reward, invalid point_type', json_encode($history));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                // get respondent
                $respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneByAppMid($history['app_mid']);
                if (!$respondent) {
                    $msg = sprintf(' %s, %s', 'Skip reward, app_mid not exist', json_encode($history));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                // get panelist
                $user = $em->getRepository('WenwenFrontendBundle:User')->find($respondent->getUserId());
                if (!$user) {
                    // maybe panelist withdrew
                    $msg = sprintf(' %s, %s', 'Skip reward, user not exist', json_encode($history));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                $logger->info(__METHOD__ . ' Start user.point=' . $user->getPoints());

                if ($this->skipRewardAlreadyExisted($history)) {
                    $msg = sprintf(' %s, %s', 'Skip reward, app_mid already rewarded', json_encode($history));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                // transaction start
                $dbh->beginTransaction();

                try {
                    // insert participation history
                    $participationHistory = $this->createParticipationHistory($history);

                    if(TaskType::SURVEY == $this->task($history)){
                        // 更新用户参与商业调查的csq计数
                        $user->updateCSQ($this->answerStatus($history));
                        $logger->debug(__METHOD__ . ' status=' . $this->answerStatus($history). ' c=' . $user->getCompleteN() . ' s=' . $user->getScreenoutN() . ' q=' . $user->getQuotafullN());
                    }



                    // 给当前用户加积分
                    $pointService->addPoints(
                        $user,
                        $this->point($history),
                        $this->type($history),
                        $this->task($history),
                        $this->comment($history),
                        $participationHistory
                    );

                    // 同时给邀请人加积分(10%)
                    $pointService->addPointsForInviter(
                        $user,
                        $this->point($history) * 0.1,
                        CategoryType::EVENT_INVITE_SURVEY,
                        TaskType::RENTENTION,
                        '您的好友' . $user->getNick() . '回答了一份' . $this->getPanelType() . '商业问卷',
                        $participationHistory
                    );

                    // 给奖池注入积分(5%)
                    if (in_array($this->type($history), CategoryType::$cost_types)) {
                        $injectPoints = intval($this->point($history) * 0.05);
                        $this->getContainer()->get('app.prize_service')->addPointBalance($injectPoints);
                    }

                    if($definitive) {
                        //$em->persist();
                        $dbh->commit();

                        $msg = sprintf(' %s, %s', ' Commit   - Point reward success', json_encode($history));
                    } else {
                        $dbh->rollBack();
                        $msg = sprintf(' %s, %s', ' RollBack - Point reward success', json_encode($history));
                    }

                    $logger->info(__METHOD__ . $msg);
                    array_push($successMessages, date('Y-m-d H:i:s') . $msg);

                    $logger->info(__METHOD__ . ' End user.point=' . $user->getPoints());

                } catch (\Exception $e) {
                    $msg = sprintf(' %s, %s', $e->getMessage(), json_encode($history));
                    $logger->error(__METHOD__ . $msg);
                    array_push($errorMessages, date('Y-m-d H:i:s') . $msg);
                    $dbh->rollBack();
                }

            }

            $logger->info(__METHOD__ . ' RESULT total=' . count($history_list) . ' success=' . count($successMessages) . ' skip=' . count($skipMessages) . ' error=' . count($errorMessages));
            $memoryEnd = memory_get_usage();
            $peakMemory = memory_get_peak_usage();
            $logger->debug(__METHOD__ . ' RESULT memory=' . $memoryStart . '/' . $memoryEnd . ' peakMemory=' . $peakMemory);

            if($resultNotification) {
                $logger->info(__METHOD__ . ' Start to notifiy system team.');
                $log = $this->getLog($successMessages, $skipMessages, $errorMessages);
                $subject = 'Report of ' . $this->getPanelType() . ' reward points for ' . $date;
                $numSent = $this->sendLogEmail($log, $subject);
                $logger->info(__METHOD__ . ' End   of notification. Email num sent: ' . $numSent . ' title=' . $subject);
            } else {
                $logger->info(__METHOD__ . ' End without notification.');
            }

            $logger->info(__METHOD__ . ' END   ' . $this->getName() . ' date=' . $date);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' ERROR: ' . $e->getMessage());
            $output->writeln(date('Y-m-d H:i:s') . ' ERROR: ' . $e->getMessage());
        }

        $output->writeln(date('Y-m-d H:i:s') . ' end ' . $this->getName());
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

            throw new \Exception($content);
        }

        $body = $response->body;

        // no data
        if (count($body) <= 2) {
            return [];
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

    abstract protected function getPanelType();

    abstract protected function preHandle(array $history_list);

    protected function sendLogEmail($content, $subject)
    {
        $alertTo = $this->getContainer()->getParameter('cron_alertTo_contacts');
        return $this->getContainer()->get('app.internal_mail_service')->sendMails($subject, $alertTo, $content);
    }

    protected function getLogger()
    {
        $log_dir = $this->getContainer()->getParameter('jili_app.logs_dir');
        $log_dir .= '/reward_point/' . (new \ReflectionClass($this))->getShortName() . '/' . date('Ym');

        $fs = new Filesystem();
        if (!$fs->exists($log_dir)) {
            $fs->mkdir($log_dir);
        }
        $log_path = $log_dir . '/' . date('d') . '.log';

        $stream = new StreamHandler($log_path);
        $logger = new Logger('command');
        $logger->pushHandler($stream);
        return $logger;
    }

    private function getLog($successMessages, $skipMessages, $errorMessages) {
        if(!is_array($successMessages) || !is_array($skipMessages) || !is_array($errorMessages)){
            return "Invalid parameters";
        }
        $success = count($successMessages);
        $skip = count($skipMessages);
        $error = count($errorMessages);

        $data[] = 'ExecuteFinishTime: ' . date('Y-m-d H:i:s');
        $data[] = 'Total: ' . ($success + $skip + $error);
        $data[] = 'Success: ' . $success;
        $data[] = 'Skip: ' . $skip;
        $data[] = 'Error: ' . $error;

        if ($error > 0) {
            $data[] = '----- Error details -----';
            foreach($errorMessages as $i => $msg) {
                $data[] = sprintf('%s, %s', $i + 1, $msg);
            }
        }

        if ($skip > 0) {
            $data[] = '----- Skip details -----';
            foreach($skipMessages as $i => $msg) {
                $data[] = sprintf('%s, %s', $i + 1, $msg);
            }
        }

        if ($success > 0) {
            $data[] = '----- Success details -----';
            foreach($successMessages as $i => $msg) {
                $data[] = sprintf('%s, %s', $i + 1, $msg);
            }
        }

        return implode("<br>", $data);
    }
}
