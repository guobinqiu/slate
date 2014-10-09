<?php
namespace Jili\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ChanetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('advertiserment:chanet')
            ->setDescription('verify the advertierment.imageurl')

            ->addOption('updateScriptRedirect',null,InputOption::VALUE_NONE, 'update the is_script_redirect field in table advertiserment.')
            ->setHelp(  <<<EOT
For prod usage:
./app/console advertiserment:chanet -e prod --updateScriptRedirect
EOT
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $env =  $this->getApplication()->getKernel() ->getEnvironment();
        $logger = $this->getContainer()->get('logger');

        $uid= 105; // paranoid user id  
        $checkinAds =  $em->getRepository('JiliApiBundle:CheckinAdverList')->findAll( );
        foreach($checkinAds as $index => $row) {
            $ad_id =  $row->getAdId();
            $ad = $em->getRepository('JiliApiBundle:Advertiserment')->find($ad_id);

            $chanet_url = $ad->getImageurlParsed($uid );
            
            $logger->debug('{jarod}'.implode(':', array(__LINE__, __FILE__, '$chanet_url','')).var_export($chanet_url, true));

        }


        // fetch each row in advertiserment
        // do the request
        // make a http request 
        // check the response 
        $output->writeln('completed');

        return 0;
    }
}
