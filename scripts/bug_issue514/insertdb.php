<?php
$log_file = "D:/xampp/htdocs/PointMedia/scripts/bug_issue514/log_file_insert.txt";
$log_handle = fopen($log_file, "a+");

$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_2014-09-23') or die('Could not select database');
mysql_set_charset("UTF8", $link);

mysql_query("BEGIN"); //开始一个事务
mysql_query("SET AUTOCOMMIT=0"); //设置事务不自动commit

$count = 0;
$total = 0;

for ($i = 0; $i < 10; $i++) {
    $query = "SELECT * , count( user_id ) count
                FROM point_history0" . $i . "
                WHERE reason =13
                AND create_time LIKE '2014-09-22%'
                GROUP BY user_id, point_change_num
                HAVING count >1 ";
    fwrite($log_handle, $query . "\r\n");
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    while ($value = mysql_fetch_assoc($result)) {

        echo "<pre>";
        print_r($value);

        $count = $value['count'];
        if ($count != 2) {
            fwrite($log_handle, "count!=2 需要执行两次insertDb  count:" . ($count) . "  user_id:" . $value['user_id'] . "\r\n");
        }

        if ($count == 4) {
            $roolback = insertDb($log_handle, $value, $total);
            if ($roolback) {
                mysql_query("ROOLBACK");
                fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                exit;
            }

            $roolback = insertDb($log_handle, $value, $total);
            if ($roolback) {
                mysql_query("ROOLBACK");
                fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                exit;
            }
        } else {
            $roolback = insertDb($log_handle, $value, $total);
            if ($roolback) {
                mysql_query("ROOLBACK");
                fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                exit;
            }
        }

        fwrite($log_handle, $value['user_id'] . "修复成功，米粒：" . $value['point_change_num'] . "\r\n");
    }

}

mysql_query("COMMIT"); //执行事务

mysql_close($link);
fwrite($log_handle, "执行完成\r\n重复记录总数：$total" . "\r\n");
fclose($log_handle);
echo "执行完成!";
exit;

function insertDb($log_handle, $value, & $total) {

    $roolback = false;
    $total++;

    //update point_history00
    $ph_sql = "insert into point_history0" . ($value['user_id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $value['user_id'] . "," . (- $value['point_change_num']) . ",13,'" . $value['create_time'] . "')";
    fwrite($log_handle, $value['user_id'] . ": " . $ph_sql . "\r\n");
    $ph_flag = mysql_query($ph_sql);
    if (!$ph_flag) {
        $roolback = true;
        return $roolback;
    }

    //insert send_message00
    $title = "重复数据修改";
    $content = '亲爱的<br/><br/>您好！您在9月19日申请了91问问积分兑换米粒，积粒网于9月22日进行米粒发放。因系统原因，造成数据重复，现已修改。对于给您造成困扰，深表歉意。<br/><br/>如有问题，请联系我们的网站客服人员。<br/><a href="https://www.91wenwen.net/support/" target="_blank">https://www.91wenwen.net/support/</a><br/><br/>积粒网运营中心';
    $m_sql1 = "insert into send_message0" . ($value['user_id'] % 10) . "(sendFrom,sendTo,title,content,createtime,read_flag,delete_flag) values (0," . $value['user_id'] . ",'" . $title . "','" . $content . "','" . $value['create_time'] . "',0,0)";
    fwrite($log_handle, $value['user_id'] . ": " . $m_sql1 . "\r\n");
    $sm_flag = mysql_query($m_sql1);
    if (!$sm_flag) {
        $roolback = true;
        return $roolback;
    }

    return $roolback;
}
?>
