<?php
//INSERT INTO `jili_db`.`ad_category` (`id`, `category_name`, `asp`, `display_name`) VALUES ('91', 'system', NULL, '米粒误发修改');

$filename = "scripts/bug_issue502/repeat.csv";
$log_file = "scripts/bug_issue502/log_file.txt";
$file_handle = fopen($filename, "r");
$log_handle = fopen($log_file, "a+");
$contents = null;
if ($file_handle !== FALSE) {
    while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
        $contents[] = $data;
    }
}
fclose($file_handle);

$link = mysql_connect('localhost', 'user_name', 'password') or die('Could not connect: ' . mysql_error());
mysql_select_db('db_name') or die('Could not select database');
mysql_set_charset("UTF8", $link);

mysql_query("BEGIN"); //开始一个事务
mysql_query("SET AUTOCOMMIT=0"); //设置事务不自动commit

foreach ($contents as $value) {

    $query = "SELECT id, email, points FROM user WHERE id = " . $value[0];
    fwrite($log_handle, $value[0] . ": " . $query . "\r\n");
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $user = mysql_fetch_assoc($result);

    if ($user['points'] > 0) {

        if ($user['points'] < $value[1]) {
            $value[1] = $user['points'];
        }

        //update user
        $user_sql = "update user set points = points - " . $value[1] . " where id = " . $user['id'];
        fwrite($log_handle, $value[0] . ": " . $user_sql . ",现有米粒:" . $user['points'] . "\r\n");
        $user_flag = mysql_query($user_sql);
        if (!$user_flag) {
            mysql_query("ROOLBACK");
            exit;
        }

        //insert point_history00
        $create_time = date('Y-m-d H:i:s');

        $ph_sql = "insert into point_history0" . ($user['id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $user['id'] . "," . (- $value[1]) . ",91,'" . $create_time . "')";
        fwrite($log_handle, $value[0] . ": " . $ph_sql . "\r\n");
        $ph_flag = mysql_query($ph_sql);
        if (!$ph_flag) {
            mysql_query("ROOLBACK");
            exit;
        }

        //insert send_message00
        $title = "米粒误发修改成功";
        $content = '亲爱的<br/><br/>您好！您在9月19日申请了91问问积分兑换米粒，积粒网于9月22日进行米粒发放。因系统原因，造成米粒重复发放，现已修改。对于给您造成困扰，深表歉意。<br/><br/>如有问题，请联系我们的网站客服人员。<br/><a href="https://www.91wenwen.net/support/" target="_blank">https://www.91wenwen.net/support/</a><br/><br/>积粒网运营中心';
        $m_sql1 = "insert into send_message0" . ($user['id'] % 10) . "(sendFrom,sendTo,title,content,createtime,read_flag,delete_flag) values (0," . $user['id'] . ",'" . $title . "','" . $content . "','" . $create_time . "',0,0)";
        fwrite($log_handle, $value[0] . ": " . $m_sql1 . "\r\n");
        $sm_flag = mysql_query($m_sql1);
        if (!$sm_flag) {
            mysql_query("ROOLBACK");
            exit;
        }

        mysql_query("COMMIT"); //执行事务
        fwrite($log_handle, $user['email'] . ": 扣除成功，扣除米粒：" .$value[1]. "\r\n");

    } else {
        fwrite($log_handle, $user['email'] . ": 米粒少于0,无法扣除" . "\r\n");
    }

}

mysql_close($link);
fwrite($log_handle, "执行完成" . "\r\n");
fclose($log_handle);
echo "执行完成!";exit;
?>
