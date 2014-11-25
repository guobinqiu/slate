<?php
$filename = "D:/xampp/htdocs/PointMedia/scripts/fix_issue538/import.csv";
$out_file = "D:/xampp/htdocs/PointMedia/scripts/fix_issue538/export.csv";

$file_handle = fopen($filename, "r");
$out_handle = fopen($out_file, "w+");
$contents = null;
if ($file_handle !== FALSE) {
    while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
        $contents[] = $data;
    }
}
fclose($file_handle);

fputcsv($out_handle, array (
    '91jili_email',
    '91jili_cross_id'
));

$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_db') or die('Could not select database');
mysql_set_charset("UTF8", $link);

unset ($contents[0]);
foreach ($contents as $content) {
    $email = $content[0];
    $query = "SELECT user_wenwen_cross.id FROM user_wenwen_cross inner join user on user_wenwen_cross.user_id = user.id where user.email = '" . $email . "'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());

    $row = mysql_fetch_row($result);
    if ($row) {
        fputcsv($out_handle, array (
            $email,
            $row[0]
        ));
    } else {
        fputcsv($out_handle, array (
            $email,
            ""
        ));
    }
}

mysql_close($link);
fclose($out_handle);
echo "执行完成!";
exit;
?>
