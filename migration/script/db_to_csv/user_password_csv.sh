#!/bin/bash

CUR_DIR=$(cd `dirname $0` && pwd -P)
source ${CUR_DIR}/config.bashrc

export MYSQL_PWD=${LOC_DB_PWD}

mkdir -p /data/91jili/merge/chk_csv/

time php -f bin/db_to_csv.php  ${LOC_DB_HOST} ${LOC_DB_USER} ${LOC_DB_PWD} ${LOC_DB_NAME} user "select  id, email, origin_flag, password_choice, pwd from user order by email asc"  > /data/91jili/merge/chk_csv/user.csv
time php -f bin/db_to_csv.php  ${LOC_DB_HOST} ${LOC_DB_USER} ${LOC_DB_PWD} ${LOC_DB_NAME} user_wenwen_login "select * from user_wenwen_login order by user_id"  > /data/91jili/merge/chk_csv/user_wenwen_login.csv


