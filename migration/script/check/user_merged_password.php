<?php

include 'config.php';
include 'migrate_function.php';
include 'Constants.php';

// dump migarte_User
function do_process() 
{

    // merged user index 
  
    // ww_panelist_file hanlder 
    // jl_user_file hanlder 

    global $panelist_file_handle;
    global $user_file_handle;
    $user_indexs = build_file_index($user_file_handle, 'email');
    $panelist_indexs = build_file_index($panelist_file_handle, 'email');

    // load all the migrated csv 
    $merged_user_file_hanlder = fopen(EXPORT_PATH . '/' . Constants::$migrate_user_name, 'r');
    $merged_user_indexes = build_file_index($merged_user_file_hanlder, 'emai');

    foreach($merged_user_indexes as $email_merged => $merged_user_file_pointer) {
        use_file_index($merged_user_indexes ,   );
                    $user_row_merged = use_file_index($user_indexs, strtolower($cross_found['email']), $user_file_handle);

    }

    // load all login info

    // user_ww only

    // user_jili only

    // build panelist_user_index
}

do_process();
