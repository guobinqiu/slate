<?php
namespace Jili\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Jili\BackendBundle\Utility\PointsPool;
use Symfony\Component\Filesystem\Filesystem;

class GameEggsBreakerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('game:eggsBreaker')
            ->setDescription('关联阿里妈妈的订单有效状态持续7天后，认定该订单为有效订单→[订单]有效 valid')
            ->addOption('pool-alarm',null,InputOption::VALUE_NONE,'奖池报警')
            ->addOption('finish-orders-audit',null,InputOption::VALUE_NONE,'完成认定该订单是否为有效订单')
            ->addOption('refresh-ranking',null,InputOption::VALUE_NONE,'更新最近得到金蛋的用户缓存文件'
            )
            ->addArgument('duration',InputArgument::OPTIONAL,'持续天数，default 0天')
            ->setHelp(<<<EOT
For prod usage:
./app/console game:eggsBreaker -e prod --finish-orders-audit --duration 7
EOT
);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

$container = $this->getContainer();
        $em = $container ->get('doctrine')->getManager();
        $env =  $this->getApplication()->getKernel() ->getEnvironment();
        $logger = $this->getContainer()->get('logger');

        if( $input->getOption('finish-orders-audit')) {
            $duration= $input->getArgument('duration');
            $this->getContainer()->get('december_activity.game_eggs_breaker')
                ->finishAudit($duration);
               ;
//todo: add to service

            $output->writeln('checkint current eggs sent');

        } else if( $input->getOption('refresh-ranking')) {
        } else if( $input->getOption('pool-alarm')) {
            

            try {
                $sql = 'select sum(num_of_common) as "common", sum(num_of_consolation) as  "consolation" from game_eggs_breaker_eggs_info;';
                $numbers =  $em->getConnection()->query($sql)->fetchAll();

                $num_common = (int ) $numbers[0]['common'];
                $num_consolation = (int) $numbers[0]['consolation'];


                $configs = $container->getParameter('game_eggs_breaker');

                $pointsPoolCommon = new PointsPool( $configs['common']['points_pool'],$configs['common']['points_strategy'] , false);
                $pointsPoolConsolation = new PointsPool( $configs['consolation']['points_pool'],$configs['consolation']['points_strategy'] , false);

                $file_common = $pointsPoolCommon->getPointsPoolFile();

                $pool_common = $pointsPoolCommon->readCached($file_common);
                $body = '';

                if( is_array($pool_common ) ){
                    $common_pool_status =  '普通奖池中金蛋数为: ' .count($pool_common);

                } else {
                    $common_pool_status =  '普通奖池为满(未开砸)';//.PHP_EOL.$file_common;
                }

                $body .= '用户持有的普通金蛋数为: '.$num_common.PHP_EOL;
                $body .= $common_pool_status.PHP_EOL;

                $file_consolation =$pointsPoolConsolation->getPointsPoolFile();
                $pool_consolation = $pointsPoolConsolation->readCached($file_consolation);
                if( is_array($pool_consolation  ) ){
                    $consolation_pool_status =  '安慰奖池中金蛋数为: ' .count($pool_consolation );
                } else {
                    $consolation_pool_status =  '安慰奖池当前为满(未开砸).';//.PHP_EOL.$file_consolation;
                }

                $body .= '用户持有的安慰蛋数为: '.$num_consolation.PHP_EOL;
                $body .= $consolation_pool_status.PHP_EOL; 
                // TODO: move the title & recipients to config_.yml 
                if( $env !== 'prod' ) {
                    $title = '[235上的测试][test]'; 
                    $recipients  = 'chiang_32@126.com';
                } else {
                    $title ='';
                    $recipients = 'vctech-system@voyagegroup.info';
                }

                $title .='积粒网-砸金蛋活动状态报告';

                $mailer = $this->getContainer()->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject($title )
                    ->setFrom($container ->getParameter('mailer_user') )
                    ->setTo($recipients )
                    ->setBody($body);
                $result =  $mailer->send($message);

                $output->writeln((int)$result . ' email sent');
            } catch ( \Exception $e) {
            //    echo $e->getMessage(),PHP_EOL;
                $logger->crit('[command][gameEggBreaker][statReport]'.$e->getMessage());
            }

        } else {
            $output->writeln( 'no options');
        }
    }
    
}
