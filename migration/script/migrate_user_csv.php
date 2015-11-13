<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');

$import_path = IMPORT_PATH;
$export_path = EXPORT_PATH;

// import file : wenwen
$panelist_file = $import_path . "/panelist.csv";
$panelist_detail_file = $import_path . "/panel_91wenwen_panelist_detail.csv";
$panelist_profile_file = $import_path . "/panel_91wenwen_panelist_profile.csv";
//todo: panel_91wenwen_panelist_profile_image 数据需要导出来
$panelist_profile_image_file = $import_path . "/panel_91wenwen_panelist_profile_image.csv";
$panelist_mobile_number_file = $import_path . "/panel_91wenwen_panelist_mobile_number.csv";
$panelist_point_file = $import_path . "/panel_91wenwen_panelist_point.csv";
$panelist_sina_connection_file = $import_path . "/panel_91wenwen_panelist_sina_connection.csv";
$pointexchange_91jili_account_file = $import_path . "/panel_91wenwen_pointexchange_91jili_account.csv";
$vote_answer_file = $import_path . "/" . VOTE_ANSWER . ".csv";
//todo: wenwen.panel_91wenwen_panelist_91jili_connection  数据需要导出来
$panelist_91jili_connection_file = $import_path . "/panel_91wenwen_panelist_91jili_connection.csv";

//todo: sop_respondent 应该迁移，如何处理： To mapping 91ww panelist_id to sop_panelist_id
# id, panelist_id, status_flag, stash_data, updated_at, created_at


// import file : jili
$user_file = $import_path . "/user.csv";
$user_wenwen_cross_file = $import_path . "/user_wenwen_cross.csv";
$weibo_user_file = $import_path . "/weibo_user.csv";
//todo: jili.migration_region_mapping 数据需要导出来
$migration_region_mapping_file = $import_path . "/migration_region_mapping.csv";

// get file content ： wenwen
$panelist = FileUtil::readCsvContent($panelist_file);
$panelist_detail = FileUtil::readCsvContent($panelist_detail_file);
$panelist_profile = FileUtil::readCsvContent($panelist_profile_file);
$panelist_profile_image = FileUtil::readCsvContent($panelist_profile_image_file);
$panelist_mobile_number = FileUtil::readCsvContent($panelist_mobile_number_file);
$panelist_point = FileUtil::readCsvContent($panelist_point_file);
$panelist_sina_connection = FileUtil::readCsvContent($panelist_sina_connection_file);
$pointexchange_91jili_account = FileUtil::readCsvContent($pointexchange_91jili_account_file);
$vote_answer = FileUtil::readCsvContent($vote_answer_file);
$panelist_91jili_connection = FileUtil::readCsvContent($panelist_91jili_connection_file);

// get file content ： jili
$user = FileUtil::readCsvContent($user_file);
$user_wenwen_cross = FileUtil::readCsvContent($user_wenwen_cross_file);
$weibo_user = FileUtil::readCsvContent($weibo_user_file);
$migration_region_mapping = FileUtil::readCsvContent($migration_region_mapping_file);

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


#import jili csv
#$user :"id","email","pwd","is_email_confirmed","is_from_wenwen","wenwen_user","token","nick","sex","birthday","tel","is_tel_confirmed","province","city","education","profession","income","hobby","personalDes","identity_num","reward_multiple","register_date","last_login_date","last_login_ip","points","delete_flag","is_info_set","icon_path","uniqkey","token_created_at","origin_flag","created_remote_addr","created_user_agent","campaign_code","password_choice"
#$user_wenwen_cross :"id","user_id","created_at"
#$weibo_user :"id","user_id","open_id","regist_date"
#$migration_region_mapping :  region_id, province_id, city_id


// export jili csv
$migrate_user_csv = $export_path . "/migrate_user.csv";
$migrate_user_wenwen_login_csv = $export_path . "/migrate_user_wenwen_login.csv";
$migrate_weibo_user_csv = $export_path . "/weibo_user.csv";
$migrate_vote_answer_csv = $export_path . "/migrate_vote_answer.csv";

#$migrate_user_csv :# id, user_id, login_password_salt, login_password_crypt_type, login_password
#$migrate_user_wenwen_login_csv :# id, user_id, login_password_salt, login_password_crypt_type, login_password
#$migrate_weibo_user_csv :"id","user_id","open_id","regist_date"
#$migrate_vote_answer_csv : id, user_id, vote_id, answer_number, updated_at, created_at


//todo: 需要注意的地方
// 1. 时区问题，取自问问db中的时间是否应该减去1小时


echo "\r\n\r\n" . date('c') . "   end!\r\n\r\n";
exit();
?>
