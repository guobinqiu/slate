<?php
$filename = "D:/xampp/htdocs/PointMedia/scripts/bug_issue550/import550.csv";
$log_file = "D:/xampp/htdocs/PointMedia/scripts/bug_issue550/log_file.sql";
$file_handle = fopen($filename, "r");
$log_handle = fopen($log_file, "w");
$contents = null;
if ($file_handle !== FALSE) {
    while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
        $contents[] = $data;
    }
}
fclose($file_handle);

$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_2014-09-23') or die('Could not select database');
mysql_set_charset("UTF8", $link);

mysql_query("BEGIN"); //开始一个事务
mysql_query("SET AUTOCOMMIT=0"); //设置事务不自动commit

foreach ($contents as $value) {

    $date = $value[0];
    $email = $value[1];
    $point = $value[2];

    $user_sql = "SELECT id,email,points FROM user WHERE email = '" . $email . "'";
    fwrite($log_handle, $email . ": " . $user_sql . "\r\n");
    $result = mysql_query($user_sql) or die('Query failed: ' . mysql_error());
    $user = mysql_fetch_assoc($result);
    fwrite($log_handle, "user_id:" . $user['id'] . ", email: " . $user['email'] . ", points: " . $user['points'] . "\r\n");

    $task_history_sql = "select * from task_history0" . ($user['id'] % 10) . " where user_id =" . $user['id'] . " and point = " . $point . " and task_name like '%亚马逊%' and date like '" . $date . "%'";
    fwrite($log_handle, $email . ": " . $task_history_sql . "\r\n");
    $result = mysql_query($task_history_sql) or die('Query failed: ' . mysql_error());
    $task_history = mysql_fetch_assoc($result);
    fwrite($log_handle, "id:" . $task_history['id'] . ", category_type: " . $task_history['category_type'] . ", status: " . $task_history['status'] . ", task_name:" . $task_history['task_name'] . "\r\n");

    //update task_history
    $task_history_update_sql = "update task_history0" . ($user['id'] % 10) . " set status = 3 where id = " . $task_history['id'];
    fwrite($log_handle, $email . ": " . $task_history_update_sql . "\r\n");
//    $task_history_flag = mysql_query($task_history_update_sql);
//    if (!$task_history_flag) {
//        mysql_query("ROOLBACK");
//        exit;
//    }

    //update user
    $user_update_sql = "update user set points = points + " . $point . " where id = " . $user['id'];
    fwrite($log_handle, $email . ": " . $user_update_sql . "\r\n");
//    $user_flag = mysql_query($user_update_sql);
//    if (!$user_flag) {
//        mysql_query("ROOLBACK");
//        exit;
//    }

    //insert point_history00
    $create_time = date('Y-m-d H:i:s');

    $ph_sql = "insert into point_history0" . ($user['id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $user['id'] . "," . $point . "," . $task_history['category_type'] . ",'" . $create_time . "')";
    fwrite($log_handle, $email . ": " . $ph_sql . "\r\n");
//    $ph_flag = mysql_query($ph_sql);
//    if (!$ph_flag) {
//        mysql_query("ROOLBACK");
//        exit;
//    }

    fwrite($log_handle, "\r\n\r\n");

}

mysql_query("COMMIT"); //执行事务

mysql_close($link);
fwrite($log_handle, "完成了" . "\r\n");
fclose($log_handle);
echo "执行完成!";
exit;
?>
