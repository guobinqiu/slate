<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PointRecentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('point:recent')
            ->setDescription('update the poinst recent cache file')
            ->addOption('date',null,InputOption::VALUE_REQUIRED, 'the date string YYYY-mm-dd')
            ->setHelp(  <<<EOT
For prod usage:
./app/console point:recent -e prod
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $env =  $this->getApplication()->getKernel() ->getEnvironment();


        //最新动态
        if('prod' === $env) {
            $filename = $this->getContainer()->getParameter('file_path_recent_point');
            $yesterday =  date('Y-m-d', strtotime(' -1 day'));
        } else {
            $yesterday  = $input->getOption('date');
            $filename = $this->getApplication()->getKernel()->getCacheDir().'/point_recent.cache' ;
        }

        $newActivity = $em->getRepository('JiliApiBundle:User')->getRecentPoint($yesterday);


        //写文件
        $handle = fopen($filename, 'w+');
        if (!$handle) {
            die('指定文件不能打开，操作中断!');
        }

        foreach ($newActivity as $activity) {
            $row = array(
                $activity['nick'],
                $activity['icon_path'],
                $activity['point_change_num'],
                $activity['display_name'] );

            fputcsv($handle, $row) ;
        }
        fclose($handle);

        $output->writeln('write to ' .$filename );

        return 1 ;
    }
}
