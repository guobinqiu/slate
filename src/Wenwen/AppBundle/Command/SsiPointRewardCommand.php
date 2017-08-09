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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(date('Y-m-d H:i:s') . ' start ' . $this->getName());

        $date = $input->getArgument('date');

        $logger = $this->getLogger();
        $logger->info(__METHOD__ . ' START ' . $this->getName() . ' date=' . $date);

        $client = new StatClient($this->getContainer()->getParameter('ssi_project_survey_code')['api_key']);
        $iterator = $this->getContainer()->get('ssi_api.conversion_report_iterator');
        $iterator->initialize($client, $date);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();

        // flags
        $successMessages = array();
        $errorMessages = array();

        $ssiProjectConfig = $this->getContainer()->getParameter('ssi_project_survey');

        $rows = 0;
        while ($row = $iterator->nextConversion()) {
            $start = time();
            $rows += 1;

            $ssiRespondentId = SsiRespondent::parseRespondentId($row['sub_id_5']);
            $ssiRespondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->find($ssiRespondentId);
            if (!$ssiRespondent) {
                $info = "Skip reward, SsiRespondent (Id: $ssiRespondentId) not found";
                array_push($successMessages, sprintf('%s, %s, %s', '', $ssiProjectConfig['point'], $info));
                $logger->info(sprintf('%s, %s, %s', '', $ssiProjectConfig['point'], $info));
                continue;
            }

            $userId = $ssiRespondent->getUserId();
            $user = $em->getRepository('WenwenFrontendBundle:User')->find($userId);
            if (!$user) {
                $info = "Skip reward, User (Id: $userId) not found.";
                array_push($successMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $logger->info(sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
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
                $info = 'Skip reward, already exist, skip transaction_id : ' . $row['transaction_id'];
                array_push($successMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $logger->info(sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
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
                $info = 'Success';
                array_push($successMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $logger->info(sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));

                $dbh->commit();

            } catch (\Exception $e) {
                $info = $e->getMessage();
                array_push($errorMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $logger->error(sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $dbh->rollBack();
            }
        } // end while

        $logger->info(__METHOD__ . ' RESULT total_count=' . $rows . ' success_count=' . count($successMessages) . ' error_count=' . count($errorMessages));

        $log = $this->getLog($successMessages, $errorMessages);
        $subject = 'Report of SSI reward points';
        $numSent = $this->sendLogEmail($log, $subject);
        $logger->info('Email num sent: ' . $numSent);

        $logger->info(__METHOD__ . ' END   ' . $this->getName() . ' date=' . $date);

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

    private function getLog(array $successMessages, array $errorMessages) {
        $success = count($successMessages);
        $error = count($errorMessages);

        $data[] = 'Date: ' . date('Y-m-d H:i:s');
        $data[] = 'Total: ' . ($success + $error);
        $data[] = 'Success: ' . $success;
        $data[] = 'Error: ' . $error;

        if ($error > 0) {
            $data[] = '----- Error details -----';
            $data[] = 'id, user_id, points, error';
            foreach($errorMessages as $i => $msg) {
                $data[] = sprintf('%s, %s', $i + 1, $msg);
            }
        }

        if ($success > 0) {
            $data[] = '----- Success details -----';
            $data[] = 'id, user_id, points, info';
            foreach($successMessages as $i => $msg) {
                $data[] = sprintf('%s, %s', $i + 1, $msg);
            }
        }

        return implode("<br>", $data);
    }
}
