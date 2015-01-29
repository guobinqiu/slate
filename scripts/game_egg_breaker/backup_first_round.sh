#! bin/bash
SUFFIX="$(date +"%Y%m%dT%H%M")" 


# 第一阶段的活动结束后，所有数据清零
 
# 尽量使用相同结构的新表
# game_eggs_breaker_taobao_order 做个备件份，然后清空
# game_eggs_broken_log 做个备份，然后清空
# game_eggs_breaker_eggs_info 做个备份然后清空
# 后端开发 0.5MD
DB_NAME=jili_db #数据库名
DB_USER=jili_admin #数据库用户名
DB_PASS=vct@20140423 #数据库密码
DB_HOST=vct002 #数据库的server 的ip 或dns
BAKCUP_DATA_DIR=/tmp/game_eggs_breaker/ # 备份数据的保存目录


mkdir -p ${BAKCUP_DATA_DIR}

tables=(game_eggs_breaker_taobao_order  game_eggs_broken_log game_eggs_breaker_eggs_info)

count=${#tables[@]}

for (( i = 0; i < ${count}; i++ )); do
    tb=${tables[$i]}
    mysqldump -u${DB_USER} -p${DB_PASS} -h${DB_HOST} ${DB_NAME} ${tb} > ${BAKCUP_DATA_DIR}/${tb}_${SUFFIX}.sql ;
done

echo 'list all the backup sql files:'
ls -lrt ${BAKCUP_DATA_DIR}

echo 'Whether truncate the tables backuped(yes):'
read  line 

if [[ $line =  "yes" ]]; then

    for (( i = 0; i < ${count}; i++ )); do
        tb=${tables[$i]}
        mysql -u${DB_USER} -p${DB_PASS} -h${DB_HOST} ${DB_NAME} -e """ truncate table ${tb}""";
    done
else    
    echo 'Not truncated tables ' 
fi

echo done
