<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushBasicProfileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sop:push_basic_profile');
        $this->setDescription('推送用户基本信息到sop');
        $this->addOption('user_id', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getOption('user_id');
        $result = $this->getContainer()->get('app.survey_service')->pushBasicProfile($userId);
        $output->writeln($result);
    }
}
