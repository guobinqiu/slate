<?php
namespace Jili\ApiBundle\Command\Worker;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResearchSurveyDeliveryNotificationCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('research_survey:delivery_notification')
            ->setDescription('research survey delivery notification')
            ->addOption('campaign_id', null , InputOption::VALUE_REQUIRED, 'dmdelivery soap api campaignId'  )
            ->addOption('group_name', null , InputOption::VALUE_REQUIRED, 'the group name in campaign, create when not exists ' )
            ->addOption('mailing_id', null , InputOption::VALUE_REQUIRED, 'dmdelivery soap api mailingId'  )
            ->addArgument('recipients', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'recipients arguments '  )
            ->setHelp(  <<<EOT
For prod usage:
./app/console research_survey:delivery_notification -e prod --campaign_id=23 --group_name=tmp_xxxx --mailing_id=90004  eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3IrOThAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3RfOTgiLCJzdXJ2ZXlfcG9pbnQiOjEwMX0=

--recipients the recipeints array is encoded by Jili\ApiBundle\Tests\Utility\String::encodeForCommandArgument(). Mulitple recipeints encoded string is separated by space.

EOT
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start DeliveryNotificationCommand...');
        $container  = $this->getContainer();
        $env = $input->getOption('env');

        $logger=$container->get('logger');

        $campaignId = $input->getOption('campaign_id');
        $mailingId = $input->getOption('mailing_id');
        $groupName = $input->getOption('group_name');

        $recipients = $input->getArgument('recipients');
        $recipients = $recipients[0];

        $pasrsed = array();

       $recipients =  explode(' ',$recipients);
        foreach($recipients as $recipient_str ) {
            $pasrsed [] = json_decode( base64_decode($recipient_str), true);
        }

        $recipients =  $pasrsed ;
        unset($pasrsed);

        $logger->info('parsed ArgvInput:'. var_export($recipients, true) );

        if( ! isset($campaignId) || ! isset($groupName)  || ! isset($mailingId) || empty($recipients) ) {

            $msg = '$campaignId:'.var_export($campaignId,true) ."\n".
                '$mailingId:'.var_export($mailingId,true)."\n".
                '$groupName:'.var_export($groupName,true)."\n".
                '$recipients:'. var_export($recipients, true);

            $logger->info($msg);
            return ;
        }

        if( $env != 'prod') {
            $msg = 'not prod env';
            $logger->info($msg);
        }

        $delivery_service  = $container->get('webpower.91wenwen.mailer'); # platform: message
        $delivery_service->setCampaignId( $campaignId )
            ->setMailingId( $mailingId )
            ->setGroup(array('name'=> $groupName, 'is_test'=> ( $env !== 'prod') ? true : false  ));

        $return = $delivery_service->sendMailing($recipients);
        $logger->info( $return );
    }
}
