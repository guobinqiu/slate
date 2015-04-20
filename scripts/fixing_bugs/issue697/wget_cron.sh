#!/bin/bash
# re push the callback 
DT=$(date +"%Y%m%d%H%M%S")


# 控制每单位时间内 发出的请求数量。
# 取apache 限制的1/2 到1/3即可。
max_request_per_second=100000


# 找到3/15/2015 ~ 4/14/2015 期间,
perl -Ilib/ accesslog_parser.pl > ~/access_log_${DT}.log

# accesslog  
grep 'query_string' ~/access_log_${DT}.log | sed -e 's/^.*query_string: //' > ~/access_log_${DT}_x.log

sed -e '/APIMemberId/d' ~/access_log_${DT}_x.log

i=0; 
t1=$(date +%s)
for x in $(cat ~/access_log_${DT}_x.log)
do
    if [[ $i -ge $max_request_per_second ]]; then
        #statements
        t2=$(date +%s)
        let "diff=t2-t1"
        echo 'processed ', $diff , 's';
        if [[ $diff -lt  1  ]]  ; then
            let "to_sleep=1-diff"
            echo 'wait ', $to_sleep , 's';
            sleep $to_sleep
        fi
        let "i=0";
        let "t1=$(date +%s)"
    fi

#  235上测试
echo     wget -P/tmp/g -a /tmp/wget.g.log  "http://jarod-jili.com/app_dev.php$x"
done

exit;


# 找到2/27/2015 ~ 3/14/2015 期间,
perl -Ilib/ csv_parser.pl > ~/confirmed_${DT}.log
grep 'WGET|||' ~/confirmed_${DT}.log| sed -e "s/\ *WGET|||//g" > ~/confirmed_${DT}_x.log


# confirmed data 

let "i=0"; 
let "t1=$(date +%s)"

for x in $(cat ~/confirmed_${DT}_x.log)
do
    if [[ $i -ge $max_request_per_second ]]; then
        t2=$(date +%s)
        let "diff=t2-t1"
        echo 'processed ', $diff , 's';

        if [[ $diff -lt  1  ]]  ; then
            let "to_sleep=1-diff"
            echo 'wait ', $to_sleep , 's';
            sleep $to_sleep
        fi

        let "i=0";
        let "t1=$(date +%s)"
    fi

#  235上测试
echo     wget -P/tmp/h  -a /tmp/wget.h.log  "http://jarod-jili.com/app_dev.php/emar/api/callback?$x"
done


