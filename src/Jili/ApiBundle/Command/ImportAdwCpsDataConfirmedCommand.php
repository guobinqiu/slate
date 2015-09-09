<?php

namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Jili\ApiBundle\Utility\String;

class ImportAdwCpsDataConfirmedCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('jili:import-adw-cps-data-confirmed')
            ->setDescription('导入已经确认的adw cps数据')
            ->addArgument('csvfile',InputArgument::REQUIRED, 'the confirmed data')
            ->setHelp(  <<<EOT
For prod usage:
./app/console jili:import-adw-cps-data-confirmed -e prod 
EOT
        );
    }

    /**
     * execute 
     * 
     * @param InputInterface $input 
     * @param OutputInterface $output 
     * @access protected
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $env =  $this->getApplication()->getKernel()->getEnvironment();

        /** @var $logger LoggerInterface */
        $logger = $container->get('monolog.logger.import_adw_cps');
        $fs = new Filesystem();
        $csv_file = $input->getArgument('csvfile');

        if( ! $fs->exists($csv_file) ) {
            $output->writeln('<error>文件没找到'.$csv_file.'</error>');
            $logger->warning('文件没找到'.$csv_file);
            return 1;
        }


        $handle = fopen($csv_file,'r');
        $v = fgetcsv($handle); // read the title line
        $i = 2;

        $stats = array(
            'accept_done' => 0,
            'refused_done' => 0,
            'accept_failed' => 0,
            'refused_failed' => 0,
            'no_order'=> 0
        );

        $code = array();
        while($v = fgetcsv($handle)) {
            $i++;

            $logger->info('['. __FUNCTION__.']line in csv file $i:'. $i);
            $status = mb_convert_encoding($v[6],'UTF-8', 'gbk');
            $name = mb_convert_encoding($v[0],'UTF-8','gbk');
            $ocd = trim($v[3], "'");
            $adid = trim($v[7], "'");
            $userId = trim($v[8], "'");

            // 合并后的商家活动， url query string includes: e=uid&u=uid_adid
            $cps_advertisement = false;
            $return = String::parseChanetCallbackUrl($userId, $adid);
            if($return){
                $cps_advertisement = true;
                $userId = $return['user_id'];
                $adid = $return['advertiserment_id'];
            }

            $logger->info( 'user_id: '.$userId . ', adid: ' . $adid. ', ocd: ' . $ocd. ', name: ' . $name. 
                ', status: ' . $status .', isNewAds: ' . ($cps_advertisement ? 'Yes':'No') );

            $adw_order = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($userId,$adid,$ocd);
            $msg = '[ name:'.$name.', userId:'.$userId.', adid:'.$adid.', ocd:'.$ocd.' ] ';
            if(  empty($adw_order)) {
                if($status === $container->getParameter('nothrough')){
                    if(! $container->get('adw_admin.data_confirmed.processor')->noCertified($userId,$adid,$ocd, $cps_advertisement)){
                        $msg .= '插入拒绝数据失败';
                        $output->writeln('<error>'.$msg.'</error>');
                        $code[]  = $msg;
                        $stats['refused_failed']++;
                    } else {
                        $msg .= '插入拒绝数数据成功';
                        $output->writeln('<info>'.$msg.'</info>');
                        $stats['refused_done']++;
                    }
                } else if($status === $container->getParameter('certified')){
                    if(! $container->get('adw_admin.data_confirmed.processor')->hasCertified($userId,$adid,$ocd,$v[5], $cps_advertisement)){
                        $msg .= '插入已认证数据失败';
                        $output->writeln('<error>'.$msg.'</error>');
                        $code[] = $msg;
                        $stats['accept_failed']++;
                    } else{
                        $msg .= '插入已认证数据成功';
                        $output->writeln('<info>'.$msg.'</info>');
                        $stats['accept_done']++;
                    }
                }
            } else {
                $msg .= '插入数据失败,无adwOrder记录';
                $output->writeln('<error>'.$msg.'</error>');
                $stats['no_order']++;
            }
        }

        $body = 'file '. $csv_file . ' import results: '; 
        $body .= PHP_EOL;
        $body .='总计:                '.($i - 2) .  PHP_EOL;
        $body .= PHP_EOL;
        $body .= '插入拒绝数据失败:   '. $stats['refused_failed'].PHP_EOL;
        $body .= '插入已认证数据失败: '. $stats['accept_failed'].PHP_EOL;
        $body .= '插入拒绝数据成功:   '. $stats['refused_done'].PHP_EOL;
        $body .= '插入已认证数据成功: '. $stats['accept_done'].PHP_EOL;
        $body .= '订单状态无效:       '. $stats['no_order'].PHP_EOL;
        $body .= PHP_EOL;
        $body .= implode(PHP_EOL, $code);

        if('prod' === $env) {

            $mailer_user = $this->getContainer()->getParameter('mailer_user');
            $message = new \Swift_Message();
            $message = \Swift_Message::newInstance()
                ->setSubject('成果确认数据导入结果 '. $env)
                ->setFrom(array($mailer_user=> 'Jili Command'))
                ->setTo( $container->getParameter('cron_alertTo_contacts'))
                ->setBody($body);

            $container = $this->getContainer();
            $mailer = $container->get('mailer');
            $mailer->send($message);
        } else {
            $logger->info($body);
        }

        return 0;
    }
}
