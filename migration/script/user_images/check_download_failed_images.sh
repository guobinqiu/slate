#! /bin/bash

CUR_DIR=$(cd `dirname $0` && pwd -P)
source ${CUR_DIR}/config.bashrc

export MYSQL_PWD=${WW_DB_PWD}

mysql -B -u ${WW_DB_USER} -p${WW_DB_PWD}  -h ${WW_DB_HOST}  ${WW_DB_NAME}  < /mnt/tmp/merge/bin/check_download_failed_images.sql

