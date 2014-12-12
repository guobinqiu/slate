<?php
namespace Jili\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Component\Filesystem\Filesystem;
// use Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest;

class GameEggsBreakerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('game:eggsBreaker')
            ->setDescription('关联阿里妈妈的订单有效状态持续7天后，认定该订单为有效订单→[订单]有效 valid')
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

        $em = $this->getContainer()->get('doctrine')->getManager();
        $env =  $this->getApplication()->getKernel() ->getEnvironment();
        $logger = $this->getContainer()->get('logger');
        if( $input->getOption('finish-orders-audit')) {
            $duration= $input->getArgument('duration');
            $this->getContainer()->get('december_activity.game_eggs_breaker')
                ->finishAudit($duration);
               ;
//todo: add to service

            $output->writeln('ok');

        } else if( $input->getOption('refresh-ranking')) {
        } else {
            $output->writeln( 'no options');
        }
    }
}
