#!/bin/bash

grep  -iE  '(cross_exist_count|exchange_exist_count)'  /data/91jili/merge/log/migrate_user_csv_20151221_2023.log  |
grep '+' |
awk -F "\t" '{print $3}' |
sed -e "s/[\r\n\"]//g" |
awk -F '+' 'BEGIN{OFS=","}{if( $1 != $2 ) print "\""$1"\"","\""$2"\"";}' | tee /tmp/conn_email_diff

head -n 1  /data/91jili/merge/jl_csv/user.csv  > /tmp/conn_email_diff_jl.user.csv
sed -e "s/,/\n/g" /tmp/conn_email_diff  | sort |uniq | xargs -I {} grep -m 1 {}  /data/91jili/merge/jl_csv/user.csv  >> /tmp/conn_email_diff_jl.user.csv
head -n 1 /data/91jili/merge/ww_csv/panelist.csv > /tmp/conn_email_diff_ww.panelist.csv
sed -e "s/,/\n/g" /tmp/conn_email_diff  | sort |uniq | xargs -I {} grep -m 1 {}  /data/91jili/merge/ww_csv/panelist.csv  >> /tmp/conn_email_diff_ww.panelist.csv
