#!/bin/bash

check_env(){
    if [ -z "$(command -v innobackupex)" ];then
        echo "The innobackupex executable was not found, check if you have installed percona-xtrabackup."
        exit 1
    fi
    if [ ! -d $backup_dir ];then
        echo "Backup directory does not exist. Check your config and create the backup directory"
        exit 1
    fi
    if [ ! -d $log_dir ];then
        echo "Log directory does not exist. Check your config and create the backup directory"
        exit 1
    fi
    if [ -f ./innobackup.conf ];then
        source ./innobackup.conf
    else
        echo "Missing Config file, Please check"
        exit 1
    fi
    #ssh -p$remote_port $remote "rm -rf $remote_backup_dir && mkdir -p $remote_backup_dir"
}

get_full(){
    if [ ! -d $backup_full ];then
    	innobackupex --defaults-files=$db_conf --socket=$db_socks --user=$db_user --password=$db_passwd --no-timestamp --rsync --use-memory=1G $backup_full
    	echo `date +%Y-%m-%d` > $backup_full/full_date
        #Backup to office server
        #cd $tmp_dir 
        #tar zcvf full-$date.tar.gz $backup_full
        #scp -P $remote_port -r full-$date.tar.gz $remote:$remote_backup_dir && rm -rf $full*.tar.gz
    	rm -rf $backup_incremental
    else
    	full_lsn=`cat $backup_full/xtrabackup_checkpoints | awk '/to_lsn/{print $3}'`
        echo "start incremental backup"
    fi
}

get_incremental(){
    if [ -d $backup_incremental ];then		
	    count=`ls $backup_incremental | wc -l`
        if [ $count -ge 1 ];then
            latest=`ls $backup_incremental | awk 'END{print $NF}'`
            innobackupex --defaults-files=$db_conf --socket=$db_socks --user=$db_user --password=$db_passwd --incremental --rsync --use-memory=1G $backup_incremental --incremental-basedir=$backup_incremental/$latest
        else
            innobackupex --defaults-files=$db_conf --socket=$db_socks --user=$db_user --password=$db_passwd --incremental --rsync --use-memory=1G $backup_incremental --incremental-basedir=$backup_full
        fi
        update_latest=`ls $backup_incremental | awk 'END{print $NF}'`
        #cd $tmp_dir
        #tar zcvf $update_latest.tar.gz $backup_incremental/$update_latest
        #scp -P $remote_port -r $update_latest.tar.gz $remote:$remote_backup_dir && rm -rf $update_latest.tar.gz
    else
    	mkdir -p $backup_incremental && echo "next time will create incremental backup"
    fi
}

del_old_backup(){
    createday=`cat $backup_full/full_date`
    if [ $createday == $del_day ];then
        rm -rf $backup_full 
    else
        echo "start backup"
    fi
}

check_env
del_old_backup
get_full
get_incremental

