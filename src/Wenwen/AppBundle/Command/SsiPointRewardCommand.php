<?php

namespace Wenwen\AppBundle\Command;

use Jili\ApiBundle\Utility\DateUtil;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use VendorIntegration\SSI\PC1\WebService\StatClient;
use Wenwen\AppBundle\Entity\SsiRespondent;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

class SsiPointRewardCommand extends ContainerAwareCommand
{
    const REPORT_TIME_ZONE = 'EST';
    const REWARD_TIME_ZONE = 'Asia/Shanghai';

    protected function configure()
    {
        $this
          ->setName('panel:reward-ssi-point')
          ->setDescription('Reward Point for SSI API conversion')
          ->addArgument('date', null, InputOption::VALUE_REQUIRED, 'conversion-date', date('Y-m-d', strtotime('2 days ago')))
          ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db')
          ->addOption('resultNotification', null, InputOption::VALUE_NONE, 'If set, the task will send a notification to system team');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(date('Y-m-d H:i:s') . ' start ' . $this->getName());

        try{
            $date = $input->getArgument('date');
            $definitive = $input->getOption('definitive');
            $resultNotification = $input->getOption('resultNotification');

            $logger = $this->getLogger();
            $logger->info(__METHOD__ . ' START ' . $this->getName() . ' date=' . $date);

            $client = new StatClient($this->getContainer()->getParameter('ssi_project_survey_code')['api_key']);
            $iterator = $this->getContainer()->get('ssi_api.conversion_report_iterator');
            $iterator->initialize($client, $date);

            $em = $this->getContainer()->get('doctrine')->getManager();
            $dbh = $em->getConnection();

            // flags
            $successMessages = array();
            $skipMessages = array();
            $errorMessages = array();

            $ssiProjectConfig = $this->getContainer()->getParameter('ssi_project_survey');

            $rows = 0;
            while ($row = $iterator->nextConversion()) {
                $start = time();
                $rows += 1;

                $ssiRespondentId = SsiRespondent::parseRespondentId($row['sub_id_5']);
                $ssiRespondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneById($ssiRespondentId);
                if (!$ssiRespondent) {
                    $msg = sprintf(' Skip reward, SsiRespondent (Id: %s) not exist. %s', $ssiRespondentId, json_encode($row));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                $userId = $ssiRespondent->getUserId();
                $user = $em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
                if (!$user) {
                    $msg = sprintf(' Skip reward, User (Id: %s) not found.  %s', $userId, json_encode($row));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                $dt = new \DateTime(
                    DateUtil::convertTimeZone($row['date_time'], self::REPORT_TIME_ZONE, self::REWARD_TIME_ZONE)
                );

                // check SsiProjectParticipationHistory exist
                $records = $em->getRepository('WenwenAppBundle:SsiProjectParticipationHistory')->findBy(array (
                    'completedAt' => $dt,
                    'transactionId' => $row['transaction_id']
                ));
                if (count($records) > 0) {
                    $msg = sprintf(' Skip reward, already exist, skip transaction_id : %s.  %s', $row['transaction_id'], json_encode($row));
                    $logger->warn(__METHOD__ . $msg);
                    array_push($skipMessages, date('Y-m-d H:i:s') . $msg);
                    continue;
                }

                $dbh->beginTransaction();

                try {
                    $history = $this->recordParticipationHistory($ssiRespondent, $row);

                    $pointService = $this->getContainer()->get('app.point_service');

                    // 给当前用户加积分
                    $pointService->addPoints(
                        $user,
                        $ssiProjectConfig['point'],
                        CategoryType::SSI_COST,
                        TaskType::SURVEY,
                        sprintf('%s (%s)', $ssiProjectConfig['title'], $dt->format('Y-m-d')),
                        $history
                    );

                    // 同时给邀请人加积分(10%)
                    $pointService->addPointsForInviter(
                        $user,
                        $ssiProjectConfig['point'] * 0.1,
                        CategoryType::EVENT_INVITE_SURVEY,
                        TaskType::RENTENTION,
                        '您的好友' . $user->getNick() . '回答了一份SSI商业问卷',
                        $history
                    );

                    // 给奖池注入积分(5%)
                    $injectPoints = intval($ssiProjectConfig['point'] * 0.05);
                    $this->getContainer()->get('app.prize_service')->addPointBalance($injectPoints);

                    if($definitive) {
                        $dbh->commit();
                        $msg = sprintf(' Commit   - rewarded point=%s user_id=%s %s', $ssiProjectConfig['point'], $userId, json_encode($row));
                    } else {
                        $dbh->rollBack();
                        $msg = sprintf(' RollBack - rewarded point=%s user_id=%s %s', $ssiProjectConfig['point'], $userId, json_encode($row));
                    }

                    array_push($successMessages, date('Y-m-d H:i:s') . $msg);
                    $logger->info(__METHOD__ . $msg);

                } catch (\Exception $e) {
                    $msg = sprintf(' %s, %s', $e->getMessage(), json_encode($history));
                    $logger->error(__METHOD__ . $msg);
                    array_push($errorMessages, date('Y-m-d H:i:s') . $msg);
                    $dbh->rollBack();
                }
            } // end while

            $logger->info(__METHOD__ . ' RESULT total=' . $rows . ' success=' . count($successMessages) . ' skip=' . count($skipMessages) . ' error=' . count($errorMessages));

            if($resultNotification){
                $log = $this->getLog($successMessages, $skipMessages, $errorMessages);
                $subject = 'Report of SSI reward points for ' . $date;
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
        if (true !== $fs->exists($log_dir)) {
            $fs->mkdir($log_dir);
        }
        $log_path = $log_dir . '/' . date('d') . '.log';

        $stream = new StreamHandler($log_path);
        $logger = new Logger('command');
        $logger->pushHandler($stream);
        return $logger;
    }

    public function recordParticipationHistory($ssiRespondent, $row)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dt = new \DateTime(DateUtil::convertTimeZone($row['date_time'], self::REPORT_TIME_ZONE, self::REWARD_TIME_ZONE));
        $history = new \Wenwen\AppBundle\Entity\SsiProjectParticipationHistory();
        $history->setSsiRespondentId($ssiRespondent->getId());
        $history->setTransactionId($row['transaction_id']);
        $history->setCompletedAt($dt);
        $em->persist($history);
        $em->flush();
        return $history;
    }

    protected function preHandle(array $history_list) {
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
