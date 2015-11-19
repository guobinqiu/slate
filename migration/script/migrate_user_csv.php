<?php
include('config.php');
include('FileUtil.php');
include('Constants.php');
include ('migrate_function.php');


function do_process()
{
    ini_set('memory_limit', '-1');

    $log_handle = fopen(LOG_PATH, "a");

    echo date('Y-m-d H:i:s') . " start!\r\n\r\n";
    FileUtil::writeContents($log_handle, "start!\r\n\r\n");

    // import file : wenwen
    $panelist_file = IMPORT_WW_PATH . "/panelist.csv";
    $panelist_detail_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_detail.csv";
    $panelist_profile_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile.csv";
    $panelist_profile_image_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile_image.csv";
    $panelist_mobile_number_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_mobile_number.csv";
    $panelist_point_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_point.csv";
    $panelist_sina_connection_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_sina_connection.csv";
    $pointexchange_91jili_account_file = IMPORT_WW_PATH . "/panel_91wenwen_pointexchange_91jili_account.csv";
    $vote_answer_file = IMPORT_WW_PATH . "/" . VOTE_ANSWER . ".csv";
    $panelist_91jili_connection_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_91jili_connection.csv";
    $sop_respondent_file = IMPORT_WW_PATH . "/sop_respondent.csv";

    // import file : jili
    $user_file = IMPORT_JL_PATH . "/user.csv";
    $user_wenwen_cross_file = IMPORT_JL_PATH . "/user_wenwen_cross.csv";
    $weibo_user_file = IMPORT_JL_PATH . "/weibo_user.csv";
    //todo: jili.migration_region_mapping 数据需要导出来
    $migration_region_mapping_file = IMPORT_JL_PATH . "/migration_region_mapping.csv";

    // export jili csv
    $migrate_user_csv = EXPORT_PATH . "/migrate_user.csv";
    $migrate_user_wenwen_login_csv = EXPORT_PATH . "/migrate_user_wenwen_login.csv";
    $migrate_weibo_user_csv = EXPORT_PATH . "/weibo_user.csv";
    $migrate_vote_answer_csv = EXPORT_PATH . "/migrate_vote_answer.csv";
    $migrate_sop_respondent_csv = EXPORT_PATH . "/migrate_sop_respondent.csv";

    //check file
    $panelist_file_handle = FileUtil::checkFile($panelist_file);
    $panelist_detail_file_handle = FileUtil::checkFile($panelist_detail_file);
    $panelist_profile_file_handle = FileUtil::checkFile($panelist_profile_file);
    $panelist_profile_image_file_handle = FileUtil::checkFile($panelist_profile_image_file);
    $panelist_mobile_number_file_handle = FileUtil::checkFile($panelist_mobile_number_file);
    $panelist_point_file_handle = FileUtil::checkFile($panelist_point_file);
    $panelist_sina_connection_file_handle = FileUtil::checkFile($panelist_sina_connection_file);
    $pointexchange_91jili_account_file_handle = FileUtil::checkFile($pointexchange_91jili_account_file);
    $vote_answer_file_handle = FileUtil::checkFile($vote_answer_file);
    $panelist_91jili_connection_file_handle = FileUtil::checkFile($panelist_91jili_connection_file);

    $user_file_handle = FileUtil::checkFile($user_file);
    $user_wenwen_cross_file_handle = FileUtil::checkFile($user_wenwen_cross_file);

    //todo: get max user id
//    $jili_user_data = FileUtil::readCsvContent($user_file);


    $cross_exist_count = 0;
    $exchange_exist_count = 0;
    $both_exist_count = 0; //cross_exist_count+exchange_exist_count+wenwen_email
    $only_wenwen_count = 0;

    // panel_91wenwen_panelist_91jili_connection 记录当前的connection csv handler中的行。 
    $connection_current = array() ;
     // 遍历user_wenwen_cross表
    $cross =  getUserWenwenCross( $user_wenwen_cross_file_handle);
    $user  =  getUser( $user_file_handle);
     // 遍历user_wenwen_cross表
    $exchange_current = array() ;

    //遍历panelist表
    $i = 0;
    fgetcsv($panelist_file_handle, 2000, ",");
    try {
        while (($panelist_data = fgetcsv($panelist_file_handle, 2000, ",")) !== FALSE) {
            $i++;

           // FileUtil::writeContents($log_handle, "panelist line:" . $i);

            $panelist_id = $panelist_data[0];
            $jili_email = $panelist_email = $panelist_data[3];

            //FileUtil::writeContents($log_handle, "panelist_id:" . $panelist_id);
            //FileUtil::writeContents($log_handle, "panelist_email:" . $panelist_email);

            $j = 0;
            $k = 0;

            //遍历panel_91wenwen_panelist_91jili_connection表
            $connection_current = getJiliConnectionByPanelistId($panelist_91jili_connection_file_handle, $panelist_id,$connection_current );
            $jili_cross_id = ($connection_current['matched'] == 1) ? $connection_current['jili_id']: null;

            if ($jili_cross_id) {
//              FileUtil::writeContents($log_handle, "jili_cross_id:" . $jili_cross_id);

              //遍历user_wenwen_cross表
              if( isset($cross[$jili_cross_id]) ) {
                $jili_email = $cross[$jili_cross_id];
                unset($cross[$jili_cross_id]);

                if ($jili_email) {
                  //                    FileUtil::writeContents($log_handle, "jili_cross_id->jili email:" . $jili_email);
                  $cross_exist_count++;
                  //                   FileUtil::writeContents($log_handle, "cross_exist_count:" . $cross_exist_count);
                  continue;
                }
              }
            }

            //遍历panel_91wenwen_pointexchange_91jili_account表
            $exchange_current = getPointExchangeByPanelistId($pointexchange_91jili_account_file_handle, $panelist_id,$exchange_current);
            $exchang_jili_email =  ($exchange_current['matched'] == 1) ? $exchange_current['jili_email']: null;
            if ($exchang_jili_email) {
              $jili_email = $exchang_jili_email;
              $exchange_exist_count++;
              //                FileUtil::writeContents($log_handle, "exchange_exist_count:" . $exchange_exist_count);
              //               FileUtil::writeContents($log_handle, "pointexchange-> jili_email:" . $jili_email);
              continue;
            }

            //遍历jili user 表
            if( isset($user[$jili_email])) {
                    $both_exist_count = $both_exist_count + 1;
//                    FileUtil::writeContents($log_handle, "both_exist_count:" . $both_exist_count);
                  unset($user[$jili_email]);
                  continue;;
            }
            $only_wenwen_count++;
        }
    } catch (Exception $e) {
        FileUtil::writeContents($log_handle, "Exception:" . $e->getMessage());
    }

    FileUtil::writeContents($log_handle, "\n\tcross_exist_count:" . $cross_exist_count.
     "\n\texchange_exist_count:" . $exchange_exist_count.
     "\n\tboth_exist_count:" . $both_exist_count.
     "\n\tonly_wenwen_count:" . $only_wenwen_count.
     "\n\ttotal:" .  ( $cross_exist_count + $exchange_exist_count+ $both_exist_count + $only_wenwen_count )
);


    FileUtil::writeContents($log_handle, "end!");
    echo ( ! function_exists('memory_get_peak_usage')) ? '0' : round(memory_get_peak_usage()/1024/1024, 2).'MB'. "\n"; 


    echo date('Y-m-d H:i:s') . " end!\r\n\r\n";

    fclose($log_handle);
}
 do_process();
