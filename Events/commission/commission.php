<?php
header("Content-type: text/html; charset=utf-8");
set_time_limit(0);
$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_db') or die('Could not select database');
//mysql_query("set names utf-8");
mysql_set_charset("UTF8", $link);

$output_filename = "D:/xampp/htdocs/PointMedia/commission" . date("YmdHis") . ".csv";
$handle = fopen($output_filename, 'a');

$query = "SELECT id,web_name FROM emar_websites_croned a where  not exists (select * from emar_activity_commission  b where a.web_name = b.mall_name) ";
//echo $query . "<br>";

$websites = array ();
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $website = array ();
    $website['id'] = $row['id'];
    $website['web_name'] = $row['web_name'];

    $name = mbStrSplit($row['web_name'], 1);
    if ($name[0]) {

        $query1 = "SELECT distinct mall_name FROM emar_activity_commission where mall_name like '" . $name[0] . "%'";
//        echo $query1 . "<br>";
        $result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());
        $mall = array ();
        while ($row1 = mysql_fetch_assoc($result1)) {
            $mall[] = $row1['mall_name'];
        }
        $website['mall_name'] = $mall;
    }

    $websites[] = $website;

    $csv['web_name'] = $website['web_name'];
    $csv['mall_name'] = implode(",", $mall);

    $content = joinCsv($csv);
    fwrite($handle, $content . "\n");

}
mysql_free_result($result);

//echo "<pre>";
//print_r(count($websites));
//
//echo "<pre>";
//print_r($websites);

fclose($handle);
mysql_close($link);
echo "ok";

function mbStrSplit($string, $len = 1) {
    $start = 0;
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, $start, $len, "utf8");
        $string = mb_substr($string, $len, $strlen, "utf8");
        $strlen = mb_strlen($string);
    }
    return $array;
}

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
