<?php
$path = __DIR__; //取得当前路径
echo $path;
$file_sended = $path . "/sended.csv";
$file_opened = $path . "/opened.csv";
$file_bounce_spamed = $path . "/bounce.csv";
$file_unsubscribe = $path . "/unsubscribe.csv";

$email_list = [];

read_sended($file_sended, $email_list);

read_opened($file_opened, $email_list);

read_spam($file_bounce_spamed, $email_list);

print_result($email_list);

print 'Memory used : ' . memory_get_usage()/1024/1024 .'MB'. PHP_EOL;

write_unsubscribe($file_unsubscribe, $email_list);

echo "执行完成!" . PHP_EOL;
exit;

function read_sended($file_path, &$email_list){
    
    $count = 0;
    foreach(file($file_path) as $line){
        $count++;
        $data = str_getcsv($line);

        if(sizeof($data) < 5){
            continue;
        }

        //print implode(",", $data) . PHP_EOL;
        $email_to = $data[4];

        if(!is_valid_email($email_to)){
            print 'Not a valid email ' . $email_to . PHP_EOL;
            continue;
        }

        if(isset($email_list[$email_to])){
            $email_list[$email_to]['send']++;
        } else {
            $email_list[$email_to]['send'] = 1;
            $email_list[$email_to]['open'] = 0;
            $email_list[$email_to]['spam'] = 0;
        }
    }
}

function read_opened($file_path, &$email_list){
    foreach(file($file_path) as $line){
        $data = str_getcsv($line);

        if(sizeof($data) < 5){
            continue;
        }

        //print implode(",", $data) . PHP_EOL;
        $email_to = $data[4];

        if(!is_valid_email($email_to)){
            print 'Not a valid email ' . $email_to . PHP_EOL;
            continue;
        }

        if(isset($email_list[$email_to])){
            //print 'opened ' . $email_to . PHP_EOL;
            $email_list[$email_to]['open']++;
        } else {
            print 'Open without send ' . $email_to . PHP_EOL;
        }
    }
}

function read_spam($file_path, &$email_list){
    foreach(file($file_path) as $line){
        $data = str_getcsv($line);

        if(sizeof($data) < 5){
            continue;
        }

        //print implode(",", $data) . PHP_EOL;
        $email_to = $data[4];
        //$status = $data[];

        if(!is_valid_email($email_to)){
            print 'Not a valid email ' . $email_to . PHP_EOL;
            continue;
        }

        if(isset($email_list[$email_to])){
            //print 'opened ' . $email_to . PHP_EOL;
            $email_list[$email_to]['spam']++;
        } else {
            print 'Open without send ' . $email_to . PHP_EOL;
        }
    }
}

function print_result($email_list){
    $count_1 = 0;
    $count_5 = 0;
    $count_10 = 0;
    $open_1 = 0;
    $open_5 = 0;
    $spam_1 = 0;
    foreach($email_list as $key => $value){
        //print $key . '|send=' . $value['send'] . '|open=' . $value['open']. PHP_EOL;
        $count_1 ++;
        if($value['send'] > 4){
            $count_5 ++;
        }
        if($value['send'] > 9){
            $count_10 ++;
        }
        if($value['open'] > 0){
            $open_1++;
        }
        if($value['spam'] > 0){
            $spam_1++;
        }
    }

    print 'send times (1 - 4)=' . $count_1 . PHP_EOL;
    print 'send times (5 - 9)=' . $count_5 . PHP_EOL;
    print 'send times (10 - )=' . $count_10 . PHP_EOL;
    print 'open times (1 - )=' . $open_1 . PHP_EOL;
    print 'spam times (1 - )=' . $spam_1 . PHP_EOL;
}

function write_unsubscribe($file_path, &$email_list){
    $fp = fopen($file_path, 'w');
    foreach ($email_list as $key => $value) {
        if($value['spam'] > 1){
            fputcsv($fp, array($key, 'spam'));
            continue;
        }
        if($value['send'] > 4 && $value['open'] < 1){
            fputcsv($fp, array($key, 'unopened'));
            continue;
        }
    }
    fclose($fp);
}



function is_valid_email($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}



?>
