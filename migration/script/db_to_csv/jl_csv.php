<?php

$tables = array(
 //  'user' ,
   'weibo_user' ,
 //  'user_wenwen_cross' ,
   'exchange_from_wenwen',
   'migration_region_mapping',
   'weibo_user',
);


$s1 =<<<SQL
select * 
into outfile '~/tmp/jili_csv/%1\$s.csv'
from %1\$s;
SQL;

#sed 's/\\\\/\\\\\\\\/g;s/"/\"/g;s/\\t/","/g;s/^/"/;s/\$/"/;s/\\n//g' 

$sed_partial=<<<CMD
sed "s/\"/\"\"/g;s/\\t/\",\"/g;s/^/\"/;s/$/\"/g;s/\\n//g" 
CMD;

$cmd1=<<<CMD
time mysql -B -u\${JILI_DB_USER}  -h \${JILI_DB_HOST} \${JILI_DB_NAME} -e "select * from %1\$s "| $sed_partial > /data/91jili/merge/jl_csv/%1\$s.csv
CMD;

$cmd2=<<<CMD
time php -f bin/db_to_csv.php  \${JILI_DB_HOST} \${JILI_DB_USER} \${JILI_DB_PWD} \${JILI_DB_NAME} %1\$s "select * from %1\$s"  > /data/91jili/merge/jl_csv/%1\$s.csv
CMD;
echo <<<EOD
#!/bin/bash

CUR_DIR=$(cd `dirname $0` && pwd -P)
source \${CUR_DIR}/config.bashrc

export MYSQL_PWD=\${JILI_DB_PWD}

# time mysql -B -u\${JILI_DB_USER}  -h \${JILI_DB_HOST} \${JILI_DB_NAME}  -e "select *  from  user   order by email asc"| $sed_partial > /data/91jili/merge/jl_csv/user.csv
 
time php -f bin/db_to_csv.php  \${JILI_DB_HOST} \${JILI_DB_USER} \${JILI_DB_PWD} \${JILI_DB_NAME} user "select * from user order by email asc"  > /data/91jili/merge/jl_csv/user.csv


#time mysql -B -u\${JILI_DB_USER}  -h \${JILI_DB_HOST} \${JILI_DB_NAME} -e "select c.*, u.email from  user_wenwen_cross c left join user u on c.user_id = u.id order by c.id asc"| $sed_partial > /data/91jili/merge/jl_csv/user_wenwen_cross.csv

time php -f bin/db_to_csv.php  \${JILI_DB_HOST} \${JILI_DB_USER} \${JILI_DB_PWD} \${JILI_DB_NAME} user_wenwen_cross "select c.*, u.email from  user_wenwen_cross c left join user u on c.user_id = u.id order by c.id asc" > /data/91jili/merge/jl_csv/user_wenwen_cross.csv


EOD;

foreach( $tables  as $t) {
  echo 'echo ', $t,PHP_EOL;
  printf( $cmd2, $t);
  echo PHP_EOL;
} 
echo PHP_EOL;

echo <<<EOD
echo "tar"
time tar cjf  jl_csv.tar.bz jl_csv/


exit ;
echo "scp"
time scp -P9012 jl_csv.tar.bz jiangtao@testgroup.91jili.com:/data/91jili/merge

screen -S dump
bash bin/jl_csv.sh  1> log/perf_log_`date +"T%H%M%SD%Y%m%d"`.txt  2>&1


EOD;
