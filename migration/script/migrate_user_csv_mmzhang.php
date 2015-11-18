<?php
include_once ('config.php');
include_once ('FileUtil.php');
include_once ('Constants.php');

//todo: get max user id
$max_user_id = 2000000;

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
    $migrate_user_only_wenwen_csv = EXPORT_PATH . "/migrate_user_only_wenwen.csv";
    $migrate_user_wenwen_login_csv = EXPORT_PATH . "/migrate_user_wenwen_login.csv";
    $migrate_weibo_user_csv = EXPORT_PATH . "/weibo_user.csv";
    $migrate_vote_answer_csv = EXPORT_PATH . "/migrate_vote_answer.csv";
    $migrate_sop_respondent_csv = EXPORT_PATH . "/migrate_sop_respondent.csv";

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
    } catch (Exception $e) {
        FileUtil::writeContents($log_handle, "Exception:" . $e->getMessage());
    }

    FileUtil::writeContents($log_handle, "cross_exist_count:" . $cross_exist_count);
    FileUtil::writeContents($log_handle, "exchange_exist_count:" . $exchange_exist_count);
    FileUtil::writeContents($log_handle, "both_exist_count:" . $both_exist_count);
    FileUtil::writeContents($log_handle, "only_wenwen_count:" . $only_wenwen_count);

    FileUtil::writeContents($log_handle, "end!");

    echo date('Y-m-d H:i:s') . " end!\r\n\r\n";

    fclose($log_handle);
}

//user data of both exist on wenwen and jili
function generate_user_data_both_exsit($panelist_row, $user_row)
{
    $user_row = generate_user_data_wenwen_common($panelist_row, $user_row);

    //origin_flag
    $user_row[30] = 3; //todo:定义常量, 是否在设值


    return $user_row;
}

//user data of only exist on wenwen
function generate_user_data_only_wenwen($panelist_row, $user_id)
{
    $user_row = generate_user_data_wenwen_common($panelist_row, $user_row);

    $user_row[0] = $user_id;

    //is_email_confirmed
    $user_row[3] = 1;

    //reward_multiple
    $user_row[20] = 1;

    //origin_flag
    $user_row[30] = 2; //todo:定义常量, 是否在设值


    for ($i = 0; $i <= 38; $i++) {
        if (!isset($user_row[$i])) {
            $user_row[$i] = null;
        }
    }
}

//user common data of wenwen
function generate_user_data_wenwen_common($panelist_row, $user_row = array())
{
    //email
    $user_row[1] = $panelist_row[3];

    //password
    $user_row[2] = $panelist_row[5];

    //nick todo profile.nickname
    $user_row[7] = $panelist_row[5];

    //sex
    $user_row[8] = $panelist_row[13];

    //birthday(todo: check 格式)
    $user_row[9] = 'todo';

    //tel: panel_91wenwen_panelist_mobile_number.mobile_number
    $user_row[10] = 'todo';

    //province: panelist.panel_region_id
    $user_row[12] = 'todo';

    //city: panelist.panel_region_id
    $user_row[13] = 'todo';

    //education: detail.graduation_code
    $user_row[14] = 'todo';

    //profession: detail.detail.job_code
    $user_row[15] = 'todo';

    //income : detail.income_personal_code
    $user_row[16] = 'todo';

    //hobby: profile.hobby
    $user_row[17] = 'todo';

    //personalDes: profile.biography
    $user_row[18] = 'todo';

    //register_date
    $user_row[21] = $panelist_row[9];

    //last_login_date(todo: 格式转化)
    $user_row[22] = $panelist_row[17];

    //last_login_ip todo
    $user_row[23] = 'todo';

    //points: panel_91wenwen_panelist_point.point_value
    $user_row[24] = 'todo';

    //icon_path:panelist_profile_image
    $user_row[27] = 'todo';

    //created_remote_addr
    $user_row[31] = $panelist_row[10];

    //created_user_agent
    $user_row[32] = $panelist_row[11];

    //campaign_code
    $user_row[33] = $panelist_row[16];

    //password_choice
    $user_row[34] = 1; //todo:定义常量


    //fav_music: profile.fav_music
    $user_row[35] = 'todo';

    //monthly_wish:profile.monthly_wish
    $user_row[36] = 'todo';

    //industry_code: detail.industry_code
    $user_row[37] = 'todo';

    //work_section_code: detail.work_section_code
    $user_row[38] = 'todo';

    return $user_row;
}

//user_wenwen_login data
function generate_user_wenwen_login_data($panelist_row, $user_id)
{
    //id
    $user_wenwen_login_row[0] = null;

    //user_id
    $user_wenwen_login_row[1] = $user_id;

    //login_password_salt
    $user_wenwen_login_row[2] = $panelist_row[7];

    //login_password_crypt_type
    $user_wenwen_login_row[3] = $panelist_row[6];

    //login_password
    $user_wenwen_login_row[4] = $panelist_row[5];

    return $user_wenwen_login_row;
}

do_process();
