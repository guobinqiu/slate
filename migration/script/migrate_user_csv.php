<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');
include_once ('CsvReader.php');

$import_path = IMPORT_PATH;
$export_path = EXPORT_PATH;
$log_path = LOG_PATH;

// ini_set("memory_limit",-1);


FileUtil::writeContents($log_path, date('c') . " start!\r\n\r\n");
echo date('c') . " start!\r\n\r\n";

// import file : wenwen
$panelist_file = $import_path . "/panelist.csv";
$panelist_detail_file = $import_path . "/panel_91wenwen_panelist_detail.csv";
$panelist_profile_file = $import_path . "/panel_91wenwen_panelist_profile.csv";
$panelist_profile_image_file = $import_path . "/panel_91wenwen_panelist_profile_image.csv";
$panelist_mobile_number_file = $import_path . "/panel_91wenwen_panelist_mobile_number.csv";
$panelist_point_file = $import_path . "/panel_91wenwen_panelist_point.csv";
$panelist_sina_connection_file = $import_path . "/panel_91wenwen_panelist_sina_connection.csv";
$pointexchange_91jili_account_file = $import_path . "/panel_91wenwen_pointexchange_91jili_account.csv";
$vote_answer_file = $import_path . "/" . VOTE_ANSWER . ".csv";
$panelist_91jili_connection_file = $import_path . "/panel_91wenwen_panelist_91jili_connection.csv";
$sop_respondent_file = $import_path . "/sop_respondent.csv";

// import file : jili
$user_file = $import_path . "/user.csv";
$user_wenwen_cross_file = $import_path . "/user_wenwen_cross.csv";
$weibo_user_file = $import_path . "/weibo_user.csv";
//todo: jili.migration_region_mapping 数据需要导出来
$migration_region_mapping_file = $import_path . "/migration_region_mapping.csv";

// export jili csv
$migrate_user_csv = $export_path . "/migrate_user.csv";
$migrate_user_wenwen_login_csv = $export_path . "/migrate_user_wenwen_login.csv";
$migrate_weibo_user_csv = $export_path . "/weibo_user.csv";
$migrate_vote_answer_csv = $export_path . "/migrate_vote_answer.csv";
$migrate_sop_respondent_csv = $export_path . "/migrate_sop_respondent.csv";

// $panelist_file_handle = fopen($panelist_file, "r");
// $panelist_sina_connection_file_handle = fopen($panelist_sina_connection_file, "r");
// $panelist_91jili_connection_file_handle = fopen($panelist_91jili_connection_file, "r");


//check file
$panelist_file_handle = FileUtil::checkFile($panelist_file);
// $panelist_detail_file_handle = FileUtil::checkFile($panelist_detail_file);
// $panelist_profile_file_handle = FileUtil::checkFile($panelist_profile_file);
// // $panelist_profile_image_file_handle = FileUtil::checkFile($panelist_profile_image_file);
// $panelist_mobile_number_file_handle = FileUtil::checkFile($panelist_mobile_number_file);
// $panelist_point_file_handle = FileUtil::checkFile($panelist_point_file);
// $panelist_sina_connection_file_handle = FileUtil::checkFile($panelist_sina_connection_file);
$pointexchange_91jili_account_file_handle = FileUtil::checkFile($pointexchange_91jili_account_file);
// $vote_answer_file_handle = FileUtil::checkFile($vote_answer_file);
$panelist_91jili_connection_file_handle = FileUtil::checkFile($panelist_91jili_connection_file);

$user_file_handle = FileUtil::checkFile($user_file);
$user_wenwen_cross_file_handle = FileUtil::checkFile($user_wenwen_cross_file);

//todo: get max user id


//遍历panelist表
$i = 0;
$both_cross_count = 0;
$both_email_count = 0;

try {
    while (($panelist_data = fgetcsv($panelist_file_handle, 4000, ",")) !== FALSE) {

        FileUtil::writeContents($log_path, "i:" . $i);

        if ($i == 1) {
            echo $i;
            FileUtil::writeContents($log_path, "panelist_data:" . var_dump($panelist_data));
        }

        if ($i == 0) {
            continue;
        }
        $panelist_id = $panelist_data[0];
        $jili_email = $panelist_email = $panelist_data[3];
        $j = 0;
        $k = 0;

        FileUtil::writeContents($log_path, "panelist_id:" . $panelist_id);
        FileUtil::writeContents($log_path, "panelist_email:" . $panelist_email);

        //遍历panel_91wenwen_panelist_91jili_connection表
        while (($jili_connection_data = fgetcsv($panelist_91jili_connection_file_handle, 2000, ",")) !== FALSE) {

            if ($j == 1) {
                echo $j;
                FileUtil::writeContents($log_path, "jili_connection_data:" . var_dump($jili_connection_data));
            }

            if ($j == 0) {
                continue;
            }
            if ($panelist_id == $jili_connection_data[0]) {
                $jili_cross_id = $jili_connection_data[1];

                FileUtil::writeContents($log_path, "jili_cross_id:" . $jili_cross_id);

                //遍历user_wenwen_cross表
                $m = 0;
                while (($user_wenwen_cross_data = fgetcsv($user_wenwen_cross_file_handle, 2000, ",")) !== FALSE) {

                    if ($m == 1) {
                        echo $m;
                        FileUtil::writeContents($log_path, "user_wenwen_cross_data:" . var_dump($user_wenwen_cross_data));
                    }

                    if ($m == 0) {
                        continue;
                    }
                    if ($jili_cross_id == $user_wenwen_cross_data[0]) {
                        $jili_email = $user_wenwen_cross_data[3];
                        FileUtil::writeContents($log_path, "jili_cross_id->jili email:" . $jili_email);
                        $both_cross_count++;
                        break;
                    }
                    $m++;
                }

                break;
            }
            $j++;
        }

        //遍历panel_91wenwen_pointexchange_91jili_account表
        while (($jili_account_data = fgetcsv($pointexchange_91jili_account_file_handle, 2000, ",")) !== FALSE) {

            if ($k == 1) {
                echo $k;
                FileUtil::writeContents($log_path, "jili_account_data:" . var_dump($jili_account_data));
            }

            if ($k == 0) {
                continue;
            }
            if ($panelist_id == $jili_account_data[0]) {
                $jili_email = $jili_account_data[1];
                FileUtil::writeContents($log_path, "pointexchange-> jili_email:" . $jili_email);
                break;
            }

            $k++;
        }

        //遍历jili user 表
        $both_email_count = $both_email_count + fetch_jili_user($jili_email, $log_path);

        $i++;
    }
} catch (Exception $e) {
    FileUtil::writeContents($log_path, "Exception:" . $e->getMessage());
}

FileUtil::writeContents($log_path, "both_cross_count:" . $both_cross_count);
FileUtil::writeContents($log_path, "both_email_count:" . $both_email_count);

//遍历jili user 表
function fetch_jili_user($email, $log_path)
{
    $n = 0;
    $both_email_count = 0;

    $user_file = $import_path . "/user.csv";
    $user_file_handle = FileUtil::checkFile($user_file);

    while (($user_data = fgetcsv($user_file_handle, 2000, ",")) !== FALSE) {

        if ($n == 1) {
            echo $n;
            FileUtil::writeContents($log_path, "user_data:" . var_dump($user_data));
        }

        if ($n == 0) {
            continue;
        }
        if ($email == $jili_account_data[1]) {
            //todo:生成新的csv文件：拥有两边账号，取问问数据
            //todo:删除user csv文件中该行
            $both_email_count++;

            FileUtil::writeContents($log_path, "wenwen->jili_email_count:" . $email);
        }
        $n++;
    }

    return $both_email_count;
}

//number one paged
// $per = 1000;


// $csvreader_panelist = new CsvReader($panelist_file);
// $panelist_number = $csvreader_panelist->get_lines();
// $panelist_page = $panelist_number / $per;
// for ($i = 0; $i < $panelist_page + 1; $i++) {
//     $panelist_data = $csvreader_panelist->get_data(10, $i * $per);


//     //1.cross_id是否绑定过积粒账号


//     //2.panel_91wenwen_pointexchange_91jili_account.91jili_email 是否兑换过积粒


//     //3.panelist.email email在积粒中是否存在
// }


FileUtil::writeContents($log_path, date('c') . " end!");

echo date('c') . " end!\r\n\r\n";
exit();
?>
<!--
#import wenwen csv
#$panelist : "id","panel_region_id","panel_id","email","login_id","login_password","login_password_crypt_type","login_password_salt","updated_at","created_at","created_remote_addr","created_user_agent","login_valid_flag","sex_code","birthday","panelist_status","campaign_code","last_login_time"
#$panelist_detail :"panelist_id","name_first","name_middle","name_last","furigana_first","furigana_middle","furigana_last","age","zip1","zip2","address1","address2","address3","home_type_code","home_year","tel1","tel2","tel3","tel_mobile1","tel_mobile2","tel_mobile3","mobile_number","marriage_code","child_code","child_num","income_family_code","income_personal_code","job_code","industry_code","work_section_code","graduation_code","industry_code_family","internet_starttime_code","internet_usetime_code","last_answer_date","updated_at","created_at"
#$panelist_profile :"id","panelist_id","nickname","show_sex","show_birthday","biography","hobby","fav_music","monthly_wish","website_url","updated_at","created_at"
#$panelist_profile_image :
#$panelist_mobile_number :"panelist_id","mobile_number","status_flag","updated_at","created_at"
#$panelist_point :"panelist_id","point_value","last_add_time","last_add_log_yyyymm","last_add_log_id","last_active_time","updated_at","created_at"
#$panelist_sina_connection :"panelist_id","sina_id","access_token","access_token_secret","is_registration","created_remote_addr","updated_at","created_at"
#$pointexchange_91jili_account :"panelist_id","jili_email","status_flag","stash_data","updated_at","created_at"
#$vote_answer :"id","panelist_id","vote_id","answer_number","updated_at","created_at"
#$panelist_91jili_connection : panelist_id, jili_id, status_flag, stash_data, updated_at, created_at

//todo: sop_respondent 应该迁移，如何处理： To mapping 91ww panelist_id to sop_panelist_id
# id, panelist_id, status_flag, stash_data, updated_at, created_at


#import jili csv
#$user :"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
#$user_wenwen_cross :"id","user_id","created_at","email"
#$weibo_user :"id","user_id","open_id","regist_date"
#$migration_region_mapping :  region_id, province_id, city_id

#export jili csv
#$migrate_user_csv :# id, user_id, login_password_salt, login_password_crypt_type, login_password
#$migrate_user_wenwen_login_csv :# id, user_id, login_password_salt, login_password_crypt_type, login_password
#$migrate_weibo_user_csv :"id","user_id","open_id","regist_date"
#$migrate_vote_answer_csv : id, user_id, vote_id, answer_number, updated_at, created_at




#$data = FileUtil::csv_get_lines($panelist_file, 100, 2000);


todo: 需要注意的地方
1. 时区问题，取自问问db中的时间是否应该减去1小时
2. set_time_limit(0)
-->
