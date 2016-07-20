<?php


find_last_get_points_at();

echo "执行完成!" . PHP_EOL;
exit;

function find_last_get_points_at(){
    $link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error() . PHP_EOL);
    mysql_select_db('jili_db') or die('Could not select database');
    mysql_set_charset("UTF8", $link);

    for($i = 0; $i < 10; $i++){
        $tableName = "point_history0" . $i;
        printf(date_create()->format('Y-m-d H:i:s') . " Start to operate " . $tableName . PHP_EOL); 
        // 获取每个用户最后一次获得积分的时间
        $selectQuery = "select user_id, MAX(create_time) as last_update_time from " . $tableName . " where create_time >= '2016-01-01 00:00:00' group by user_id";
        $selectResult = mysql_query($selectQuery) or die('Query failed: ' . mysql_error() . PHP_EOL);
        
        $updateCount = 1;

        // 更新这个用户的last_get_points_at时间
        while($row = mysql_fetch_assoc($selectResult)){
            $lastUpdateTime = $row['last_update_time'];
            $userId = $row['user_id'];
            $updateQuery = "update user set last_get_points_at = '" . $lastUpdateTime . "' where id = '" . $userId . "'";
            $result = mysql_query($updateQuery);
            if(!$result){
                $message  = 'Invalid query: ' . mysql_error() . PHP_EOL;
                $message .= 'Whole query: ' . $query . PHP_EOL;
                print $message;
            } else {
                mysql_query("COMMIT");
                $updateCount ++;
                if(0 == $updateCount % 1000){
                    printf(date_create()->format('Y-m-d H:i:s') . " update count = " . $updateCount . PHP_EOL); 
                }
            }
        }
        printf(date_create()->format('Y-m-d H:i:s') . " totla update count = " . $updateCount . PHP_EOL); 
    }
    $close_flag = mysql_close($link);

    if ($close_flag){
        printf(date_create()->format('Y-m-d H:i:s') . " connection closed" . PHP_EOL);
    }
}




?>
