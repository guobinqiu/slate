<?php
/*******

Context:

 game_eggs_breker_tobao_order表中没有设置 
unique( user_id , order_id)

 需要清掉每个用户 提交的重复数据

TODO:
 不同用户，还是可以提交重复和数据，所以在提交时，
或审核前需要 对订单号 是否属于当前用户做一个判断 。




调试时
php __FILE__   -u user -p MyNewPassword -hlocalhost -d jili_0904 2>/tmp/r.err

正式执行时
修改db 帐号
php __FILE__ -e prod -uDBUSER -pDBPWD -hDBHOST -dDBNAME 2>/tmp/error

***/

$options = getopt('e:u:p:h:d:');
// var_dump($options);
//die();
$is_prod = (isset($options['e']) && $options['e'] === 'prod') ? true: false; 

/* Connect to an ODBC database using driver invocation */
$user = isset($options['u']) ?  $options['u']:'root' ;
$password = isset($options['p'])?   $options['p']:'MyNewPassword' ;
$host = isset($options['h'])?   $options['h']:'127.0.0.1' ;
$database =  isset($options['d']) ?  $options['d']: 'jili_0904' ;

$dsn = "mysql:dbname=$database;host=$host;port=3306";

echo $dsn,PHP_EOL;
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_PERSISTENT  => false,
); 

try {
    $dbh = new PDO($dsn, $user, $password,$options);
} catch (PDOException $e) {
    fwrite( STDERR, 'Connection failed: ' . $e->getMessage(). PHP_EOL);
    exit -1;
}


$sql = 'select count(*) as cnt , max(id) as  max_id , group_concat(id) as ids , user_id , order_id  from game_eggs_breaker_taobao_order group by user_id , order_id having cnt > 1;';
try {
    $stmt = $dbh->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $count_deleted = 0;
    while( $row = $stmt->fetch()) {
        echo '-- ' , $row['ids'] ;
        $ids = explode(',', $row['ids']);

        //is there audit_completed rows?
        $stmt1 = $dbh->query('select *  from game_eggs_breaker_taobao_order where id in ('. $row['ids'] .')  and audit_status = 2 ');
        $stmt1->setFetchMode(PDO::FETCH_ASSOC);
        $result_test = $stmt1->fetchAll();

        if(count($result_test )== 0 ) {
            $id_exclude = $row['max_id'];
        } else if(count( $result_test )== 1 ) {
            $id_exclude = $result_test[0]['id']  ;
        } else {
            echo "\e[1;31m [SKIPPED]\e[0m\n";
            fwrite(STDERR, 'ERROR:dumplidated order_id audit completed.'.PHP_EOL. json_encode($result_test ). PHP_EOL);
            continue;
        }

        $k = array_search($id_exclude, $ids);

        unset($ids[$k]);
        echo  ' => ' , implode(' : ' , $ids),PHP_EOL;
        foreach($ids as $id )  {
            $sql = 'delete from game_eggs_breaker_taobao_order where id  = ' . $id . ' limit 1' ;
            if( $is_prod ) {
                $count_ =  $dbh->exec( $sql );
                $count_deleted += $count_;
            } else {
                echo $sql , PHP_EOL;
            }
        }
    }

    if($is_prod) {
        echo ' result: ' , $count_deleted , ' rows deleted!',PHP_EOL;
    }
} catch( PDOException $e){
echo __LINE__, ' ' ;
    echo $e->getMessage();
    echo PHP_EOL;
}

// try {  
//     $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $dbh->beginTransaction();
// 
// 
//     $dbh->commit();
// 
// } catch (Exception $e) {
//     $dbh->rollBack();
//     echo "Failed: " . $e->getMessage();
// }
