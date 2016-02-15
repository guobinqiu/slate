#! /bin/bash
# add crontab sample
# 0 2 * * * /bin/sh /var/www/html/jili/bin/schedule/reward-sop-point.sh &2>/data/91jili/logs/cron/reward-sop-point.log &1>/dev/null 

cd $(dirname $0)/../../;
./app/console panel:reward-sop-point --definitive date +%Y-%m-%d --date="-1 day" -e prod
