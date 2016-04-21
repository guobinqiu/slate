<?php

// sed 's/"/\\"/g;s/,/\\,/g;s/\t/\",\"/g;s/^/\"/;s/$/\"/;s/\n//g' 
// sed 's/"/""/g;s/\t/\",\"/g;s/^/\"/;s/$/\"/;s/\n//g' 
// sed 's/"/\\"/g;s/,/\\,/g;s/\\/\\\\/g;s/\t/,/g;s/\n//g' 
//sed 's/\\\\/\\\\\\\\/g;s/"/\\\\"/g;s/,/\\\\,/g;s/\\t/,/g;s/\\n//g' 
// sed 's/\\\\/\\\\\\\\/g;s/"/\"/g;s/\\t/","/g;s/^/"/;s/\$/"/;s/\\n//g' 
//sed 's/"/""/g;s/\\t/","/g;s/^/"/;s/$/"/g;s/\\n//g' 
$sed_partial=<<<CMD
sed "s/\"/\"\"/g;s/\\t/\",\"/g;s/^/\"/;s/$/\"/g;s/\\n//g" 
CMD;


$cmd0=<<<CMD
time php -f bin/db_to_csv.php  \${WW_DB_HOST} \${WW_DB_USER} \${WW_DB_PWD} \${WW_DB_NAME}  panelist "select * from panelist where panel_id = 2 order by id asc "  > /mnt/tmp/merge/ww_csv/panelist.csv
CMD;

$cmd1=<<<CMD
time php -f bin/db_to_csv.php  \${WW_DB_HOST} \${WW_DB_USER} \${WW_DB_PWD} \${WW_DB_NAME} %1\$s "select * from %1\$s %2\$s"  > /mnt/tmp/merge/ww_csv/%1\$s.csv
CMD;

# 'sop_respondent'
$cmd2=<<<CMD
time php -f bin/db_to_csv.php  \${WW_DB_HOST} \${WW_DB_USER} \${WW_DB_PWD} \${WW_DB_NAME}  ssi_respondent "select ssi.* from ssi_respondent ssi inner join panelist p on p.id = ssi.panelist_id  where p.panel_id = 2 order by ssi.panelist_id asc "  > /mnt/tmp/merge/ww_csv/ssi_respondent.csv
CMD;

$a = array(
//        'panelist',
        'panel_91wenwen_panelist_profile',
        'panel_91wenwen_panelist_profile_image',
        'panel_91wenwen_panelist_detail',
        'panel_91wenwen_panelist_point',
        'panel_91wenwen_panelist_mobile_number',
        'panel_91wenwen_pointexchange_91jili_account',
        'panel_91wenwen_panelist_91jili_connection',
        'sop_respondent',
        'panel_91wenwen_panelist_sina_connection',
        'panel_91wenwen_vote_answer_201604',
        'panel_91wenwen_vote',
        'panel_91wenwen_vote_image',
        'panel_91wenwen_vote_choice',
        'panel_faq',// (根据情况)
        'panel_faq_category',//(根据情况)
);
$orders= array(
        'panel_91wenwen_panelist_profile' => 'order by panelist_id asc',
        'panel_91wenwen_panelist_profile_image'=> ' order by panelist_id asc',
        'panel_91wenwen_panelist_detail'=> 'order by panelist_id asc',
        'panel_91wenwen_panelist_point'=> 'order by panelist_id asc',
        'panel_91wenwen_panelist_mobile_number'=> ' order by panelist_id asc',
        'panel_91wenwen_pointexchange_91jili_account'=> ' order by panelist_id asc',
        'panel_91wenwen_panelist_91jili_connection'=> ' order by panelist_id asc',
        'sop_respondent'=> ' order by panelist_id asc',
        'panel_91wenwen_panelist_sina_connection'=> 'order by panelist_id asc',
        'panel_91wenwen_vote_answer_201604'=> '',
        'panel_91wenwen_vote'=> '',
        'panel_91wenwen_vote_image'=> '',
        'panel_91wenwen_vote_choice'=> '',
        'panel_faq'=> '',  // (根据情况)
        'panel_faq_category'=> '',//(根据情况)
);

echo <<<BASH
#!/bin/bash

CUR_DIR=$(cd `dirname $0` && pwd -P)

rm -rf /mnt/tmp/merge/ww_csv/
mkdir -p /mnt/tmp/merge/ww_csv/

source \${CUR_DIR}/config.bashrc

BASH;


echo 'echo panelist' ,"\n";
echo $cmd0, "\n";

foreach ($a as $v){
    echo 'echo ' ,$v ,"\n";
    printf($cmd1, $v , $orders[$v]);
    echo "\n";
    echo "\n";
}

echo 'echo ssi_respondent' ,"\n";
echo $cmd2, "\n";

echo <<<CMD

echo "tar"
time tar -cjf ww_csv.tar.bz ww_csv

exit;

-h

screen -S dump

bash bin/ww_csv.sh 1> log/dump_to_csv_log_`date +%F`.txt 2>&1

CMD;
