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

class GmoMemberListCSVCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('gmo:member_list_csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parameterService = $this->getContainer()->get('app.parameter_service');

        $filepath = $parameterService->getParameter('gmo_memberlist_filepath');
        $filename = $parameterService->getParameter('gmo_memberlist_filename');
        $compress = true;

        $fs = new Filesystem();
        if (true !==  $fs->exists($filepath)) {
            $fs->mkdir($filepath);
        }
        $handle = fopen($filepath . '/' . $filename, 'w');

        // Add the header of the CSV file
        fputcsv($handle,
            array(
                'MONITOR_ID',
                'EMAIL',
                'EMAIL_MD5',
                'SEX_CD',
                'AGE',
                'BIRTHDAY',
                'PREF_CD',
                //optional below
                'ZIP_CD1',
                'ZIP_CD2',
                'MARRIAGE',
                'INDUSTRY_TYPE_CD',
                'JOB_CD',
                'C_PHONE_COMP',
                'DRIVER_LICENSE',
                'HOUSE_OWNER',
                'ENTRY_DATE',
                'LAST_LOGIN_DATE',
                'ACTIVE_FLAG',
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
                cl.gmo_city_id as pref_cd,
                null as zip_cd1,
                null as zip_cd2,
                null as marriage,
                null as industry_type_cd,
                null as job_cd,
                null as c_phone_comp,
                null as driver_license,
                null as house_owner,
                date_format(u.register_complete_date, '%Y%m%d') as entry_date,
                date_format(u.last_login_date, '%Y%m%d') as last_login_date,
                1 as active_flag
            from user u
            left join (user_profile up
              left join cityList cl on cl.city_id = up.city
            )
            on u.id = up.user_id
            where date(u.last_login_date) > date_sub(curdate(), interval 180 day)
          ) t
          where t.sex_cd is not null and t.birthday is not null;
        ";
        $em = $this->getContainer()->get('doctrine')->getManager();
        $results = $em->getConnection()->query($sql);

        // Add the data queried from database
        while($row = $results->fetch()) {
            fputcsv($handle, // The file pointer
                array(
                    $row['id'],
                    $row['email'],
                    $row['email_md5'],
                    $row['sex_cd'],
                    $row['age'],
                    $row['birthday'],
                    $row['pref_cd'],
                    $row['zip_cd1'],
                    $row['zip_cd2'],
                    $row['marriage'],
                    $row['industry_type_cd'],
                    $row['job_cd'],
                    $row['c_phone_comp'],
                    $row['driver_license'],
                    $row['house_owner'],
                    $row['entry_date'],
                    $row['last_login_date'],
                    $row['active_flag'],
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
