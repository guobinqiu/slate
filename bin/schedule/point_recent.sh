#! /bin/bash
# crontab 
#  bash ...../point_recent.sh &2>/tmp/a.log &1>/dev/null 

cd $(dirname $0)/../../;
./app/console point:recent  -e prod 
