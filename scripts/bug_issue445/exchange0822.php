<?php
$filename = "scripts/bug/import0822.csv";
$log_file = "scripts/bug/log_file.txt";
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

    $query = "SELECT id, email, points FROM user WHERE email = '" . $value[0] . "'";
    fwrite($log_handle, $value[0] . ": " . $query . "\r\n");
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $user = mysql_fetch_assoc($result);

    if ($user['points'] >= $value[1]) {

        //update user
        $user_sql = "update user set points = points - " . $value[1] . " where id = " . $user['id'];
        fwrite($log_handle, $value[0] . ": " . $user_sql . "\r\n");
        $user_flag = mysql_query($user_sql);
        if (!$user_flag) {
            mysql_query("ROOLBACK");
            exit;
        }

        //update point_history00
        $create_time = date('Y-m-d H:i:s');

        $ph_sql = "insert into point_history0" . ($user['id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $user['id'] . "," . (- $value[1]) . ",12,'" . $create_time . "')";
        fwrite($log_handle, $value[0] . ": " . $ph_sql . "\r\n");
        $ph_flag = mysql_query($ph_sql);
        if (!$ph_flag) {
            mysql_query("ROOLBACK");
            exit;
        }

        //update send_message00
        $m_sql1 = "select id from send_message0" . ($user['id'] % 10) . " where sendTo = " . $user['id'] . " and createtime like '2014-08-18%' and title = '手机费兑换失败' ";
        fwrite($log_handle, $value[0] . ": " . $m_sql1 . "\r\n");

        $sm_res = mysql_query($m_sql1) or die('Query failed: ' . mysql_error());
        $title = "手机费兑换成功";
        $content = '亲爱的<br/><br/>您好！您的手机费兑换申请已经处理成功，请及时查看您的手机余额。<br/><br/>如有问题，请联系我们的网站客服人员。<br/><a href="https://www.91wenwen.net/support/" target="_blank">https://www.91wenwen.net/support/</a><br/><br/>积粒网运营中心';
        while ($sm = mysql_fetch_assoc($sm_res)) {
            $sm_update = "update send_message0" . ($user['id'] % 10) . " set title = '" . $title . "', content = '" . $content . "'";
            fwrite($log_handle, $value[0] . ": " . $sm_update . "\r\n");
            $sm_update_flag = mysql_query($sm_update);
            if (!$sm_update_flag) {
                mysql_query("ROOLBACK");
                exit;
            }
        }

        //update points_exchange
        $ex_sql = "SELECT id FROM points_exchange where user_id = " . $user['id'] . " and finish_date like '2014-08-18%' and type=4 and status = 2";
        fwrite($log_handle, $value[0] . ": " . $ex_sql . "\r\n");
        $ex_res = mysql_query($ex_sql) or die('Query failed: ' . mysql_error());
        while ($ex = mysql_fetch_assoc($ex_res)) {
            $ex_update = "update points_exchange set status = 1 where id = " . $ex['id'];
            fwrite($log_handle, $value[0] . ": " . $ex_update . "\r\n");
            $ex_update_flag = mysql_query($ex_update);
            if (!$ex_update_flag) {
                mysql_query("ROOLBACK");
                exit;
            }
        }

        mysql_query("COMMIT"); //执行事务
        fwrite($log_handle, $user['email'] . ": 扣除成功" . "\r\n");

    } else {
        fwrite($log_handle, $user['email'] . ": 无法扣除" . "\r\n");
    }

}

mysql_close($link);
fwrite($log_handle, "完成了" . "\r\n");
fclose($log_handle);
echo "执行完成!";
exit;
?>
