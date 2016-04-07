#!/bin/bash
log_time=`date "+%F"`
/usr/bin/php /var/www/html/jili/app/console jili:run_crontab_Dmdelivery pointFailureForMonth > /data/91jili/logs/cron/DmdeliveryForMonth/$log_time.log
