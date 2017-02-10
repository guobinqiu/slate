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
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

class SsiPointRewardCommand extends ContainerAwareCommand
{
    const REPORT_TIME_ZONE = 'EST';
    const REWARD_TIME_ZONE = 'Asia/Shanghai';

    protected $logger;

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
        $output->writeln('start panel:reward-ssi-point: '.date('Y-m-d H:i:s'));

        $date = $input->getArgument('date');
        $this->setLogger($this->getName());

        $client = new StatClient($this->getContainer()->getParameter('ssi_project_survey_code')['api_key']);
        $iterator = $this->getContainer()->get('ssi_api.conversion_report_iterator');
        $iterator->initialize($client, $date);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();

        $hasErrors = false;

        $successMessages = array();
        $success = 0;

        $errorMessages = array();
        $error = 0;

        $ssiProjectConfig = $this->getContainer()->getParameter('ssi_project_survey');

        $rows = 0;
        while ($row = $iterator->nextConversion()) {
            $rows += 1;

            $ssiRespondentId = \Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId($row['sub_id_5']);
            $ssiRespondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneById($ssiRespondentId);
            if (!$ssiRespondent) {
                $info = "Skip reward, SsiRespondent (Id: $ssiRespondentId) not found";
                array_push($successMessages, sprintf('%s, %s, %s', null, $ssiProjectConfig['point'], $info));
                $success += 1;
                continue;
            }

            $userId = $ssiRespondent->getUserId();
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
            if (!$user) {
                $info = "Skip reward, User (Id: $userId) not found.";
                array_push($successMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $success += 1;
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
                $success += 1;
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

                $dbh->commit();

            } catch (\Exception $e) {
                array_push($errorMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $e->getMessage()));
                $error += 1;
                $hasErrors = true;
                $dbh->rollBack();
            }

            if (!$hasErrors) {
                // 给奖池注入积分(5%)
                $injectPoints = intval($ssiProjectConfig['point'] * 0.05);
                $this->getContainer()->get('app.prize_service')->addPointBalance($injectPoints);
                $info = '给奖池注入积分' . $injectPoints;
                array_push($successMessages, sprintf('%s, %s, %s', $userId, $ssiProjectConfig['point'], $info));
                $success += 1;
            }
        } // end while

        $content = 'Date: ' . date('Y-m-d H:i:s');
        $content .= '<br/>Total: ' . $rows;
        $content .= '<br/>Success: ' . $success;
        $content .= '<br/>Error:' . $error;
        if ($error > 0) {
            $content .= '<br/>----- Error details -----';
            $content .= '<br/>id, user_id, points, error';
            foreach($errorMessages as $i => $errorMessage) {
                $content .= '<br/>' . sprintf('%s, %s', $i + 1, $errorMessage);
            }
        }
        if ($success > 0) {
            $content .= '<br/>----- Success details -----';
            $content .= '<br/>id, user_id, points, info';
            foreach($successMessages as $i => $successMessage) {
                $content .= '<br/>' . sprintf('%s, %s', $i + 1, $successMessage);
            }
        }
        $subject = 'Report of panel SSI reward points';
        $this->notice($content, $subject);

        $this->logger->info('Finish executing');
        $output->writeln('end panel:reward-ssi-point: '.date('Y-m-d H:i:s'));
    }

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
        $log_dir .= '/'.$domain.'/'.date('Ym/');
        $fs = new Filesystem();
        if (true !== $fs->exists($log_dir)) {
            $fs->mkdir($log_dir);
        }
        $log_path = $log_dir.date('d').'.log';

        $stream = new StreamHandler($log_path);
        $logger = new Logger('command');
        $logger->pushHandler($stream, Logger::INFO);
        $this->logger = $logger;
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
}
