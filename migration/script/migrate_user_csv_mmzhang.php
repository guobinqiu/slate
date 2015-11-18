<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');
include_once ('migrate_function.php');

ini_set('memory_limit', '-1');

function do_process()
{
    $log_handle = fopen(LOG_PATH, "a");
    echo date('Y-m-d H:i:s') . " start!\r\n\r\n";
    FileUtil::writeContents($log_handle, "start!\r\n\r\n");

    //todo: get max user id
    $max_user_id = 2000000;

    // import file
    $panelist_file = IMPORT_WW_PATH . "/panelist.csv";
    $panelist_91jili_connection_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_91jili_connection.csv";
    $pointexchange_91jili_account_file = IMPORT_WW_PATH . "/panel_91wenwen_pointexchange_91jili_account.csv";
    $user_file = IMPORT_JL_PATH . "/user.csv";

    // get file content
    //$panelist_data = FileUtil::readCsvContent($panelist_file);
    $panelist_data = FileUtil::csv_get_lines($panelist_file, 10);
    $connection_account_data = FileUtil::readCsvContent($panelist_91jili_connection_file);
    $exchange_account_data = FileUtil::readCsvContent($pointexchange_91jili_account_file);
    $jili_user_data = FileUtil::readCsvContent($user_file);

    $migrate_user_only_wenwen_data = array ();
    $migrate_user_wenwen_login_data = array ();

    $cross_exist_count = 0;
    $exchange_exist_count = 0;
    $both_exist_count = 0; //cross_exist_count+exchange_exist_count+wenwen_email
    $only_wenwen_count = 0;
    $both_exist = false;

    try {
        //遍历panelist表
        foreach ($panelist_data as $panelist_row) {

            $panelist_id = $panelist_row[0];
            $jili_email = $panelist_email = $panelist_row[3];

            FileUtil::writeContents($log_handle, "panelist_id:" . $panelist_id);

            //遍历panel_91wenwen_panelist_91jili_connection表
            foreach ($connection_account_data as $wenwen_cross) {
                if ($panelist_id == $wenwen_cross[0]) {
                    $cross_id = $wenwen_cross[1];

                    //遍历user_wenwen_cross表
                    $user_wenwen_cross = FileUtil::readCsvContent($user_wenwen_cross_file);
                    foreach ($user_wenwen_cross as $jili_cross) {
                        if ($cross_id == $jili_cross[0]) {
                            $jili_email = $jili_cross[3];
                            $cross_exist_count++;
                            FileUtil::writeContents($log_handle, "cross_exist_count:" . $cross_exist_count);
                            break 2;
                        }
                    }
                }
            }

            //遍历panel_91wenwen_pointexchange_91jili_account表
            foreach ($exchange_account_data as $exchange) {
                if ($panelist_id == $exchange[0]) {
                    $jili_email = $exchange[1];
                    $exchange_exist_count++;
                    FileUtil::writeContents($log_handle, "exchange_exist_count:" . $exchange_exist_count);
                    break;
                }
            }

            //遍历jili user 表
            foreach ($jili_user_data as $user_key => $user_row) {
                if ($jili_email == $user_row[1]) {

                    $both_exist = true;
                    $both_exist_count = $both_exist_count + 1;
                    FileUtil::writeContents($log_handle, "both_exist_count:" . $both_exist_count);

                    //todo:生成新的user数据：拥有两边账号，相同的部分取问问数据
                    $jili_user_data[$user_key] = generate_user_data_both_exsit($panelist_row, $user_row);

                    //todo:生成仅存在问问的账号的user_wenwen_login数据
                    $migrate_user_wenwen_login_data[] = generate_user_wenwen_login_data($panelist_row, $user_row[0]);

                    //todo 新浪数据合并


                    //todo sop_respondent数据迁移


                    //vote_answer 数据迁移


                    break;
                }
            }

            if (!$both_exist) {
                //只存在问问的用户
                $only_wenwen_count++;
                FileUtil::writeContents($log_handle, "only_wenwen_count:" . $only_wenwen_count);

                //todo:生成仅存在问问的账号的user数据
                $max_user_id++;
                $migrate_user_only_wenwen_data[] = generate_user_data_only_wenwen($panelist_row, $max_user_id);

                //todo:生成仅存在问问的账号的user_wenwen_login数据
                $migrate_user_wenwen_login_data[] = generate_user_wenwen_login_data($panelist_row, $max_user_id);

                //todo 新浪数据迁移


                //todo sop_respondent数据迁移


                //vote_answer 数据迁移
            }
        }
    } catch (Exception $e) {
        FileUtil::writeContents($log_handle, "Exception:" . $e->getMessage());
    }

    FileUtil::writeContents($log_handle, "cross_exist_count:" . $cross_exist_count);
    FileUtil::writeContents($log_handle, "exchange_exist_count:" . $exchange_exist_count);
    FileUtil::writeContents($log_handle, "both_exist_count:" . $both_exist_count);
    FileUtil::writeContents($log_handle, "only_wenwen_count:" . $only_wenwen_count);

    //export csv file
    export_csv($jili_user_data, Constants::$jili_user_title, 'migrate_user.csv');
    export_csv($migrate_user_only_wenwen_data, Constants::$jili_user_title, 'migrate_user_only_wenwen.csv');
    export_csv($migrate_user_wenwen_login_data, Constants::$user_wenwen_login_title, 'migrate_user_wenwen_login.csv');

    FileUtil::writeContents($log_handle, "end!");

    echo date('Y-m-d H:i:s') . " end!\r\n\r\n";

    fclose($log_handle);
}

do_process();
