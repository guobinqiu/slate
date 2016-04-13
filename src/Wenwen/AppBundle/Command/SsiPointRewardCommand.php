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
      ->addOption('date', null, InputOption::VALUE_REQUIRED, 'conversion-date', date('Y-m-d', strtotime('2 days ago')))
      ->addOption('definitive', null, InputOption::VALUE_NONE, 'If set, the task will operate on db')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getContainer()->get('kernel')->getEnvironment();
        $date = $input->getOption('date');
        $definitive = $input->getOption('definitive');
        $this->setLogger($this->getName());

        $client = new StatClient($this->getContainer()->getParameter('ssi_project_survey_code'));
        $iterator = $this->getContainer()->get('ssi_api.conversion_report_iterator');
        $iterator->initialize($client, $date);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $dbh = $em->getConnection();
        $dbh->beginTransaction();

        $ssiProjectConfig = $this->getContainer()->getParameter('ssi_project_survey');
        try {
            while ($row = $iterator->nextConversion()) {
                $ssiRespondentId = \Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId($row['sub_id_5']);
                $ssiRespondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneById($ssiRespondentId);
                if (!$ssiRespondent) {
                    $this->logger->info("SsiRespondent (Id: $ssiRespondentId) not found");
                    continue;
                }

                $userId = $ssiRespondent->getUserId();
                $user = $em->getRepository('JiliApiBundle:User')->findOneById($userId);
                if (!$ssiRespondent) {
                    $this->logger->info("User (Id: $userId) not found.");
                    continue;
                }

                $dt = new \DateTime(
                  DateUtil::convertTimeZone($row['date_time'], self::REPORT_TIME_ZONE, self::REWARD_TIME_ZONE)
                );

                $this->getContainer()->get('points_manager')->updatePoints(
                    $user->getId(),
                    $ssiProjectConfig['point'],
                    \Jili\ApiBundle\Entity\AdCategory::ID_QUESTIONNAIRE_COST,
                    \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_SURVEY,
                    sprintf('%s (%s)', $ssiProjectConfig['title'], $dt->format('Y-m-d'))
                );

                $this->recordParticipationHistory($ssiRespondent, $row);
            }
        } catch (\Exception $e) {
            $dbh->rollBack();
            throw $e;
        }

        if ($definitive) {
            $dbh->commit();
        } else {
            $dbh->rollBack();
        }
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
