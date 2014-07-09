<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JulyActivityCommand extends ContainerAwareCommand {
    protected function configure() {
        $this->setName('jili:julyactivity')->setDescription('july activity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('start...');

        $filename = $this->getContainer()->getParameter('file_path_july_activity');
        //写文件
        $handle = fopen($filename, "w");

        $start = "2014-07-01 00:00:00";
        $end = "2014-07-31 23:59:59";

        //前100名
        $limit = 100;
        $offset = 0;
        $users = $this->getUsers($start, $end, $limit, $offset);
        foreach ($users as $key => $value) {
            $value['no'] = $key +1;
            fputcsv($handle, $value);
        }

        //第200名
        $limit = 1;
        $offset = 199;
        $users = $this->getUsers($start, $end, $limit, $offset);
        $users[0]['no'] = 200;
        fputcsv($handle, $users[0]);

        //第300名
        $limit = 1;
        $offset = 299;
        $users = $this->getUsers($start, $end, $limit, $offset);
        $users[0]['no'] = 300;
        fputcsv($handle, $users[0]);

        //第400名
        $limit = 1;
        $offset = 399;
        $users = $this->getUsers($start, $end, $limit, $offset);
        $users[0]['no'] = 400;
        fputcsv($handle, $users[0]);

        //第500名
        $limit = 1;
        $offset = 499;
        $users = $this->getUsers($start, $end, $limit, $offset);
        $users[0]['no'] = 500;
        fputcsv($handle, $users[0]);

        fclose($handle);

        $output->writeln('successfully');
    }

    protected function getUsers($start, $end, $limit, $offset) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $users = $em->getRepository('JiliApiBundle:User')->getTotalCPAPointsByTime($start, $end, $limit, $offset);
        return $users;
    }
}