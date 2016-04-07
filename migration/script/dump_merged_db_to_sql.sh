#! /bin/bash

SQL_FILE='/tmp/merged_.sql';

DB_NAME=zili_dev_`date +%w`
DB_HOST=localhost
DB_USER=root
export MYSQL_PWD=ecnavi

echo "-- ${DB_NAME}" > ${SQL_FILE}

mysqldump -u${DB_USER} -h${DB_HOST} --no-data  --no-create-db  ${DB_NAME} >> ${SQL_FILE}


# tables with data requires  for development
tables_wanted=(ad_position advertiserment ad_banner taobao_self_promotion_products activity_category ad_activity ad_category taobao_category taobao_component taobao_recommend callboard cb_category checkin_point_times checkin_adver_list cityList emar_activity_commission emar_products_cron emar_products_croned emar_websites emar_websites_category emar_websites_category_cron emar_websites_cron emar_websites_croned experience_advertisement hobby_list market_activity point_reason provinceList rate_ad limit_ad points_exchange_type reward_type month_income)
count_tables_wanted=${#tables_wanted[@]}

for (( i = 0; i < count_tables_wanted; i++ )); do
    tbl=${tables_wanted[$i]}
     
    mysqldump -u${DB_USER} -h${DB_HOST} --no-create-info ${DB_NAME}  ${tbl} >> ${SQL_FILE}
done


