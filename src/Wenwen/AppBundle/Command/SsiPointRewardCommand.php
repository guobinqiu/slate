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
        $output->writeln('start panel:reward-ssi-point...');

        $env = $this->getContainer()->get('kernel')->getEnvironment();
        $date = $input->getArgument('date');
        $definitive = $input->getOption('definitive');
        $this->setLogger($this->getName());

        $this->logger->info('Start executing');
        $this->logger->info('    definitive= ' . ($definitive ? 'true' : 'false'));
        $this->logger->info('    date=' . $date);

        $client = new StatClient($this->getContainer()->getParameter('ssi_project_survey_code')['api_key']);
        $iterator = $this->getContainer()->get('ssi_api.conversion_report_iterator');
        $iterator->initialize($client, $date);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();

        $notice_flag = false;

        $ssiProjectConfig = $this->getContainer()->getParameter('ssi_project_survey');
        while ($row = $iterator->nextConversion()) {

            $this->logger->info('transaction_id: ' . $row['transaction_id']);
            $this->logger->info('date_time: ' . $row['date_time']);

            $ssiRespondentId = \Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId($row['sub_id_5']);
            $ssiRespondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneById($ssiRespondentId);
            if (!$ssiRespondent) {
                $this->logger->info("SsiRespondent (Id: $ssiRespondentId) not found");
                continue;
            }

            $userId = $ssiRespondent->getUserId();
            $user = $em->getRepository('JiliApiBundle:User')->findOneById($userId);
            if (!$user) {
                $this->logger->info("User (Id: $userId) not found.");
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
                $this->logger->info('already exist, skip transaction_id : ' . $row['transaction_id']);
                continue;
            }

            $dbh->beginTransaction();

            try {
                $this->getContainer()->get('points_manager')->updatePoints(
                    $user->getId(),
                    $ssiProjectConfig['point'],
                    \Jili\ApiBundle\Entity\AdCategory::ID_QUESTIONNAIRE_COST,
                    \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_SURVEY,
                    sprintf('%s (%s)', $ssiProjectConfig['title'], $dt->format('Y-m-d'))
                );

                $this->recordParticipationHistory($ssiRespondent, $row);

            } catch (\Exception $e) {
                $this->logger->info('rollBack: ' . $e->getMessage());
                $notice_flag = true;
                $dbh->rollBack();
                throw $e;
            }

            if ($definitive) {
                $this->logger->info('definitive true: commit');
                $dbh->commit();
            } else {
                $this->logger->info('definitive false: rollBack');
                $dbh->rollBack();
            }
        }

        if ($notice_flag) {
            $content = date('Y-m-d H:i:s');
            $subject = 'Panel reward ssi survey point fail, please check log';
            $this->notice($content, $subject);
        }

        $this->logger->info('Finish executing');
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
    }
}
