<?php
header("Content-type:text/html;charset=utf-8");
$execute = false;

$dsn = "mysql:host=localhost;dbname=jili_2014-09-23";
$dbh = new PDO($dsn, 'root', '');
$dbh->exec("SET NAMES 'utf8';");

$filename = __DIR__ . "/import550.csv";
$log_file = __DIR__ . "/log_file.sql";



$file_handle = fopen($filename, "r");
$log_handle = fopen($log_file, "w");
$contents = null;
if ($file_handle !== FALSE) {
    while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
        $contents[] = $data;
    }
}
fclose($file_handle);

$dbh->beginTransaction();

try {

    foreach ($contents as $value) {

        $date = $value[0];
        $email = $value[1];
        $point = $value[2];

        //检索user
        $user_sql = "SELECT id,email,points FROM user WHERE email = '" . $email . "'";
        fwrite($log_handle, $email . ": " . $user_sql . "\r\n");
        $query = $dbh->prepare($user_sql);
        $query->execute() or die(print_r($dbh->errorInfo(), true));
        $user = $query->fetch(PDO :: FETCH_ASSOC);
        fwrite($log_handle, "email: " . $user['email'] . ", user_id:" . $user['id'] . ", points: " . $user['points'] . "\r\n");

        //检索task_history
        $task_history_sql = "select * from task_history0" . ($user['id'] % 10) . " where user_id =" . $user['id'] . " and status = 2 and point = " . $point . " and task_name like '%亚马逊%' and `date` like '" . $date . "%'";
        fwrite($log_handle, $email . ": " . $task_history_sql . "\r\n");
        $query = $dbh->prepare($task_history_sql);
        $query->execute() or die(print_r($dbh->errorInfo(), true));
        $task_history = $query->fetch(PDO :: FETCH_ASSOC);
        fwrite($log_handle, "id:" . $task_history['id'] . ", category_type: " . $task_history['category_type'] . ", status: " . $task_history['status'] . ", task_name:" . $task_history['task_name'] . "\r\n");

        //update task_history
        $task_history_update_sql = "update task_history0" . ($user['id'] % 10) . " set status = 3 where id = " . $task_history['id'] . " limit 1";
        fwrite($log_handle, $email . ": " . $task_history_update_sql . "\r\n");
        if ($execute) {
            $count = $dbh->exec($task_history_update_sql);
            fwrite($log_handle, $email . ": count: " . $count . "\r\n");
        }

        //update user
        $user_update_sql = "update user set points = points + " . $point . " where id = " . $user['id'] . " limit 1";
        fwrite($log_handle, $email . ": " . $user_update_sql . "\r\n");
        if ($execute) {
            $count = $dbh->exec($user_update_sql);
            fwrite($log_handle, $email . ": count: " . $count . "\r\n");
        }

        //insert point_history00
        $create_time = date('Y-m-d H:i:s');

        $ph_sql = "insert into point_history0" . ($user['id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $user['id'] . "," . $point . "," . $task_history['category_type'] . ",'" . $create_time . "')";
        fwrite($log_handle, $email . ": " . $ph_sql . "\r\n");
        if ($execute) {
            $count = $dbh->exec($ph_sql);
            fwrite($log_handle, $email . ": count: " . $count . "\r\n");
        }

        fwrite($log_handle, "\r\n\r\n");

    }

    $dbh->commit();
    fwrite($log_handle, "Success!\r\n\r\n");

} catch (Exception $e) {
    $dbh->rollBack();
    fwrite($log_handle, "Failed: " . $e->getMessage() . "\r\n\r\n");
}

fclose($log_handle);
echo "\r\n\r\n" . date('c') . "   end!\r\n\r\n";
exit;
?>
