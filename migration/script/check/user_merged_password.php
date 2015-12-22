<?php


include __DIR__.'/../config.php';
include __DIR__.'/../migrate_function.php';
include __DIR__.'/../Constants.php';

$log_handle = fopen(LOG_PATH.'/'.basename(__FILE__, '.php').'_'. date('Ymd_Hi').'.log', 'a');
// dump migarte_User
function do_process() 
{
    // merged user index 
  

    global $log_handle;
    // ww_panelist_file hanlder 
    // jl_user_file hanlder 

    global $panelist_file_handle;
    global $user_file_handle;

//    FileUtil::writeContents($log_handle, "max_user_id:" . $max_user_id);

    $panelist_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . '/panelist.csv');
    $user_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . '/user.csv');

    $user_indexs = build_file_index($user_file_handle, 'email');
    $panelist_indexs = build_file_index($panelist_file_handle, 'email');

    // load all the migrated csv 
    $merged_user_file_hanlder = fopen(EXPORT_PATH . '/' . Constants::$migrate_user_name, 'r');
    // user_wenwen_login indexes

    $merged_user_indexes = build_file_index($merged_user_file_hanlder, 'email');

    foreach($merged_user_indexes as $email_merged => $merged_user_file_pointer) {
        $email_merged = strtolower($email_merged);
        $user_row_merged = use_file_index($merged_user_indexes, $email_merged, $merged_user_file_hanlder);

// is FROM WENWEN

        if( $user_row_merged ) {
            if( isset($panelist_indexs[$email_merged] )  ) {

                // compare the PASSWORD_CHOICE & PASSWORD_DATA
                continue;

            } 

        } 

        // is FROM JILI

    }

    // load all login info
                // compare the PASSWORD_CHOICE & PASSWORD_DATA

    // user_ww only

    // user_jili only

    // build panelist_user_index
}

do_process();
