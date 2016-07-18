#!/bin/bash

if [ -f innobackup.conf ];then
    source innobackup.conf
else
    echo "Missing Config file, Please check"
    exit 1
fi


get_last_incremental(){
    last_backup=`ls $backup_incremental | awk 'END{print $NF}'`
}

usage(){
    echo "Usage :
    e.g:  $(basename $0) $last_backup"
}

get_incremental_list(){
	ls $backup_incremental > /tmp/list
	sed -i '/'"$restore_date"'/,$d' /tmp/list
	incremental_list=`cat /tmp/list`   
}

restore(){
    get_incremental_list
    innobackupex --apply-log --redo-only $backup_full
    for i in $incremental_list
    do
        innobackupex --apply-log --redo-only $backup_full --incremental-dir=$backup_incremental/$i
    done
    innobackupex --apply-log $backup_full --incremental-dir=$backup_incremental/$restore_date
    innobackupex --apply-log $backup_full
    mv $db_dir $db_dir.old
    innobackupex --copy-back $backup_full
    chown -R mysql:mysql $db_dir
    service mysqld restart
}

restore_date=$1
get_last_incremental
if [ -z $restore_date ]; then
    usage
    exit 1
else
    echo "Start to restre from $restore_date"
    restore
    if [ $? -eq 0 ]; then
    	echo "Restore done, please check and remove backup dir via manual"
    else
        echo "Restore fail please check"
    fi
fi


