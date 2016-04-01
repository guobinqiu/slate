#!/bin/bash
log_time=`date "+%F"`
/usr/bin/php /var/www/html/jili/app/console jili:run_crontab_Dmdelivery pointFailureForWeek > /data/91jili/logs/cron/DmdeliveryForWeek/$log_time.log
