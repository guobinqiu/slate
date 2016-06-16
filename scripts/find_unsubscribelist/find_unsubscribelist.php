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


/*
class EmailList{
    private $EmailSendOpenCountList = array();

    public function addEmail($EmailSendOpenCount){
        $this->EmailSendOpenCountList[] = $EmailSendOpenCount;
    }

    public function getEmail($email){
        foreach($this->EmailSendOpenCountList as $EmailSendOpenCount){
            if($EmailSendOpenCount->getEmailAddress() === $email){
                return $EmailSendOpenCount;
            }
        }
        return false;
    }

    public function showList(){
        foreach($this->EmailSendOpenCountList as $EmailSendOpenCount){
            print $EmailSendOpenCount->getEmailAddress() . ' | send = ' . $EmailSendOpenCount->getSendCount() . PHP_EOL;
        }
    }

}


class EmailSendOpenCount {
    private $email_address;
    private $send_count = 0;
    private $open_count = 0;
    private $bounce_count = 0;
    private $spam_report_count = 0;

    public function __construct($email) {
        $this->email_address = $email;
    }

    public function setEmailAddress($email_address){
        $this->email_address = $email_address;
    }

    public function setSendCount($send_count){
        $this->send_count = $send_count;
    }

    public function setOpenCount($open_count){
        $this->open_count = $open_count;
    }

    public function setBounceCount($bounce_count){
        $this->bounce_count = $bounce_count;
    }

    public function setSpamReportCount($spam_report_count){
        $this->spam_report_count = $spam_report_count;
    }

    public function getEmailAddress(){
        return $this->email_address;
    }

    public function getSendCount(){
        return $this->send_count;
    }

    public function getOpenCount(){
        return $this->open_count;
    }

    public function getBounceCount(){
        return $this->bounce_count;
    }

    public function getSpamReportCount(){
        return $this->spam_report_count;
    }

    public function addSendCount(){
        $this->send_count += 1;
    }

    public function addOpenCount(){
        $this->open_count += 1;
    }

    public function addBounceCount(){
        $this->bounce_count += 1;
    }

    public function addSpamReportCount(){
        $this->spam_report_count += 1;
    }

    public function isUnsubscribe(){
        if($this->spam_report_count > 0){
            return true;
        }
        if($this->bounce_count > 0) {
            return true;
        }
        if($this->send_count > 4){
            if($this->open_count < 1){
                return true;
            }
        }
        return false;
    }
}






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
