<?php
#ini_set('memory_limit', '2048M');

include ('config.php');
include ('FileUtil.php');
include ('Constants.php');
include ('migrate_function.php');

$log_handle = fopen(LOG_PATH.'/'.basename(__FILE__, '.php').'_'. date('Ymd_Hi').'.log', 'a');
echo date('Y-m-d H:i:s') . " start!\r\n\r\n";
FileUtil::writeContents($log_handle, "start!\r\n\r\n");

//export vote csv
exec('php migrate_vote_csv.php > vote.txt');

// initialise csv file handle, create index
initialise_csv();

/**
 * Import data process, and generate export data
 * @return void
 */
function do_process()
{
    global $log_handle;
    global $panelist_file_handle;
    global $user_file_handle;
    global $weibo_user_file_handle;
    global $user_wenwen_cross_file_handle;
    global $pointexchange_91jili_account_file_handle;
    global $panelist_91jili_connection_file_handle;
    global $panelist_sina_connection_file_handle;
    global $sina_connection_indexs;
    global $weibo_user_indexs;

    global $open_ids_dismatched;
    $cross_exist_count = 0;
    $exchange_exist_count = 0;
    $email_exist_count = 0;
    $weibo_exist_count = 0;
    $both_exist_count = 0; // cross_exist_count+exchange_exist_count+wenwen_email + weibo_exist_count
    $only_wenwen_count = 0;
    $only_jili_count = 0;
    // panel_91wenwen_panelist_91jili_connection 记录当前的connection csv handler中的行。
    $connection_current = array ();
    //创建索引
    $connection_indexes = build_key_value_index($panelist_91jili_connection_file_handle, 'panelist_id', 'jili_id');
    $cross_indexs = build_key_value_index($user_wenwen_cross_file_handle, 'id', 'email');
    $exchange_indexes =build_key_value_index($pointexchange_91jili_account_file_handle, 'panelist_id','jili_email');
    $user_indexs = build_file_index($user_file_handle, 'email');
    $user_indexs_by_id = build_key_value_index($user_file_handle,'id', 'email');
    $weibo_user_indexes_by_open_id = build_key_value_index($weibo_user_file_handle, 'open_id', 'user_id');

    // for backup the a_j 
    $both_but_diff_emails = array();

    //get max user id
    $max_user_id = get_max_user_id($user_file_handle);

    // array to store the relation between panelist_id and user_id
    $array_panelistid_userid = array();
    
    
    FileUtil::writeContents($log_handle, "max_user_id:" . $max_user_id);
    //遍历panelist表
    fgetcsv($panelist_file_handle, 2000);
    try {
        while (($panelist_row = fgetcsv($panelist_file_handle, 2048, ',','"','"')) !== false ) {

            $panelist_id = $panelist_row[0];

            // 遍历panel_91wenwen_panelist_91jili_connection表 , I connection
            $connection_indexes_found = use_key_value_index($connection_indexes , $panelist_id );

            if ($connection_indexes_found ) {
                // 遍历user_wenwen_cross表
                $cross_found = use_key_value_index($cross_indexs, $connection_indexes_found['jili_id']);
                if ($cross_found ) {
                    $user_row = use_file_index($user_indexs, strtolower($cross_found['email']), $user_file_handle);
                    if( $user_row) {
                        if( strtolower($panelist_row[3]) !== strtolower($user_row[1])  ) {
                            $both_but_diff_emails [] = strtolower($panelist_row[3]);
                        }

                        // 生成新的user数据：拥有两边账号，相同的部分取问问数据
                        generate_user_data_both_exsit($panelist_row, $user_row);
                        $array_panelistid_userid[$panelist_row[0]] = $user_row[0];
                        //其他要迁移的数据
                        migrate_common($panelist_row, $user_row[0]);
                        $cross_exist_count++;

                        $log_msg = "\n". 
                            "\t".'cross_exist_count:'.  $cross_exist_count.
                            "\t".$panelist_row[3] . '+' .$user_row[1] ;

                        FileUtil::writeContents($log_handle, $log_msg);
                        continue;
                    }
                }
            }

            // 遍历panel_91wenwen_pointexchange_91jili_account表 II exchange
            $exchange_indexes_found = use_key_value_index($exchange_indexes  , $panelist_id);
            if ($exchange_indexes_found) {
                $user_row = use_file_index($user_indexs, strtolower( $exchange_indexes_found['jili_email'] ), $user_file_handle);
                if( $user_row) {
                    if( strtolower($panelist_row[3]) !== strtolower($user_row[1])  ) {
                        $both_but_diff_emails [] =strtolower($panelist_row[3]);
                    }
                    // 生成新的user数据：拥有两边账号，相同的部分取问问数据
                    generate_user_data_both_exsit($panelist_row, $user_row);
                    $array_panelistid_userid[$panelist_row[0]] = $user_row[0];
                    //其他要迁移的数据
                    migrate_common($panelist_row,  $user_row[0]);
                    $exchange_exist_count++;
                    $log_msg = "\n". 
                        "\t".'exchange_exist_count:'.  $exchange_exist_count.
                        "\t".$panelist_row[3] . '+' .$user_row[1] ;
                    FileUtil::writeContents($log_handle, $log_msg);
                    continue;
                }
            }


            // 遍历jili user 表 III eamil
            $panelist_email = $panelist_row[3];
            $user_row = use_file_index($user_indexs, strtolower($panelist_email), $user_file_handle);
            if ( $user_row) {
                // 生成新的user数据：拥有两边账号，相同的部分取问问数据
                generate_user_data_both_exsit($panelist_row, $user_row);
                $array_panelistid_userid[$panelist_row[0]] = $user_row[0];
                //其他要迁移的数据
                migrate_common($panelist_row, $user_row[0]);
                $both_exist_count++;
                $log_msg = "\n". 
                    "\t".'both_exist_count:'.  $both_exist_count.
                    "\t".$panelist_row[3] . '+' .$user_row[1] ;
                FileUtil::writeContents($log_handle, $log_msg);
                continue;
            }


            // 遍历wenwen_sina  user_weibo表 IV  user connected by open_id
            $panelist_sina_row = use_file_index($sina_connection_indexs, $panelist_id, $panelist_sina_connection_file_handle, false);
            if( $panelist_sina_row  ) {
                // check open_id exists in jili.weibo_user 
                $weibo_user_indexes_by_open_id_found  = use_key_value_index($weibo_user_indexes_by_open_id, $panelist_sina_row[1]) ;
                if($weibo_user_indexes_by_open_id_found) {
                    // unset found panelist_id in  ww_sina_index manually
                    unset($sina_connection_indexs[$panelist_id]);
                    $user_indexs_by_id_found  = use_key_value_index($user_indexs_by_id, $weibo_user_indexes_by_open_id_found['user_id'] );
                    if($user_indexs_by_id_found ) {
                        $user_row = use_file_index($user_indexs, strtolower($user_indexs_by_id_found['email'] ), $user_file_handle);
                        if(  $user_row) {

                            if( strtolower($panelist_row[3]) !== strtolower($user_row[1])  ) {
                                $both_but_diff_emails [] =strtolower($panelist_row[3]);
                            }

                            // 生成新的user数据：拥有两边账号，相同的部分取问问数据
                            generate_user_data_both_exsit($panelist_row, $user_row);
                            $array_panelistid_userid[$panelist_row[0]] = $user_row[0];

                            //其他要迁移的数据
                            migrate_common($panelist_row, $user_row[0]);
                            $weibo_exist_count++;
                            $log_msg = "\n". 
                                "\t".'weibo_exist_count:'.  $weibo_exist_count.
                                "\t".$panelist_row[3] . '+' .$user_row[1] ;
                            FileUtil::writeContents($log_handle, $log_msg);
                            continue;
                        }
                    }
                }
            }

            $max_user_id++;
            //生成仅存在问问的账号的user数据
            generate_user_data_only_wenwen($panelist_row,  $max_user_id);
            $array_panelistid_userid[$panelist_row[0]] = $max_user_id;

            //其他要迁移的数据
            migrate_common($panelist_row, $max_user_id);

            $only_wenwen_count++;
            $log_msg = "\n". 
                "\t".'only_wenwen_count:'.  $only_wenwen_count.
                "\t".$panelist_row[3] . '+ maxid:' .$max_user_id;
            FileUtil::writeContents($log_handle, $log_msg);
        }
        
        generate_history_details($array_panelistid_userid);
        export_history_data($array_panelistid_userid);
        
    } catch (Exception $e) {
        FileUtil::writeContents($log_handle, "Exception:" . $e->getMessage());
    }
    // unset global file handler 
    global $panelist_profile_file_handle;
    global $panelist_detail_file_handle;
    global $panelist_profile_image_file_handle;
    global $panelist_mobile_number_file_handle;
    global $panelist_point_file_handle;
    global $vote_answer_file_handle;
    global $sop_respondent_file_handle;

    unset($panelist_profile_file_handle);
    unset($panelist_detail_file_handle);
    unset($panelist_profile_image_file_handle);
    unset($panelist_mobile_number_file_handle);
    unset($panelist_point_file_handle);
    unset($panelist_sina_connection_file_handle);
    unset($pointexchange_91jili_account_file_handle);
    unset($vote_answer_file_handle);
    unset($panelist_91jili_connection_file_handle);
    unset($sop_respondent_file_handle);
    unset($user_wenwen_cross_file_handle);
    unset($migration_region_mapping_file_handle);
    // unset global index
    global $panelist_image_indexs;
    global $panelist_point_indexs;
    global $panelist_mobile_indexs;
    global $region_mapping_indexs;
    global $panelist_detail_indexs;
    global $panelist_profile_indexs;
    //    global $sina_connection_indexs;
    global $sop_respondent_indexs;
    global $vote_answer_indexs;

    unset($panelist_image_indexs);
    unset($panelist_point_indexs);
    unset($panelist_mobile_indexs);
    unset($region_mapping_indexs);
    unset($panelist_detail_indexs);
    unset($panelist_profile_indexs);
    unset($sina_connection_indexs);
    unset($sop_respondent_indexs);
    unset($vote_answer_indexs);

    // unset local insexes
    unset($connection_indexes);
    unset($cross_indexs);
    unset($user_indexs_by_id);
    unset($weibo_user_indexes_by_open_id);


    $log_msg = "\n". 
        "\t".'count of $both_but_diff_emails: '. count($both_but_diff_emails);
    FileUtil::writeContents($log_handle, $log_msg);

    $user_ids_ignored= array();

    //  user_only , no matched 
    foreach ($user_indexs as $email => $pointer) {
        $email = strtolower($email) ;
        fseek($user_file_handle, $pointer);
        $user_row = fgetcsv($user_file_handle);
        
        if( in_array( $email , $both_but_diff_emails )) {
            $log_msg = "\n". 
                "\t".'Ignored user for merged already, email:'.  $email.
                "\t". json_encode($user_row , true);
            FileUtil::writeContents($log_handle, $log_msg);
            $user_ids_ignored[] =  $user_row[0];
            continue;
        }

        generate_user_data_only_jili($user_row);

        $only_jili_count++;
        $log_msg = "\n". 
            "\t".'only_jili_count:'.  $only_jili_count.
            "\t". $user_row[1] ;
        FileUtil::writeContents($log_handle, $log_msg);
    }

    $log_msg = "\n". 
        "\t".'count of $user_ids_ignored: '. count($user_ids_ignored).
        "\t".'count of $open_ids_dismatched: '. count($open_ids_dismatched);
    FileUtil::writeContents($log_handle, $log_msg);

    // weibo_user: no changed
    foreach($weibo_user_indexs as $user_id => $pointer) {
        if( in_array($user_id , $user_ids_ignored)) {
            $log_msg = "\n". 
                "\t".'Ignored weib_user for user_ignored,jili.user.id:'.  $user_id;
            FileUtil::writeContents($log_handle, $log_msg);
            continue;
        }

        fseek($weibo_user_file_handle, $pointer);
        $weibo_user = fgetcsv($weibo_user_file_handle);

        if ( in_array( $weibo_user[2] , $open_ids_dismatched ) ) {

            $log_msg = "\n". 
                "\t".'Ignored weibo_user for open_id used,open_id:'.  $weibo_user[1] ;
            FileUtil::writeContents($log_handle, $log_msg);
            continue;
        }
        //id set default to avoid duplicated PK when insert.
        $weibo_user[0] = 'NULL';
        export_csv_row($weibo_user, Constants::$migrate_weibo_user_name);
    }

    $log_msg =  PHP_EOL.'cross_exist_count:' . $cross_exist_count . 
        PHP_EOL.'exchange_exist_count:' . $exchange_exist_count . 
        PHP_EOL.'weibo_exist_count:' . $weibo_exist_count . 
        PHP_EOL.'both_exist_count:' . $both_exist_count . 
        PHP_EOL.'only_wenwen_count:' . $only_wenwen_count . 
        PHP_EOL.'only_jili_count:' . $only_jili_count . 
        PHP_EOL.'import_wenwen_count:' . ($both_exist_count + $only_wenwen_count) .
        PHP_EOL.'export user total:" '. ($both_exist_count + $only_wenwen_count + $only_jili_count) .
        PHP_EOL.
        PHP_EOL. round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB'.
        PHP_EOL.'end!';
    FileUtil::writeContents($log_handle, $log_msg);

    fclose($log_handle);

    echo date('Y-m-d H:i:s') . "memory_get_peak_usage:";
    echo (!function_exists('memory_get_peak_usage')) ? '0' : round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB' . "\n";
    echo date('Y-m-d H:i:s') . " end!\r\n\r\n";
}


do_process();


