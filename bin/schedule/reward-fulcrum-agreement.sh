#! /bin/bash
# add crontab sample
# 0 2 * * * /bin/sh /var/www/html/jili/bin/schedule/reward-fulcrum-agreement.sh 2>/data/91jili/logs/cron/reward-fulcrum-agreement.log 1>/dev/null 

cd $(dirname $0)/../../;
./app/console panel:reward-fulcrum-agreement --definitive `date +%Y-%m-%d -d="-1 day"` -e prod

