#! /bin/bash
# crontab 
#  bash ...../october_activity.sh &2>/tmp/a.log &1>/dev/null 

cd $(dirname $0)/../../;
./app/console jili:cpa_ranking_activity "2014-10-01 00:00:00" "2014-10-31 23:59:59" -e prod 
