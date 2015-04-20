#!/bin/bash
# re push the callback 

#grep 'WGET|||' 6.3.log | sed -e "s/\ *WGET|||//g" > 6.3x.log
for x in $(catt /tmp/6.3x.log)
do
    wget -P/tmp/g -a /tmp/wget.g.log  "http://jarod-jili.com/app_dev.php$x"
done

# grep query_string 3.log | sed -e "s/query_string://g"   -e "s/ //g" > 3x.log
for x in $(cat /tmp/3x.log)
do
    wget -P/tmp/h  -a /tmp/wget.h.log  "http://jarod-jili.com/app_dev.php/emar/api/callback?$x"
done
