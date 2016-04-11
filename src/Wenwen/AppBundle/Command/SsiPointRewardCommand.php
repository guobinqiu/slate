<?php

namespace Wenwen\AppBundle\Command;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use VendorIntegration\SSI\PC1\WebService\StatClient;

class SsiPointRewardCommand extends ContainerAwareCommand
{
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
        $definitive = $input->hasOption('definitive');
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
                $ssiRespondentId = self::parseSsiRespondentId($row['sub_id_5']);
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
                $this->getContainer()->get('points_manager')->updatePoints(
                    $user->getId(),
                    $ssiProjectConfig['point'],
                    \Jili\ApiBundle\Entity\AdCategory::ID_QUESTIONNAIRE_COST,
                    \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_SURVEY,
                    sprintf('%s (%s)', $ssiProjectConfig['title'], $date)
                );
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

    public static function parseSsiRespondentId($sub_id_5)
    {
        if (preg_match('/\Awwcn-(\d+)\z/', $sub_id_5, $matches)) {
            return $matches[1];
        }

        return;
    }
}
