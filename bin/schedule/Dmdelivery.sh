#!/bin/bash
log_time=`date "+%F"`
/usr/bin/php /var/www/html/jili/app/console jili:run_crontab_Dmdelivery pointFailure > /data/91jili/logs/cron/Dmdelivery/$log_time.log
