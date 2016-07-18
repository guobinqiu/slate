#!/bin/bash
shijian=`date "+%F"`

/usr/bin/mysqldump -uuser -ppassword jili_db > /data/backup/jili_$shijian.sql

cd /data/backup

tar -zcf ./jili_$shijian.tar.gz ./jili_$shijian.sql

rm -rf /data/backup/jili_$shijian.sql

