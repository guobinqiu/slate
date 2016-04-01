#!/bin/bash
source /data/91jili/merge/bin/config.bashrc 

CUR_DIR=$(cd `dirname $0` && pwd -P)

mysql -u${LOC_DB_USER} -p${LOC_DB_PWD} ${LOC_DB_NAME} < ${CUR_DIR}/insert_migration.sql
