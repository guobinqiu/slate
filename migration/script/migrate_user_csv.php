<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');
include_once ('CsvReader.php');

function do_process()
{
    // ini_set("memory_limit",-1);
    FileUtil::writeContents(LOG_PATH, date('c') . " start!\r\n\r\n");
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

            FileUtil::writeContents(LOG_PATH, "panelist line:" . $i);

            $panelist_id = $panelist_data[0];
            $jili_email = $panelist_email = $panelist_data[3];

            //FileUtil::writeContents(LOG_PATH, "panelist_id:" . $panelist_id);
            //FileUtil::writeContents(LOG_PATH, "panelist_email:" . $panelist_email);


            $j = 0;
            $k = 0;

            //遍历panel_91wenwen_panelist_91jili_connection表
            $jili_cross_id = getJiliConnectionByPanelistId($panelist_91jili_connection_file_handle, $panelist_id);
            if ($jili_cross_id) {
                FileUtil::writeContents(LOG_PATH, "jili_cross_id:" . $jili_cross_id);

                //遍历user_wenwen_cross表
                $jili_email = getUserWenwenCrossById($user_wenwen_cross_file_handle, $jili_cross_id);
                if ($jili_email) {
                    FileUtil::writeContents(LOG_PATH, "jili_cross_id->jili email:" . $jili_email);

                    $cross_exist_count++;
                    FileUtil::writeContents(LOG_PATH, "cross_exist_count:" . $cross_exist_count);
                }
            }

            //遍历panel_91wenwen_pointexchange_91jili_account表
            $exchang_jili_email = getPointExchangeByPanelistId($pointexchange_91jili_account_file_handle, $panelist_id);
            if ($exchang_jili_email) {
                $jili_email = $exchang_jili_email;
                $exchange_exist_count++;
                FileUtil::writeContents(LOG_PATH, "exchange_exist_count:" . $exchange_exist_count);
                FileUtil::writeContents(LOG_PATH, "pointexchange-> jili_email:" . $jili_email);
            }

            //遍历jili user 表
            $user_data = fetch_jili_user($jili_email);
            if ($user_data) {
                $both_exist_count = $both_exist_count + 1;
                FileUtil::writeContents(LOG_PATH, "both_exist_count:" . $both_exist_count);

                //todo:生成新的csv文件：拥有两边账号，相同的部分取问问数据
                generate_csv_both();
            } else {
                $only_wenwen_count++;
                FileUtil::writeContents(LOG_PATH, "only_wenwen_count:" . $only_wenwen_count);

                //todo:生成新的csv文件：仅存在问问的账号
                generate_csv_from_wenwen();
            }
        }
    } catch (Exception $e) {
        FileUtil::writeContents(LOG_PATH, "Exception:" . $e->getMessage());
    }

    FileUtil::writeContents(LOG_PATH, "cross_exist_count:" . $cross_exist_count);
    FileUtil::writeContents(LOG_PATH, "exchange_exist_count:" . $exchange_exist_count);
    FileUtil::writeContents(LOG_PATH, "both_exist_count:" . $both_exist_count);
    FileUtil::writeContents(LOG_PATH, "only_wenwen_count:" . $only_wenwen_count);

    FileUtil::writeContents(LOG_PATH, date('c') . " end!");

    echo date('c') . " end!\r\n\r\n";
}
//遍历jili user 表
function fetch_jili_user($email)
{
    $n = 0;

    $user_file = IMPORT_JL_PATH . "/user.csv";
    $user_file_handle = FileUtil::checkFile($user_file);

    fgetcsv($user_file_handle, 2000, ",");
    while (($user_data = fgetcsv($user_file_handle, 2000, ",")) !== FALSE) {
        $n++;

        if ($email == $jili_account_data[1]) {
            FileUtil::writeContents(LOG_PATH, "both exist: wenwen=>jili_email:" . $email);

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
  //skip the title line
  $row = fgets($fh, 1024);
  $panelist_id_pos = strpos($row, ',');
  $panelist_id = substr($row, 1, $panelist_id_pos - 2);

  while($panelist_id_input < $panelist_id ) {

  }

  $jili_id_pos = strpos($row, ',', $panelist_id_pos + 1);
  $jili_id = substr($row, $panelist_id_pos + 2, $jili_id_pos - $panelist_id_pos - 3);
  $status_flag_pos = strpos($row, ',', $jili_id_pos + 1);

  $status_flag = substr($row, $jili_id_pos + 2, $status_flag_pos - $jili_id_pos - 3);
  // need  to check to status_flag
  if ($status_flag != 1) {
    return ;
  }
  return $jili_id;
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

/**
 * 遍历panel_91wenwen_pointexchange_91jili_account表
 * "panelist_id","jili_email","status_flag","stash_data","updated_at","created_at"
 * "305","28216843@qq.com","1","NULL","2014-02-24 10:21:34","2014-02-20 11:58:08"
 * "2229759","syravia@gmail.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/944966ca79a14e49c74009896922bf13/1436557""}","2015-11-16 11:38:00","2015-11-16 11:38:00"
 * @return  or null
 */
function getPointExchangeByPanelistId($fh, $panelist_id_input)
{
    rewind($fh);
    fgets($fh);
    while ($row = fgets($fh, 1024)) {
        $panelist_id_pos = strpos($row, ',');
        if ($panelist_id_input != substr($row, 1, $panelist_id_pos - 2)) {
            continue;
        }

        $jili_email_pos = strpos($row, ',', $panelist_id_pos + 1);
        $status_flag_pos = strpos($row, ',', $jili_email_pos + 1);
        $status_flag = substr($row, $jili_email_pos + 2, $status_flag_pos - $jili_email_pos - 3);

        if ($status_flag != 1) {
            continue;
        }

        $jili_email = substr($row, $panelist_id_pos + 2, $jili_email_pos - $panelist_id_pos - 3);
        return $jili_email;
    }
    return;
}

// do_process();
