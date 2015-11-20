<?php
ini_set('memory_limit', '2048M');

include ('config.php');
include ('FileUtil.php');
include ('Constants.php');
include ('migrate_function.php');

$log_handle = fopen(LOG_PATH, "a");
echo date('Y-m-d H:i:s') . " start!\r\n\r\n";
FileUtil::writeContents($log_handle, "start!\r\n\r\n");

initialise_csv();

function do_process()
{
    global $log_handle;
    global $panelist_file_handle;
    global $user_file_handle;
    global $user_wenwen_cross_file_handle;
    global $pointexchange_91jili_account_file_handle;
    global $panelist_91jili_connection_file_handle;

    $cross_exist_count = 0;
    $exchange_exist_count = 0;
    $both_exist_count = 0; //cross_exist_count+exchange_exist_count+wenwen_email
    $only_wenwen_count = 0;

    // panel_91wenwen_panelist_91jili_connection 记录当前的connection csv handler中的行。
    $connection_current = array ();

    //创建索引
    $cross_indexs = build_key_value_index($user_wenwen_cross_file_handle, 'id', 'email');
    $user_indexs = build_file_index($user_file_handle, 'email');

    // 遍历user_wenwen_cross表
    $exchange_current = array ();

    //get max user id
    $max_user_id = get_max_user_id_by_index($user_indexs, $user_file_handle);

    //遍历panelist表
    $i = 0;
    fgetcsv($panelist_file_handle, 2000, ",");
    try {
        while (($panelist_row = fgetcsv($panelist_file_handle, 2000, ",")) !== FALSE) {

            $i++;

            $panelist_id = $panelist_row[0];
            $jili_email = $panelist_email = $panelist_row[3];
            $jili_user_id = '';

            $j = 0;
            $k = 0;

            //遍历panel_91wenwen_panelist_91jili_connection表
            $connection_current = getJiliConnectionByPanelistId($panelist_91jili_connection_file_handle, $panelist_id, $connection_current);
            $jili_cross_id = ($connection_current['matched'] == 1) ? $connection_current['jili_id'] : null;

            if ($jili_cross_id) {
                //遍历user_wenwen_cross表
                $cross_found = use_key_value_index($cross_indexs, $jili_cross_id);
                if ($cross_found) {
                    $jili_email = $cross_found['email'];

                    if ($jili_email) {
                        $cross_exist_count++;
                        continue;
                    }
                }
            }

            //遍历panel_91wenwen_pointexchange_91jili_account表
            $exchange_current = getPointExchangeByPanelistId($pointexchange_91jili_account_file_handle, $panelist_id, $exchange_current);
            $exchang_jili_email = ($exchange_current['matched'] == 1) ? $exchange_current['jili_email'] : null;
            if ($exchang_jili_email) {
                $jili_email = $exchang_jili_email;
                $exchange_exist_count++;
                continue;
            }

            //遍历jili user 表
            if (isset($user_indexs[$jili_email])) {
                $both_exist_count = $both_exist_count + 1;
                $user_row = use_file_index($user_indexs, $jili_email, $user_file_handle, true);
                $jili_user_id = $user_row[0];

                //生成新的user数据：拥有两边账号，相同的部分取问问数据
                generate_user_data_both_exsit($panelist_row, $user_row);

                //其他要迁移的数据
                migrate_common($panelist_row, $jili_user_id);

                continue;
            }
            $only_wenwen_count++;

            $max_user_id++;
            $jili_user_id = $max_user_id;

            //生成仅存在问问的账号的user数据
            generate_user_data_only_wenwen($panelist_row, $jili_user_id);

            //新浪数据迁移
            generate_weibo_user_data($panelist_row[0], $jili_user_id);

            //其他要迁移的数据
            migrate_common($panelist_row, $jili_user_id);
        }
    } catch (Exception $e) {
        FileUtil::writeContents($log_handle, "Exception:" . $e->getMessage());
    }

    //user_only
    foreach ($user_indexs as $email => $pointer) {
        fseek($user_file_handle, $pointer);
        $user_row = fgetcsv($user_file_handle);
        export_csv_row($user_row, Constants::$migrate_user_name);
    }

    FileUtil::writeContents($log_handle, "\n\tcross_exist_count:" . $cross_exist_count . "\n\texchange_exist_count:" . $exchange_exist_count . "\n\tboth_exist_count:" . $both_exist_count . "\n\tonly_wenwen_count:" . $only_wenwen_count . "\n\ttotal:" . ($cross_exist_count + $exchange_exist_count + $both_exist_count + $only_wenwen_count));

    FileUtil::writeContents($log_handle, "end!");
    echo (!function_exists('memory_get_peak_usage')) ? '0' : round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB' . "\n";

    echo date('Y-m-d H:i:s') . " end!\r\n\r\n";

    fclose($log_handle);
}

function migrate_common($panelist_row, $jili_user_id)
{

    //问问的账号的password数据迁移到user_wenwen_login
    generate_user_wenwen_login_data($panelist_row, $jili_user_id);

    //sop_respondent数据迁移
    generate_sop_respondent_data($panelist_row[0], $jili_user_id);

    //vote_answer 数据迁移
    generate_vote_answer_data($panelist_row[0], $jili_user_id);
}

do_process();


