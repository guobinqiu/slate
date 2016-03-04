#! /bin/bash
# add crontab sample
# 0 2 * * * /bin/sh /var/www/html/jili/bin/schedule/reward-fulcrum-point.sh 2>/data/91jili/logs/cron/reward-fulcrum-point.log 1>/dev/null 

cd $(dirname $0)/../../;
./app/console panel:reward-fulcrum-point --definitive `date +%Y-%m-%d -d "-1 day"` -e prod
