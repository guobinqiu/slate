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

class JmsDemoCommand extends ContainerAwareCommand
{
    private $alertTo;
    private $alertSubject;
    
    protected function configure()
    {
        $this->setName('jms:demo')
            ->setDescription('this is a demo testing')
            ->addOption('timeout', 't', InputOption::VALUE_REQUIRED, 'seconds to sleep', 10 )
            ->addOption('queue-name', 'Q', InputOption::VALUE_REQUIRED, 'the queue name' ,'default' );
    
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer(); 

        $timeout=  $input->getOption('timeout');
        $queueName= $input->getOption('queue-name');
        $logger=$container->get('logger');
        $msg = 'sleep '.$timeout.'s in queue '.$queueName;
        $logger->info($msg);

        print $msg;
        sleep($timeout);

    }
    
}
