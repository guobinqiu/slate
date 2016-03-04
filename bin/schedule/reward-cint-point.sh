#! /bin/bash
# add crontab sample
# 0 2 * * * /bin/sh /var/www/html/jili/bin/schedule/reward-cint-point.sh &2>/data/91jili/logs/cron/reward-cint-point.log &1>/dev/null

cd $(dirname $0)/../../;
./app/console panel:reward-cint-point --definitive `date +%Y-%m-%d --date="-1 day"` -e prod
