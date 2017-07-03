<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

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
        $em = $this->getContainer()->get('doctrine')->getManager();
        $surveyService = $this->getContainer()->get('app.survey_service');
        $pointService = $this->getContainer()->get('app.point_service');
        $parameterService = $this->getContainer()->get('app.parameter_service');

        $user_id = $input->getOption('user_id');
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $success = $surveyService->pushBasicProfile($user);
        $output->writeln($success);
    }
}
