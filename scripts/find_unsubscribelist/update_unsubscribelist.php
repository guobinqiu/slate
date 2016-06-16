<?php
$path = __DIR__; //取得当前路径
echo $path;
$file_unsubscribe = $path . "/unsubscribe.csv";

$email_list = [];


read_unsubscribe($file_unsubscribe, $email_list);

print 'Memory used : ' . memory_get_usage()/1024/1024 .'MB'. PHP_EOL;

update_unsubscribe($email_list);

echo "执行完成!" . PHP_EOL;
exit;

function read_unsubscribe($file_path, &$email_list){
    
    $count = 0;
    foreach(file($file_path) as $line){
        $count++;
        $data = str_getcsv($line);

        if(sizeof($data) < 2){
            continue;
        }

        //print implode(",", $data) . PHP_EOL;
        $email_to = $data[0];
        $status = $data[1];

        if(!is_valid_email($email_to)){
            print 'Not a valid email ' . $email_to . PHP_EOL;
            continue;
        }

        if(isset($email_list[$email_to])){

        } else {
            $email_list[$email_to]['status'] = $status;
        }
    }
}


function update_unsubscribe(&$email_list){
    $link = mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error() . PHP_EOL);
    mysql_select_db('jili_db') or die('Could not select database');
    mysql_set_charset("UTF8", $link);

    $created_date = date("Y-m-d H:i:s");

    $insert_count = 0;

    foreach ($email_list as $key => $value) {
        // 通过email地址去找用户id
        $query = "SELECT u.id FROM user u where u.email = '" . $key . "'";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error() . PHP_EOL);
        $row = mysql_fetch_row($result);
        $user_id = $row[0];

        if(!$user_id){
            // 如果没找到id，说明这个用户已经挂了，直接处理下一个
            print 'email=' . $key . ' not found in user table' . PHP_EOL;
            continue;
        }
        // 如果找到id，查找user_unsubscribe表中是否已经有这个用户
        $query = "SELECT ueu.id FROM user_edm_unsubscribe ueu where ueu.user_id = '" . $user_id . "'";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error() . PHP_EOL);
        $row = mysql_fetch_row($result);
        $un_id = $row[0];

        if($un_id){
            // 这个用户已经在不发邮件的名单里了，直接处理下一个
            print 'user_id=' . $user_id . ' already in user_edm_unsubscribe table' . PHP_EOL;
            continue;
        }

        // 将这个用户记录到不发邮件的名单中
        $query = "INSERT INTO user_edm_unsubscribe (user_id, created_time) VALUES ( '" . $user_id . "', '" . $created_date . "')";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error() . PHP_EOL);
        if($result){
            mysql_query("COMMIT");
            $insert_count ++;
        }
        if($insert_count % 5000 === 0){
            print 'Add ' . $insert_count .' record into user_edm_unsubscribe table' . PHP_EOL;
        }
    }

    print 'Totally add ' . $insert_count .' record into user_edm_unsubscribe table' . PHP_EOL;
}


function is_valid_email($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}


/*

    /*
    $contents = null;
    $file_handle = fopen($file_path, "r");
    if ($file_handle !== FALSE) {
        while ($line = fgetcsv($file_handle)) {
            print implode(",", $line) ; 
            $contents[] = $line;
        }
    } else {
        return false;
    }
    fclose($file_handle);
    return $contents;
    */


/*
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

*/


?>
