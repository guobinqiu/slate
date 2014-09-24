<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


class CpaRankingActivityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('jili:cpa_ranking_activity')
             ->setDescription('cpa ranking activity.')
             ->addArgument('start_time', InputArgument :: REQUIRED, 'start time')
             ->addArgument('end_time', InputArgument :: REQUIRED, 'end time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start...');

        //开始时间和结束时间
        $start_time = $input->getArgument('start_time');
        $end_time = $input->getArgument('end_time');
        $output->writeln('start_time:' . $start_time);
        $output->writeln('end_time:' . $end_time);

        //写文件
          ;
        $filename = $this->getContainer()->getParameter('file_path_cpa_ranking_activity'). date('Ym', strtotime($start_time)) . '.csv';
        $file_path = dirname($filename);
        $fs = new Filesystem();
        if( true !==  $fs->exists($file_path) ) {
            $fs->mkdir($file_path);
        }
        $handle = fopen($filename, 'w');

        //前100名
        $limit = 100;
        $offset = 0;
        $users = $this->getUsers($start_time, $end_time, $limit, $offset);
        foreach ($users as $key => $value) {
            $value['no'] = $key +1;
            fputcsv($handle, $value);
        }

        //第200名
        $limit = 1;
        $offset = 199;
        $users = $this->getUsers($start_time, $end_time, $limit, $offset);
        if ($users) {
            $users[0]['no'] = 200;
            fputcsv($handle, $users[0]);
        }

        //第300名
        $limit = 1;
        $offset = 299;
        $users = $this->getUsers($start_time, $end_time, $limit, $offset);
        if ($users) {
            $users[0]['no'] = 300;
            fputcsv($handle, $users[0]);
        }

        //第400名
        $limit = 1;
        $offset = 399;
        $users = $this->getUsers($start_time, $end_time, $limit, $offset);
        if ($users) {
            $users[0]['no'] = 400;
            fputcsv($handle, $users[0]);
        }

        //第500名
        $limit = 1;
        $offset = 499;
        $users = $this->getUsers($start_time, $end_time, $limit, $offset);
        if ($users) {
            $users[0]['no'] = 500;
            fputcsv($handle, $users[0]);
        }

        fclose($handle);

        $output->writeln('successfully');
    }

    protected function getUsers($start_time, $end_time, $limit, $offset) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $users = $em->getRepository('JiliApiBundle:User')->getTotalCPAPointsByTime($start_time, $end_time, $limit, $offset);
        return $users;
    }
}
