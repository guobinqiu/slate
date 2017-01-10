<?php

namespace Wenwen\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * php app/console gmo:member_list_csv --filepath=/Users/guobin/workspace/PointMedia/web --filename=memberList.csv --compress=true
 */
class GmoMemberListCSVCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('gmo:member_list_csv');
        $this->addOption('filepath', null, InputOption::VALUE_REQUIRED);
        $this->addOption('filename', null, InputOption::VALUE_REQUIRED);
        $this->addOption('compress', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filepath = $input->getOption('filepath');
        $filename = $input->getOption('filename');
        $compress = $input->getOption('compress');

        $fs = new Filesystem();
        if (true !==  $fs->exists($filepath)) {
            $fs->mkdir($filepath);
        }
        $handle = fopen($filepath . '/' . $filename, 'w');

        // Add the header of the CSV file
        fputcsv($handle,
            array(
                'MONITOR_ID', 'EMAIL', 'EMAIL_MD5',
                'SEX_CD', 'AGE', 'BIRTHDAY',
                'PREF_CD'
            ),
            ','
        );

        // Query data from database
        $sql = "
            select t.* from (
                select
                    u.id,
                    null as email,
                    null as email_md5,
                    up.sex as sex_cd,
                    timestampdiff(year, up.birthday, curdate()) as age,
                    date_format(up.birthday, '%Y%m%d') as birthday,
                    cl.gmo_city_id as pref_cd
                from user u
                left join (user_profile up
                  left join cityList cl on cl.city_id = up.city
                )
                on u.id = up.user_id
                where date(u.last_login_date) > date_sub(curdate(), interval 180 day)
            ) t
            where t.sex_cd is not null and t.birthday is not null
        ";
        $em = $this->getContainer()->get('doctrine')->getManager();
        $results = $em->getConnection()->query($sql);

        // Add the data queried from database
        while($row = $results->fetch()) {
            fputcsv($handle, // The file pointer
                array(
                    $row['id'], $row['email'], $row['email_md5'],
                    $row['sex_cd'], $row['age'], $row['birthday'],
                    $row['pref_cd']
                ), // The fields
                ',' // The delimiter
            );
        }

        fclose($handle);

        if ($compress) {
            $zip = new ZipArchive();
            $zip->open($filepath . '/' . $filename . '.zip', ZipArchive::OVERWRITE);
            $zip->addFile($filepath . '/' . $filename, $filename);
            $zip->close();
        }
    }
}
