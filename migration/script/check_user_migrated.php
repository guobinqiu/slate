<?php
include 'migrate_function.php';
include 'Constants.php';

// dump migarte_User
function do_process() 
{

// build 1 index.
$merged_user_csv_file_hanlder = fopen(EXPORT_PATH . '/' . Constants::$migrate_user_name, 'r');

$merged_user_csv_indexes = build_file_index($merged_user_csv_file_hanlder, 'region_id');


// load all the migrated csv 
// load all login info

// user_ww only

// user_jili only

// build panelist_user_index
}

do_process();
