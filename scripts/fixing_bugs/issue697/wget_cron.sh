#!/bin/bash
# re push the callback 
DT=$(date +"%Y%m%d%H%M%S")


# 控制每单位时间内 发出的请求数量。
# 取apache 限制的1/2 到1/3即可。
max_request_per_second=100

i=0; 
j=0;
#t1=$(date +%s)


# 找到2/27/2015 ~ 3/14/2015 期间,
perl -Ilib/ csv_parser.pl > ~/confirmed_${DT}.log
grep 'WGET|||' ~/confirmed_${DT}.log| sed -e "s/\ *WGET|||//g" > ~/confirmed_${DT}_x.log
grep 'WGET|_|' ~/confirmed_${DT}.log| sed -e "s/\ *WGET|_|//g" >> ~/confirmed_${DT}_x.log
mkdir  -p /tmp/yqfconfirmed_output/ /tmp/yqfconfirmed_wgetlog/
# confirmed data 
for x in $(cat ~/confirmed_${DT}_x.log)
do
    if [[ $i -ge $max_request_per_second ]]; then
        sleep 1 
        let "i=0";
    fi

    wget -O /tmp/yqfconfirmed_output/$j -P/tmp/yqfconfirmed_wgetlog/ -a /tmp/wget.yqfconfirmed.log  "http://www.91jili.com/emar/api/callback?$x"
    let "j+=1"; 
done

let "i=0"; 
let "j=0"; 



# 找到3/15/2015 ~ 4/14/2015 期间,
perl -Ilib/ accesslog_parser.pl > ~/access_log_${DT}.log
# accesslog  
grep 'query_string' ~/access_log_${DT}.log | sed -e 's/^.*query_string: //' > ~/access_log_${DT}_x.log
sed -i'' -e '/APIMemberId/d' ~/access_log_${DT}_x.log
mkdir -p /tmp/httpaccess_output/ /tmp/httpaccess_wgetlog/
for x in $(cat ~/access_log_${DT}_x.log)
do
    if [[ $i -ge $max_request_per_second ]]; then
        #statements
        sleep 1 
        let "i=0";
    fi

    #  235上测试
    wget -O /tmp/httpaccess_output/$j -P/tmp/httpaccess_wgetlog/ -a /tmp/wget.httpaccess.log  "http://www.91jili.com/emar/api/callback?$x"
    let "j+=1";
done