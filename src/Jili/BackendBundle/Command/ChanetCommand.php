<?php
namespace Jili\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest;

class ChanetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('advertiserment:chanet')
            ->setDescription('verify the advertierment.imageurl')

            ->addOption('joinCheckinAdverList',null,InputOption::VALUE_NONE, 'update the is_script_redirect field in table advertiserment.')
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

        if( $input->getOption('joinCheckinAdverList')) {
            $ads =  $em->getRepository('JiliApiBundle:Advertiserment')->findAllByCheckinAdverList( );
        } else {
            $ads = $em->getRepository('JiliApiBundle:Advertiserment')->findAll();
        }

        foreach($ads as $index => $row) {
            $output->writeln($row->getId(). ' ' . $row->getTitle());

            $ad = $row;

            $chanet_url = $ad->getImageurlParsed($uid ); 
            $chanetAd = new ChanetHttpRequest($chanet_url);
            $chanetAd->fetch();

        
            $em->getRepository('JiliApiBundle:Advertiserment')->updateByImageUrlResponse();

            $ad->setIsExpired($chanetAd->isExpired());
            if( $chanetAd->isExpired() ) {
                $ad->setIsScriptRedirect(0);
            }  else {
                $ad->setIsScriptRedirectByImageurlResp($chanetAd->getDestinationUrl());
            }
            $em->persist($ad);
            $em->flush();
            $em->clear();

            // $ad = $em->getRepository('JiliApiBundle:Advertiserment')->update($ad_id);
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





