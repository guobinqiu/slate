<?php
namespace Jili\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class VoteApiCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('jili:vote_api')->setDescription('get vote data from 91wenwen');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start...');
        $output_filename = $this->getContainer()->getParameter('file_path_wenwen_vote');
        $url = $this->getContainer()->getParameter('wenwen_vote_api');

        //请求api接口
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);

        //解析接口数据
        $data = json_decode($return, true);

        if ($data['meta'] && $data['meta']['code'] == 200) {

            $file_path = dirname($output_filename);
            $fs = new Filesystem();
            if( true !==  $fs->exists($file_path) ) {
                $fs->mkdir($file_path);
            }
            //保存接口数据
            $handle = fopen($output_filename, 'w');
            if ($handle) {
                fwrite($handle, json_encode($data['data']));
            }
            fclose($handle);

        }
        $output->writeln('successfully');
    }
}
