<?php
set_time_limit(0);
$link = mysql_connect('localhost', 'root', 'ecnavi') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_db') or die('Could not select database');
mysql_query("set names utf-8");

$start = "2014-05-01 00:00:00";
$end = "2014-05-31 23:59:59";

//生成csv文件
$output_filename = "/data/91jili/logs/admin/mayEvents" . date("YmdHis") . ".csv";
$handle = fopen($output_filename, 'a');
$csvline = array ();

//csv file title
$title = joinCsv(array (
    'user_id',
    'email',
    'point',
    'task_name',
    'category_type',
    'task_type'
));
fwrite($handle, $title . "\n");

//category_type 17-OfferWow体验广告    18-Offer99体验广告
for ($i = 0; $i < 10; $i++) {

    echo "i:" . $i . "\n";

    $query = "select user_id, count(user_id) as count, sum(point) as points from task_history0" . $i . " t " .
    "where ((t.category_type = 17 and status = 3) or (t.category_type = 18)) and  t.date >= '" . $start . "' and t.date <= '" . $end . "' " .
    " group by user_id having count >= 3 ";

    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $user['user_id'] = $row['user_id'];
        $user['email'] = "";
        $user['point'] = round($row['points'] * (1 / 6));
        $user['task_name'] = "5月份活动送积分";
        $user['category_type'] = "21";
        $user['task_type'] = "4";

        $content = joinCsv($user);
        fwrite($handle, $content . "\n");
    }
    mysql_free_result($result);
}
fclose($handle);
mysql_close($link);
echo "ok";

function joinCsv($row) {
    $csvline = '';
    $csv_array = array ();
    foreach ($row as $column) {
        $csv_array[] = (preg_match('/[\"]/', $column)) ? '"' . preg_replace('/\"/', '""', $column) . '"' : '"' . $column . '"';
    }
    $csvline .= implode(',', $csv_array);
    return $csvline;
}
?>
