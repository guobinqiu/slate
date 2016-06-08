<?php
$path = __DIR__; //取得当前路径
echo $path;
$filename = $path . "/old-91wenwen_2016-05-01-2016-05-31-5315.csv";
$out_file = $path . "/old-91wenwen_ssi_reward_user_id.csv";

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
    'user_id',
    'email',
    'point',
    'task_name',
    'category_type',
    'task_type'
));

$link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('jili_db') or die('Could not select database');
mysql_set_charset("UTF8", $link);

unset ($contents[0]);
foreach ($contents as $content) {
    $ssi_respondent_id = substr($content[8], 5);
    //print $ssi_respondent_id . PHP_EOL;
    
    $query = "SELECT ssi_respondent.user_id FROM ssi_respondent where ssi_respondent.id = '" . $ssi_respondent_id . "'";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    $user_id = mysql_fetch_row($result);

    if ($user_id) {

        $query = "SELECT user.email FROM user where user.id = '" . $user_id[0] . "'";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        $email = mysql_fetch_row($result);
        if ($email) {
        fputcsv($out_handle, array (
            $user_id[0],
            $email[0],
            '180',
            'SSI问卷',
            '93',
            '9'
        ));
        }

    } else {
        print "SSI_Respondent_id not found. [" . ssi_respondent_id . "]" . PHP_EOL;
    }
    
}

mysql_close($link);
fclose($out_handle);
echo "执行完成!" . PHP_EOL;
exit;
?>
