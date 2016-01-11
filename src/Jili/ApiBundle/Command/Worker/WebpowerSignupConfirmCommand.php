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

class WebpowerSignupConfirmCommand extends ContainerAwareCommand
{
    private $alertTo;
    private $alertSubject;
    
    protected function configure()
    {
        $this->setName('webpower-mailer:signup-confirm')
            ->setDescription('sending the mail by webpower delievery soap')
            ->addOption('campaign_id', null , InputOption::VALUE_REQUIRED, 'dmdelivery soap api campaignId'  )
            ->addOption('group_id', null , InputOption::VALUE_REQUIRED, 'dmdelivery soap api groupId'  )
            ->addOption('mailing_id', null , InputOption::VALUE_REQUIRED, 'dmdelivery soap api mailingId'  )
            ->addOption('email', null , InputOption::VALUE_REQUIRED, 'recipient email'  )
            ->addOption('name', null , InputOption::VALUE_REQUIRED, 'recipient name'  )
            ->addOption('title', null , InputOption::VALUE_REQUIRED, 'recipient title'  )
            ->addOption('register_key', null , InputOption::VALUE_REQUIRED, 'recipient register_key'  )
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container  = $this->getContainer(); 
        $env = $input->getOption('env');

        $logger=$container->get('logger');

        $campaignId = $input->getOption('campaign_id');
        $groupId = $input->getOption('group_id');
        $mailingId = $input->getOption('mailing_id');
        $email = $input->getOption('email');
        $name = $input->getOption('name');
        $title = $input->getOption('title');
        $regiterKey = $input->getOption('register_key');

        $delivery_service  = $container->get('webpower.91wenwen_signup.mailer');

        if( $env != 'prod') {
            $msg = 'campaign_id:'.$campaignId. ',group_id:'.$groupId.',mailing_id:'.$mailingId ;
            $msg .= ',email:'.$email. ',name:'.$name.',title:'.$title.',register_key:'.$regiterKey;
            $logger->info($msg);
        }

        $delivery_service->setCampaignId( $campaignId )
            ->setGroupId( $groupId )
            ->setMailingId( $mailingId );
        $return = $delivery_service->singleEmail(  array('email'=>$email, 'name'=>$name,'title'=>$title,'register_key'=>$regiterKey ) );
        $logger->info( $return );
    }
}
