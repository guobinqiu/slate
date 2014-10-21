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
            $chanetAd = $this->request($chanet_url);
            $is_expired = $chanetAd->isExpired();
            $ad->setIsExpired($is_expired);
            $em->flush();

        }

        $output->writeln('completed');
        return 0;
    }

    /**
     *
     */
    protected function request($chanet_url)
    {
        $chanetAd = new ChanetHttpRequest($chanet_url);
        $chanetAd->fetch();
        return $chanetAd;
    }
}





