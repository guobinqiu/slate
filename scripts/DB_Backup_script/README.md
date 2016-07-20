Backup mysql DB for 91wenwen


Summay

Full Backup 
    使用 mysqldump 在每天晚上1点，db server上的备份目录是/data/backup/，保留60天（以后可能缩短至30天）
    在Office中每天4点将db server full backup 取回至200服务器。备份目录/home/backup/sqlserver/backup，预计保留100天

Incremental Backup
    使用  Percona innobackupex (xtrabackup)，每天1点40分做一次全备为base，之后每小时做一次增备。
    full PATH：  /data/innobackupex/backup/full
    incremental PATH: /data/innobackupex/backup/incremental


Env

Install innobackupex
    1.yum install http://www.percona.com/downloads/percona-release/redhat/0.1-3/percona-release-0.1-3.noarch.rpm
    2.yum install percona-xtrabackup-24

Run Script

    restore 
        ./restore_innobackup.sh  2016-07-19_03-40-01(从增量备份的列表中选择需要恢复的时间点)
    
