<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');
include_once ('CsvReader.php');

function do_process()
{
    $import_path = IMPORT_PATH;
    $export_path = EXPORT_PATH;
    $log_path = LOG_PATH;

    // ini_set("memory_limit",-1);


    FileUtil::writeContents($log_path, date('c') . " start!\r\n\r\n");
    echo date('c') . " start!\r\n\r\n";

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
    $migrate_user_csv = $export_path . "/migrate_user.csv";
    $migrate_user_wenwen_login_csv = $export_path . "/migrate_user_wenwen_login.csv";
    $migrate_weibo_user_csv = $export_path . "/weibo_user.csv";
    $migrate_vote_answer_csv = $export_path . "/migrate_vote_answer.csv";
    $migrate_sop_respondent_csv = $export_path . "/migrate_sop_respondent.csv";

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


    $cross_exist_count = 0;
    $exchange_exist_count = 0;
    $both_exist_count = 0; //cross_exist_count+exchange_exist_count+wenwen_email
    $only_wenwen_count = 0;

    //遍历panelist表
    $i = 0;
    fgetcsv($panelist_file_handle, 2000, ",");
    try {
        while (($panelist_data = fgetcsv($panelist_file_handle, 2000, ",")) !== FALSE) {
            $i++;
            FileUtil::writeContents($log_path, "panelist line:" . $i);

            $panelist_id = $panelist_data[0];
            $jili_email = $panelist_email = $panelist_data[3];

            //FileUtil::writeContents($log_path, "panelist_id:" . $panelist_id);
            //FileUtil::writeContents($log_path, "panelist_email:" . $panelist_email);


            $j = 0;
            $k = 0;

            //遍历panel_91wenwen_panelist_91jili_connection表
            fgetcsv($panelist_91jili_connection_file_handle, 2000, ",");
            while (($jili_connection_data = fgetcsv($panelist_91jili_connection_file_handle, 2000, ",")) !== FALSE) {
                $j++;

                if ($panelist_id == $jili_connection_data[0]) {
                    $jili_cross_id = $jili_connection_data[1];

                    FileUtil::writeContents($log_path, "jili_cross_id:" . $jili_cross_id);

                    //遍历user_wenwen_cross表
                    $m = 0;
                    fgetcsv($user_wenwen_cross_file_handle, 2000, ",");
                    while (($user_wenwen_cross_data = fgetcsv($user_wenwen_cross_file_handle, 2000, ",")) !== FALSE) {
                        $m++;

                        if ($jili_cross_id == $user_wenwen_cross_data[0]) {
                            $jili_email = $user_wenwen_cross_data[3];
                            FileUtil::writeContents($log_path, "jili_cross_id->jili email:" . $jili_email);
                            $cross_exist_count++;
                            FileUtil::writeContents($log_path, "cross_exist_count:" . $cross_exist_count);
                            break;
                        }
                    }

                    break;
                }
            }

            //遍历panel_91wenwen_pointexchange_91jili_account表
            fgetcsv($pointexchange_91jili_account_file_handle, 2000, ",");
            while (($jili_account_data = fgetcsv($pointexchange_91jili_account_file_handle, 2000, ",")) !== FALSE) {
                $k++;

                if ($panelist_id == $jili_account_data[0]) {
                    $jili_email = $jili_account_data[1];
                    $exchange_exist_count++;
                    FileUtil::writeContents($log_path, "exchange_exist_count:" . $exchange_exist_count);
                    FileUtil::writeContents($log_path, "pointexchange-> jili_email:" . $jili_email);
                    break;
                }
            }

            //遍历jili user 表
            $user_data = fetch_jili_user($jili_email, $log_path);
            if ($user_data) {
                $both_exist_count = $both_exist_count + 1;
                FileUtil::writeContents($log_path, "both_exist_count:" . $both_exist_count);

                //todo:生成新的csv文件：拥有两边账号，相同的部分取问问数据
                generate_csv_both();
            } else {
                $only_wenwen_count++;
                FileUtil::writeContents($log_path, "only_wenwen_count:" . $only_wenwen_count);

                //todo:生成新的csv文件：仅存在问问的账号
                generate_csv_from_wenwen();
            }
        }
    } catch (Exception $e) {
        FileUtil::writeContents($log_path, "Exception:" . $e->getMessage());
    }

    FileUtil::writeContents($log_path, "cross_exist_count:" . $cross_exist_count);
    FileUtil::writeContents($log_path, "exchange_exist_count:" . $exchange_exist_count);
    FileUtil::writeContents($log_path, "both_exist_count:" . $both_exist_count);
    FileUtil::writeContents($log_path, "only_wenwen_count:" . $only_wenwen_count);

    FileUtil::writeContents($log_path, date('c') . " end!");

    echo date('c') . " end!\r\n\r\n";
}
//遍历jili user 表
function fetch_jili_user($email, $log_path)
{
    $n = 0;

    $user_file = IMPORT_JL_PATH . "/user.csv";
    $user_file_handle = FileUtil::checkFile($user_file);

    fgetcsv($user_file_handle, 2000, ",");
    while (($user_data = fgetcsv($user_file_handle, 2000, ",")) !== FALSE) {
        $n++;

        if ($email == $jili_account_data[1]) {
            FileUtil::writeContents($log_path, "both exist: wenwen=>jili_email:" . $email);

            //todo:删除user csv文件中该行
            //delete_row_user_csv();
            $data = array (
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            );

            fputcsv($user_file_handle, $data);

            return $user_data;
        }
    }

    return false;
}

function generate_csv_both()
{
    //生成 migrate_user.csv
    //生成 migrate_user_wenwen_login.csv
}

function generate_csv_from_wenwen()
{
    //生成 migrate_user.csv
    //生成 migrate_user_wenwen_login.csv
}

/**
 * 遍历panel_91wenwen_panelist_91jili_connection表
 *
 * "panelist_id","jili_id","status_flag","stash_data","updated_at","created_at"
 * "305","16980","1","NULL","2015-01-07 16:50:12","2015-01-07 16:50:12"
 *
 * @return jili_id or null
 */
function getJiliConnectionByPanelistId($fh, $panelist_id_input)
{
    rewind($fh);
    fgets($fh); //skip the title line
    while ($row = fgets($fh, 1024)) {
        $panelist_id_pos = strpos($row, ',');

        if ($panelist_id_input != substr($row, 1, $panelist_id_pos - 2)) {
            continue;
        }

        $jili_id_pos = strpos($row, ',', $panelist_id_pos + 1);
        $jili_id = substr($row, $panelist_id_pos + 2, $jili_id_pos - $panelist_id_pos - 3);
        $status_flag_pos = strpos($row, ',', $jili_id_pos + 1);

        $status_flag = substr($row, $jili_id_pos + 2, $status_flag_pos - $jili_id_pos - 3);
        // need  to check to status_flag
        if ($status_flag != 1) {
            continue;
        }
        return $jili_id;
    }
    return;
}

/**
 *  遍历user_wenwen_cross表
 * "id","user_id","created_at","email"
 * "5629","1270570","2014-11-26 16:32:00","NULL"
 * @return email or null
 */
function getUserWenwenCrossById($fh, $id_input)
{
    if (empty($id_input)) {
        return;
    }

    rewind($fh);
    fgets($fh);
    while ($row = fgets($fh)) {
        if (substr($row, 1, strlen($id_input)) != $id_input) {
            continue;
        }
        $email = substr($row, strrpos($row, ',') + 2, -2);
        return ('NULL' == $email) ? null : $email;
    }
    return;
}

// do_process();
