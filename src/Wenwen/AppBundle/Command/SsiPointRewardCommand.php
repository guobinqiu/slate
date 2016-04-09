<?php

namespace Wenwen\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
}
