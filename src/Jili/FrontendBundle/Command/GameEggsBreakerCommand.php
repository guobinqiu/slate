<?php
namespace Jili\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//    GameEggsBreakerCommand
class GameEggsBreakerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('game:egg-breaker')
            ->setDescription('manager emar products with table emar_products_synced')
            ->addOption(
               'refresh-ranking',
               null,
               InputOption::VALUE_NONE,
               '更新最近得到金蛋的用户缓存文件'
            )
            ->addOption(
               'audit-taobaoOrders',
               null,
               InputOption::VALUE_NONE,
               '关联阿里妈妈的订单有效状态持续7天后，认定该订单为有效订单→[订单]有效 valid'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $logger = $this->getContainer()->get('logger');
        $output->writeln('ok');
        if ($input->getOption('refresh-ranking')) {

            // read from the 
        } else if ($input->getOption('audit-taobaoOrders')) {
            // code...
        }

    }

}
