<?php
namespace Jili\ApiBundle\Command\Worker;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Psr\Log\LoggerInterface;

class OfferwowRecruitNotificationCommand extends ContainerAwareCommand
{
    private $alertTo;
    private $alertSubject;
    
    protected function configure()
    {
        $this->setName('recruit-notification:offerwow')
            ->setDescription('offer99 ')
            ->addOption('user_id',null, InputOption::VALUE_REQUIRED,'user id ')
            ->addOption('txid', null, InputOption::VALUE_REQUIRED, 'tid if from offerwow' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer(); 

        $tracking = $input->getOption('user_id');
        $txid = $input->getOption('txid');

        $logger=$container->get('logger');
    }
    
}

