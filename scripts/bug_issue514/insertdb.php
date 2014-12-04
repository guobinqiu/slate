<?php
$log_file = "D:/xampp/htdocs/PointMedia/scripts/bug_issue514/log_file_insert.txt";
$log_handle = fopen($log_file, "w");

$log_file2 = "D:/xampp/htdocs/PointMedia/scripts/bug_issue514/log_file_user.txt";
$log_handle2 = fopen($log_file2, "w");

$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_2014-09-23') or die('Could not select database');
mysql_set_charset("UTF8", $link);

mysql_query("BEGIN"); //开始一个事务
mysql_query("SET AUTOCOMMIT=0"); //设置事务不自动commit

$count = 0;
$total = 0;
$total_222 = 0;
$total_333 = 0;
$total_4 = 0;

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
        $total_222++;
        $count = $value['count'];
        fwrite($log_handle, " count:" . ($count) . "  user_id:" . $value['user_id'] . "\r\n");

        $checkBefore = checkPointBeforeStart($log_handle, $value, $count);
        if (!$checkBefore) {
            $total_333++;
            echo $value['user_id'] . "<br>";
            continue;
        }

        if ($count == 2 || $count == 3) {
            $roolback = insertDb($log_handle, $log_handle2, $value, $total);
            if ($roolback) {
                mysql_query("ROOLBACK");
                fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                exit;
            }
        } else
            if ($count == 4) {

                $total_4++;

                $roolback = insertDb($log_handle, $log_handle2, $value, $total);
                if ($roolback) {
                    mysql_query("ROOLBACK");
                    fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                    exit;
                }

                $roolback = insertDb($log_handle, $log_handle2, $value, $total);
                if ($roolback) {
                    mysql_query("ROOLBACK");
                    fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
                    exit;
                }
            } else {
                fwrite($log_handle, "count 在2,3,4之外，没有执行  count:" . ($count) . "  user_id:" . $value['user_id'] . "\r\n");
            }

        $checkAfter = checkPointAfterEnd($log_handle, $value);
        if ($checkAfter) {
            fwrite($log_handle, $value['user_id'] . "修复成功，米粒：" . $value['point_change_num'] . "\r\n");
        } else {
            fwrite($log_handle, $value['user_id'] . "修复失败，米粒：" . $value['point_change_num'] . "\r\n");
        }

    }

}
echo "总的：" . $total_222 . "<br>";
echo "可执行：" . $total . "<br>";
echo "不可执行:" . $total_333 . "<br>";
echo "coount=4:" . $total_4 . "<br>";

mysql_query("COMMIT"); //执行事务

mysql_close($link);
fwrite($log_handle, "执行完成\r\n重复记录总数：$total" . "\r\n");
fclose($log_handle);
fclose($log_handle2);
echo "执行完成!";
exit;

function checkPointBeforeStart($log_handle, $value, $count) {

    $user_sql = "select points from user where id = " . $value['user_id'];
    $result = mysql_query($user_sql);
    $user = mysql_fetch_assoc($result);

    $point_history_sql = "select sum(point_change_num) sum from point_history0" . ($value['user_id'] % 10) . " where user_id = " . $value['user_id'];
    $result = mysql_query($point_history_sql);
    $point_history = mysql_fetch_assoc($result);

    $point_change_num = $value['point_change_num'];
    if ($count == 4) {
        $point_change_num = $point_change_num * 2;
    }
    if (($point_history['sum'] - $point_change_num) == $user['points']) {
        return true;
    } else {
        fwrite($log_handle, $value['user_id'] . "分数不平衡不能执行\r\n");
        return false;
    }
}

function checkPointAfterEnd($log_handle, $value) {

    $user_sql = "select points from user where id = " . $value['user_id'];
    $result = mysql_query($user_sql);
    $user = mysql_fetch_assoc($result);

    $point_history_sql = "select sum(point_change_num) sum from point_history0" . ($value['user_id'] % 10) . " where user_id = " . $value['user_id'];
    $result = mysql_query($point_history_sql);
    $point_history = mysql_fetch_assoc($result);

    if (($point_history['sum'] - $user['points']) == 0) {
        fwrite($log_handle, "执行后分数平衡了\r\n");
        return true;
    } else {
        fwrite($log_handle, "执行后分数不平衡\r\n");
        return false;
    }
}

function insertDb($log_handle, $log_handle2, $value, & $total) {

    $roolback = false;
    $total++;

    $csv = array ();
    $csv['user_id'] = $value['user_id'];
    $csv['point_change_num'] = $value['point_change_num'];
    fputcsv($log_handle2, $csv) . "\r\n";

    //update point_history00
    $ph_sql = "insert into point_history0" . ($value['user_id'] % 10) . " (user_id,point_change_num,reason,create_time) values (" . $value['user_id'] . "," . (- $value['point_change_num']) . ",13,'" . $value['create_time'] . "')";
    fwrite($log_handle, $value['user_id'] . ": " . $ph_sql . "\r\n");
    //    $ph_flag = mysql_query($ph_sql);
    //    if (!$ph_flag) {
    //        $roolback = true;
    //        return $roolback;
    //    }

    return $roolback;
}
?>
