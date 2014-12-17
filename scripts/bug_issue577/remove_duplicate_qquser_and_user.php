<?php
/*******

Context:
qq用户注册时产生的多个重复数据的删除
需要删的数据在user ,qq_user里

 需要清掉每个用户 提交的重复数据

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


$sql = 'select qq_user.id,qq_user.open_id,qq_user.user_id,user.id as uid, user.email,user.pwd, user.points,user.register_date,user.last_login_ip 
        from qq_user inner join user on qq_user.user_id = user.id 
        and qq_user.open_id in (select distinct(open_id) from qq_user group by qq_user.open_id having count(*)>1) order by qq_user.open_id;';
try {
    $stmt = $dbh->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $arr2 = array();
    $tmp_openid = "";
    $key = -1;
    foreach(  $stmt->fetchAll() as $row) {
        if($row['open_id']==$tmp_openid){
        } else {
            $key++;
            $tmp_openid = $row['open_id'];

        }
        $arr2[$key][] = $row;
    }
    
    $safeData = array();
    foreach(  $arr2 as $keys=>$rows) {
        //按照points排序，大的在前面
        sort_array($rows,'points','desc');
        
        //如果数组points都相同的情况下
        if($rows[0]['points']==$rows[1]['points']){
            
            //bind时userid两条记录一样的情况，qq_user里删除老的一条，user表不删
            if($rows[0]['user_id']==$rows[1]['user_id']){
                echo "+++++".$rows[0]['id'].",".$rows[0]['uid'].",".$rows[0]['points']."\n";
                unset($rows[0]);
                /*foreach($rows as $key=>$row){
                    echo "-----".$row['id'].",".$row['uid'].",".$row['points']."\n";
                }
                echo "**********"."\n";*/
    
                delete_data($dbh,$rows, false);
                
            } else {
                //其他情况下，最后条留下
                echo "+++++".$rows[count($rows)-1]['id'].",".$rows[count($rows)-1]['uid'].",".$rows[count($rows)-1]['points']."\n";
                unset($rows[count($rows)-1]);
                /*foreach($rows as $key=>$row){
                    echo "-----".$row['id'].",".$row['uid'].",".$row['points']."\n";
                }
                echo "**********"."\n";*/
                delete_data($dbh,$rows);
            }
            
        } else {
            // points有一个比较大，后面都比较小的情况下（一般为1）
            echo "+++++".$rows[0]['id'].",".$rows[0]['uid'].",".$rows[0]['points']."\n";
            unset($rows[0]);
                /*foreach($rows as $key=>$row){
                    echo "-----".$row['id'].",".$row['uid'].",".$row['points']."\n";
                }
                echo "**********"."\n";*/
            delete_data($dbh,$rows);
        }
        
    }

} catch( PDOException $e){
echo __LINE__, ' ' ;
    echo $e->getMessage();
    echo PHP_EOL;
}

function sort_array(&$array, $keyid, $order = 'asc', $type = 'number') {
    if (is_array($array)) {
        foreach($array as $val) {
            $order_arr[] = $val[$keyid];
       }
       $order = ($order == 'asc') ? SORT_ASC: SORT_DESC;
       $type = ($type == 'number') ? SORT_NUMERIC: SORT_STRING;
       array_multisort($order_arr, $order, $type, $array);
    }
}

function delete_data($dbh,$rows, $both=true) {
        
    foreach ($rows as $row){
        try{
            $dbh->beginTransaction();
            $dbh->exec(  'delete from qq_user where id  = ' . $row['id'] . ' limit 1' );
            if($both){
                $dbh->exec(   'delete from user where id  = ' . $row['uid'] . ' limit 1' );
            }
            $dbh->commit();
        } catch (Exception $ex) {
            $dbh->rollBack();
             echo"Failed: ".$e->getMessage();
        }
    }
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
