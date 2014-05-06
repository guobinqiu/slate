<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PointManageCommand extends ContainerAwareCommand {
    protected function configure() {
        $this->setName('api:pointmanage')
        ->setDescription('add point in manual control')
        ->addArgument('name', InputArgument :: REQUIRED, 'upload file name');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        /** @var $logger LoggerInterface */
        $logger = $this->getContainer()->get('logger');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $name = $input->getArgument('name');
        $output->writeln('name:' . $name);
        //$logger->error('{mmzhang}'. implode(':', array(__LINE__,__CLASS__,'ignored','') ). 'file name:'.$name);

        if ($name) {
            //判断是否是csv文件
            $format = explode(".", $name);
            if ((!isset ($format[1])) || (isset ($format[1]) && $format[1] != "csv")) {
                $output->writeln('Please upload csv file and code is utf-8 file.');
                return;
            }

            //文件路径
            $log_dir = $this->getContainer()->getParameter('file_path_admin_point_manage');
            $output->writeln('log_dir:' . $log_dir);

            $path = $log_dir . "/" . $name;
            $log_path = $log_dir . "/" . $format[0] . "_log.csv";

            $return = $this->getContainer()->get('point_manage.processor')->process($path, $log_path);

            $output->writeln(print_r($return));

            $output->writeln('ok');
        } else {
            $output->writeln('name is require.');
        }
    }
}
?>
