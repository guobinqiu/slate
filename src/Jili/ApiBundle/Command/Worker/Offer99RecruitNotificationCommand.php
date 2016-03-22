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

class Offer99RecruitNotificationCommand extends ContainerAwareCommand
{
    private $alertTo;
    private $alertSubject;
    
    protected function configure()
    {
        $this->setName('recruit-notification:offer99')
            ->setDescription('offer99 ')
            ->addOption('user_id',null, InputOption::VALUE_REQUIRED,'user id ')
            ->addOption('tid', null, InputOption::VALUE_REQUIRED, 'tid if from offer99' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer(); 

        $timeout=  $input->getOption('tid');
        $queueName= $input->getOption('userid');

        $logger=$container->get('logger');


//      $container->get();

    }

    public function makeSignature($tid, $uid, $ad_key ) 
    {
        return  md5(join('', $tid, $uid, $ad_key));
    }
    
}

