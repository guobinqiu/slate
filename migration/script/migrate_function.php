<?php

# predefined global variables
//file handle
$panelist_file_handle = '';
$panelist_detail_file_handle = '';
$panelist_profile_file_handle = '';
$panelist_profile_image_file_handle = '';
$panelist_mobile_number_file_handle = '';
$panelist_point_file_handle = '';
$panelist_sina_connection_file_handle = '';
$pointexchange_91jili_account_file_handle = '';
$vote_answer_file_handle = '';
$panelist_91jili_connection_file_handle = '';
$user_file_handle = '';
$user_wenwen_cross_file_handle = '';
$weibo_user_file_handle = '';
$migration_region_mapping_file_handle = '';
$sop_respondent_file_handle = '';
$ssi_respondent_file_handle = '';

//索引
$panelist_image_indexs = '';
$panelist_point_indexs = '';
$panelist_mobile_indexs = '';
$region_mapping_indexs = '';
$panelist_detail_indexs = '';
$panelist_profile_indexs = '';
$sop_respondent_indexs = '';
$ssi_respondent_indexs = '';
$vote_answer_indexs = '';
$sina_connection_indexs = '';
$weibo_user_indexs = '';

// weibo_ids
$open_ids_dismatched = array();

// initialise csv file handle, create index
function initialise_csv()
{

    //check file
    global $panelist_file_handle;
    global $panelist_profile_file_handle;
    global $panelist_detail_file_handle;
    global $panelist_profile_image_file_handle;
    global $panelist_mobile_number_file_handle;
    global $panelist_point_file_handle;
    global $panelist_sina_connection_file_handle;
    global $pointexchange_91jili_account_file_handle;
    global $vote_answer_file_handle;
    global $panelist_91jili_connection_file_handle;
    global $sop_respondent_file_handle;
    global $ssi_respondent_file_handle;
    global $user_file_handle;
    global $user_wenwen_cross_file_handle;
    global $weibo_user_file_handle;
    global $migration_region_mapping_file_handle;

    $panelist_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panelist.csv");
    $panelist_profile_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile.csv");
    $panelist_detail_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_detail.csv");
    $panelist_profile_image_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile_image.csv");
    $panelist_mobile_number_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_mobile_number.csv");
    $panelist_point_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_point.csv");
    $panelist_sina_connection_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_sina_connection.csv");
    $pointexchange_91jili_account_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_pointexchange_91jili_account.csv");
    $vote_answer_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/" . VOTE_ANSWER . ".csv");
    $panelist_91jili_connection_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/panel_91wenwen_panelist_91jili_connection.csv");
    $sop_respondent_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/sop_respondent.csv");
    $ssi_respondent_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . "/ssi_respondent.csv");
    $user_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . "/user.csv");
    $user_wenwen_cross_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . "/user_wenwen_cross.csv");
    $weibo_user_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . "/weibo_user.csv");
    $migration_region_mapping_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . "/migration_region_mapping.csv");

    //创建索引
    global $panelist_image_indexs;
    global $panelist_point_indexs;
    global $panelist_mobile_indexs;
    global $region_mapping_indexs;
    global $panelist_detail_indexs;
    global $panelist_profile_indexs;
    global $sina_connection_indexs;
    global $sop_respondent_indexs;
    global $ssi_respondent_indexs;
    global $vote_answer_indexs;
    global $weibo_user_indexs;

    $panelist_image_indexs = build_key_value_index($panelist_profile_image_file_handle, 'panelist_id', 'l_file');
    $panelist_point_indexs = build_key_value_index($panelist_point_file_handle, 'panelist_id', 'point_value');
    $panelist_mobile_indexs = build_key_value_index($panelist_mobile_number_file_handle, 'panelist_id', 'mobile_number');
    $region_mapping_indexs = build_file_index($migration_region_mapping_file_handle, 'region_id');
    $panelist_detail_indexs = build_file_index($panelist_detail_file_handle, 'panelist_id');
    $panelist_profile_indexs = build_file_index($panelist_profile_file_handle, 'panelist_id');
    $sina_connection_indexs = build_file_index($panelist_sina_connection_file_handle, 'panelist_id');
    $sop_respondent_indexs = build_file_index($sop_respondent_file_handle, 'panelist_id');
    $ssi_respondent_indexs = build_file_index($ssi_respondent_file_handle, 'panelist_id');
    $vote_answer_indexs = build_file_index($vote_answer_file_handle, 'panelist_id');
    $weibo_user_indexs = build_file_index($weibo_user_file_handle, 'user_id');

    // insert title for merged user, in order to build index
    export_csv_row(Constants::$jili_user_title, Constants::$migrate_user_name);
    export_csv_row(Constants::$user_wenwen_login_title, Constants::$migrate_user_wenwen_login_name);
}

/**
 * @param $fh  the csv file handler
 * @param $key_name    索引的关键字列名
 * @param $val_name   索引的键值列名
 * @return array($key_name_data => array( $val_name_data) )
 */
function build_key_value_index($fh, $key_name, $val_name)
{
    rewind($fh);
    $title = fgets($fh);
    $key_pos = strpos($title, $key_name);
    $val_pos = strpos($title, $val_name);

    if (false === $key_pos || false === $val_pos) {
        return; //
    }
    if ($key_pos > $val_pos) {
        $min_pos = $val_pos;
        $max_pos = $key_pos;
    } else if ($key_pos < $val_pos) {
        $min_pos = $key_pos;
        $max_pos = $val_pos;
    } else {
        $min_pos = $val_pos;
        $max_pos = $min_pos;
    }

    $min_col_seq = substr_count(substr($title, 0, $min_pos), ',');
    $max_col_seq = substr_count(substr($title, 0, $max_pos), ',');

    $index = array ();
    $csv_line = '';
    while ($row = fgets($fh )) {
        $csv_line .= $row;
        if( "\r\n" == substr($row, -2) ) {
            continue;
        }

        $min_head = 0;
        for ($i = $min_col_seq; $i > 0; $i--) {
            $min_head = strpos($csv_line, ',', $min_head) + 1;
        }
        $min_tail = strpos($csv_line, ',', $min_head);
        $min_col_value = strtolower(substr($csv_line, $min_head, $min_tail - $min_head ));
        $min_col_value = trim($min_col_value ,  '"');

        if ($key_pos == $val_pos) {
            $index[$min_col_value] = array (
                $val_name => $min_col_value
            );
            continue;
        }
        $max_head = $min_tail;
        for ($i = $max_col_seq - $min_col_seq; $i > 0; $i--) {
            $max_head = strpos($csv_line, ',', $max_head) + 1;
        }

        $max_tail = strpos($csv_line, ',', $max_head);

        if ($max_tail === false) {
            $max_tail = strlen($csv_line) - 1;
        }

        $max_col_value = strtolower(substr($csv_line, $max_head , $max_tail - $max_head ));
        $max_col_value = trim($max_col_value , '"');

        if ($key_pos > $val_pos) {
            $index[$max_col_value] = array (
                $val_name => $min_col_value
            );
        } else {
            $index[$min_col_value] = array (
                $val_name => $max_col_value
            );
        }

        $csv_line = '';
    }

    return $index;
}

/**
 * 使用索引
 * @param index  索引array
 * @param key_val  关键字
 * @param with_unset  是否从索引中删除找到了内容
 * @return array(val_name  => val_data),  'val_name': 查找对象的名称
 */
function use_key_value_index(&$index, $key_val, $with_unset = true)
{
    if (!isset($index[$key_val])) {
        return;
    }
    $found = $index[$key_val];
    if ($with_unset) {
        unset($index[$key_val]);
    }
    return $found;
}

/**
 * return array( 'col_value'=> array('pointer=> to_line));
 */
function build_file_index($fh, $col_name = 'panelist_id')
{
    rewind($fh);
    $title = fgets($fh);
    $col_pos = strpos($title, $col_name);
    if (false === $col_pos) {
        return; //
    }


    $col_seq = substr_count(substr($title, 0, $col_pos  ), ',');

    $p = ftell($fh);

    $built = array ();
    $csv_line = '';
    while ($row = fgets($fh)) {
        $csv_line .= $row;
        if( "\r\n" == substr($row, -2) ) {
           continue;
        }

        $head_pos = 0;

        for ($i = $col_seq; $i > 0; $i--) {
            $head_pos = strpos($csv_line, ',', $head_pos) + 1;
        }

        $tail_pos = strpos($csv_line, ',', $head_pos );
        $col_value = strtolower(substr($csv_line, $head_pos , $tail_pos - $head_pos ));
        $col_value  = trim($col_value, '"');

        $built[$col_value] = $p;

        $p = ftell($fh);
        $csv_line = '';
    }

    return $built;
}

function use_file_index(&$index, $col_val, $fh, $with_unset = true)
{
    if (!isset($index[$col_val])) {
        return;
    }

    fseek($fh, $index[$col_val]);
    if ($with_unset) {
        unset($index[$col_val]);
    }

    return fgetcsv($fh,2048, ',','"','"');
}

/**
 * @param $fh file hanlder
 * @return  array( cross_id=> $email) ;
 */
function get_max_user_id($fh)
{
    rewind($fh);
    $max_id = 0;
    while ($row = fgets($fh)) {
        $id_pos = strpos($row, ',');

        if (false === $id_pos) {
            continue;
        }
        $id = (int) trim(substr($row, 0, $id_pos ));

        if ($max_id < $id) {
            $max_id = $id;
        }
    }
    return $max_id;
}

/**
 * 遍历panel_91wenwen_panelist_91jili_connection表
 *
 * "panelist_id","jili_id","status_flag","stash_data","updated_at","created_at"
 * "305","16980","1","NULL","2015-01-07 16:50:12","2015-01-07 16:50:12"
 * @return jili_id or null
 */
function getJiliConnectionByPanelistId($fh, $panelist_id_input, array $current)
{
    if (empty($panelist_id_input)) {
        $current['matched'] = 0;
        return $current;
    }

    if (isset($current['panelist_id'])) {
        if ($panelist_id_input == $current['panelist_id']) {
            $current['matched'] = 1;
            return $current;
        } else if ($panelist_id_input < $current['panelist_id']) {
            $current['matched'] = 0;
            return $current;
        }
    } else {
        rewind($fh);
        fgets($fh);
    }

    $current['matched'] = 0;
    // the input panelist_id > current panelist_id, move next line.
    while ($row = fgets($fh, 1024)) {
        $panelist_id_pos = strpos($row, ',');
        $current['panelist_id'] = substr($row, 1, $panelist_id_pos - 2);

        if ($panelist_id_input <= $current['panelist_id']) {
            $jili_id_pos = strpos($row, ',', $panelist_id_pos + 1);
            $current['jili_id'] = substr($row, $panelist_id_pos + 2, $jili_id_pos - $panelist_id_pos - 3);

            if ($panelist_id_input != $current['panelist_id']) {
                break;
            }

            $status_flag_pos = strpos($row, ',', $jili_id_pos + 1);

            $status_flag = substr($row, $jili_id_pos + 2, $status_flag_pos - $jili_id_pos - 3);
            // need  to check to status_flag
            if ($status_flag != 1) {
                break;
            }

            $current['matched'] = 1;
            break;
        }
    }

    return $current;
}

/**
 *  遍历user_wenwen_cross表
 * "id","user_id","created_at","email"
 * "5629","1270570","2014-11-26 16:32:00","NULL"
 * @return array( 'matched'=> , email => , id )
 */
function getUserWenwenCrossById($fh, $id_input, $current)
{
    if (empty($id_input)) {
        $current['matched'] = 0;
        return $current;
    }

    if (isset($current['id'])) {
        if ($id_input == $current['id']) {
            $current['matched'] = 1;
            return $current;
        } else if ($id_input < $current['id']) {
            $current['matched'] = 0;
            return $current;
        }
    } else {
        rewind($fh);
        fgets($fh);
    }

    $current['matched'] = 0;
    while ($row = fgets($fh, 1024)) {

        $id_pos = strpos($row, ',');
        $current['id'] = substr($row, 1, $id_pos - 2);

        if ($id_input <= $current['id']) {
            $email = substr($row, strrpos($row, ',') + 2, -2);
            $current['email'] = ('NULL' == $email) ? null : $email;
            if ($id_input == $current['id']) {
                $current['matched'] = 1;
            }
            break;
        }
    }
    return $current;
}

function getUserWenwenCross($fh)
{
    rewind($fh);
    fgets($fh);
    $a = array ();

    while ($row = fgets($fh, 1024)) {

        $id_pos = strpos($row, ',');
        $email = substr($row, strrpos($row, ',') + 2, -2);
        $a[substr($row, 1, $id_pos - 2)] = ('NULL' == $email) ? null : $email;
    }
    return $a;
}

/**
 * @param $fh file hanlder
 * @return  array( cross_id=> $email) ;
 */
function getUser($fh)
{
    rewind($fh);
    fgets($fh);

    $p = ftell($fh); // the pointer for each row
    $r = fgets($fh);
    $data = array ();

    while (!feof($fh)) {

        $id_pos = strpos($r, ',');
        $email_pos = strpos($r, ',', $id_pos + 1);

        $email = substr($r, $id_pos + 2, $email_pos - $id_pos - 3);
        $data[$email] = array (
            'id' => substr($r, 1, $id_pos - 2),
            'pointer' => $p
        );

        $p = ftell($fh);
        $r = fgets($fh);
    }

    return $data;
}

/**
 * 遍历panel_91wenwen_pointexchange_91jili_account表
 *
 * "panelist_id","jili_email","status_flag","stash_data","updated_at","created_at"
 * "305","28216843@qq.com","1","NULL","2014-02-24 10:21:34","2014-02-20 11:58:08"
 * "2229759","syravia@gmail.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/944966ca79a14e49c74009896922bf13/1436557""}","2015-11-16 11:38:00","2015-11-16 11:38:00"
 * @return array('matched'=> ,jili_email=> panelist_id=>)  or null
 */
function getPointExchangeByPanelistId($fh, $panelist_id_input, $current )
{
    if (empty($panelist_id_input)) {
        $current['matched'] = 0;
        return $current;
    }

    if (isset($current['panelist_id'])) {
        if ($panelist_id_input == $current['panelist_id']) {
            $current['matched'] = 1;
            return $current;
        } else if ($panelist_id_input < $current['panelist_id']) {
            $current['matched'] = 0;
            return $current;
        }
    } else {
        rewind($fh);
        fgets($fh);
    }

    $current['matched'] = 0;
    // the input panelist_id > current panelist_id, move next line.
    while ($row = fgets($fh, 2048)) {
        $panelist_id_pos = strpos($row, ',');
        $current['panelist_id'] = substr($row, 1, $panelist_id_pos - 2);
        if ($panelist_id_input <= $current['panelist_id']) {

            $jili_email_pos = strpos($row, ',', $panelist_id_pos + 1);
            $current['jili_email'] = substr($row, $panelist_id_pos + 2, $jili_email_pos - $panelist_id_pos - 3);

            if ($panelist_id_input != $current['panelist_id']) {
                break;
            }

            $status_flag_pos = strpos($row, ',', $jili_email_pos + 1);
            $status_flag = substr($row, $jili_email_pos + 2, $status_flag_pos - $jili_email_pos - 3);
            if ($status_flag != 1) {
                break;
            }
            $current['matched'] = 1;
            break;
        }
    }
    return $current;
}

/**
 * Export the user data of both exist on wenwen and jili
 * @param array $panelist_row One line data of panelist csv
 * @param array $user_row  One line data of user csv
 * @return  void
 */
function generate_user_data_both_exsit($panelist_row, $user_row)
{
    $user_row = generate_user_data_wenwen_common($panelist_row, $user_row);
    //origin_flag
    $user_row[32] = Constants::$origin_flag['wenwen_jili'];
    $user_row = set_default_value($user_row);
    export_csv_row($user_row, Constants::$migrate_user_name);
    export_history_data($panelist_row[0], $user_row[0]);
}

/**
 * Export the user data of only exist on wenwen
 * @param array $panelist_row One line data of panelist csv
 * @param integer $user_id
 * @return void
 */
function generate_user_data_only_wenwen($panelist_row, $user_id)
{
    $user_row = generate_user_data_wenwen_common($panelist_row);
    //id
    $user_row[0] = $user_id;
    //is_from_wenwen
    $user_row[4] = Constants::$is_from_wenwen['wenwen_only'];
    //reward_multiple
    $user_row[20] = 1;
    //origin_flag
    $user_row[32] = Constants::$origin_flag['wenwen'];
    $user_row = set_default_value($user_row);
    export_csv_row($user_row, Constants::$migrate_user_name);
    export_history_data($panelist_row[0], $user_id);
}

/**
 * Generate the user jili only data
 * @param array $row  , the row in jili user csv
 * @return  array $row
 */
function generate_user_data_only_jili($row = array())
{
    // is_email_confirmed
    if ($row[2]) {
        //user has password, set is_email_confirmed = 1
        $row[3] = 1;
    } else {
        //user password is null, is_email_confirmed = 0
        $row[3] = 0;
    }

    //origin_flag
    $row[32] = Constants::$origin_flag['jili'];
    //password_choice
    $row[36] = Constants::$password_choice['pwd_jili'];
    $row = set_default_value($row);
    export_csv_row($row, Constants::$migrate_user_name);
}

/**
 * Generate the user common data
 * @param array $panelist_row One line data of panelist csv
 * @param array $user_row One line data of user csv
 * @return  array $user_row
 */
function generate_user_data_wenwen_common($panelist_row, $user_row = array())
{
    if(empty($panelist_row)) {
        return '';
    }
    //email
    $user_row[1] = $panelist_row[3];

    //password
    if( ! isset($user_row[2]) ) {
        $user_row[2] = ''; //$panelist_row[5];
    }

    //is_email_confirmed
    $user_row[3] = 1;

    // token
    $user_row[6] = '';

    //sex
    if('' === $panelist_row[13])  {
        $user_row[8] = 'NULL';
    } else{
        $user_row[8] = $panelist_row[13];
    }

    //birthday varchar(50) :1986-8 (panelist.birthday:1983-12-01)
    if( ! empty($panelist_row[14]) ) {
        $user_row[9] = $panelist_row[14];
    }

    //register_date (panelist.created_at)
    $user_row[21] = get_one_hour_ago_time($panelist_row[9]);

    //last_login_date(panelist.panelist.last_login_time)
    $user_row[23] = get_one_hour_ago_time($panelist_row[17]);

    //last_login_ip
    //$user_row[24]';


    //delete_flag todo: 是否要查看问问的黑名单处理
    $user_row[26] = 0;

    //is_info_set
    $user_row[28] = 1;

    //created_remote_addr
    $user_row[33] = $panelist_row[10];

    //created_user_agent
    $user_row[34] = $panelist_row[11];

    //campaign_code
    $user_row[35] = $panelist_row[16];

    //password_choice
    $user_row[36] = Constants::$password_choice['pwd_wenwen'];

    //tel: panel_91wenwen_panelist_mobile_number.mobile_number
    global $panelist_mobile_indexs;
    if (isset($panelist_mobile_indexs[$panelist_row[0]])) {
        $user_row[10] = $panelist_mobile_indexs[$panelist_row[0]]['mobile_number'];

        //is_tel_confirmed
        $user_row[11] = 1;
    }

    //province , city : panelist.panel_region_id
    global $region_mapping_indexs;
    global $migration_region_mapping_file_handle;
    if (isset($region_mapping_indexs[$panelist_row[1]])) {
       $region_mapping_row = use_file_index($region_mapping_indexs, $panelist_row[1], $migration_region_mapping_file_handle, false);
        //province
        $user_row[12] = $region_mapping_row[1];
        //city
        $user_row[13] = $region_mapping_row[2];
    } else {
#        if(empty($user_row[12])) {
#            $user_row[12] = 'NULL';
#        }
#        if(empty($user_row[13])) {
#            $user_row[13] = 'NULL';
#        }

    }

    global $panelist_detail_indexs;
    global $panelist_detail_file_handle;
    if (isset($panelist_detail_indexs[$panelist_row[0]])) {
        $panelist_detail_row = use_file_index($panelist_detail_indexs, $panelist_row[0], $panelist_detail_file_handle, true);
        //education: detail.graduation_code
        if($panelist_detail_row[30] === '') {
            $user_row[14] = 'NULL';
        }else {
            $user_row[14] = $panelist_detail_row[30];
        }

        //income : detail.income_personal_code
        if( $panelist_detail_row[26] === ''||$panelist_detail_row[26] ===  '0' ||$panelist_detail_row[26] ===  'NULL' ) {
            $user_row[16] ='NULL';
        } else {
            if ( isset(Constants::$income[$panelist_detail_row[26]]) ) {
                $user_row[16] = Constants::$income[$panelist_detail_row[26]];
            }else  {
                throw new \Exception($panelist_detail_row[26] . ' income mapping not defined');
            }
        }

        //profession: detail.detail.job_code
        if( $panelist_detail_row[27] === '') {
            $user_row[15] ='NULL';
        } else {
            $user_row[15] = $panelist_detail_row[27];
        }


        //industry_code: detail.industry_code
        if($panelist_detail_row[28] === '') {
            $user_row[39] ='NULL';
        } else {
            $user_row[39] = $panelist_detail_row[28];
        }

        //work_section_code: detail.work_section_code
        if( $panelist_detail_row[29] === '' ) {
            $user_row[40] ='NULL';
        } else {
            $user_row[40] = $panelist_detail_row[29];
        }
    } else {
#        //education: detail.graduation_code
#        if(empty($user_row[14] )) {
#            $user_row[14] = 'NULL';
#        }
#
#        //income : detail.income_personal_code
#        if(empty($user_row[16] )) {
#            $user_row[16] ='NULL';
#        }
#
#        //profession: detail.detail.job_code
#        if(empty($user_row[15] )) {
#            $user_row[15] ='NULL';
#        }
#
#
#        //industry_code: detail.industry_code
#        if(empty($user_row[39] )) {
#            $user_row[39] ='NULL';
#        }
#
#        //work_section_code: detail.work_section_code
#        if(empty($user_row[40] )) {
#            $user_row[40] ='NULL';
#        }

    }

    global $panelist_profile_indexs;
    global $panelist_profile_file_handle;

    if (isset($panelist_profile_indexs[$panelist_row[0]])) {
        $panelist_profile_row = use_file_index($panelist_profile_indexs, $panelist_row[0], $panelist_profile_file_handle, true);

        //nick profile.nickname
        $user_row[7] = $panelist_profile_row[2];

        //hobby: profile.hobby
        // $user_row[17] = $panelist_profile_row[6];

        //personalDes: profile.biography
        $user_row[18] = addslashes($panelist_profile_row[5]);

        //fav_music: profile.fav_music
        $user_row[37] = $panelist_profile_row[7];
        //monthly_wish:profile.monthly_wish
        $user_row[38] = $panelist_profile_row[8];
    }

    //points: panel_91wenwen_panelist_point.point_value
    global $panelist_point_indexs;


    if (isset($panelist_point_indexs[$panelist_row[0]])) {

        //points
        if( !isset($user_row[24]) ) {
            $user_row[25] =  0;
        }
        $user_row[25]  +=  (int) $panelist_point_indexs[$panelist_row[0]]['point_value'];
    } else {
        if( ! isset($user_row[24])  ){
            $user_row[25] = 0;
        }
    }

    //icon_path:panelist_profile_image
    global $panelist_image_indexs;
    if (isset($panelist_image_indexs[$panelist_row[0]])) {
        $user_row[29] = 'uploads/user/' . $panelist_image_indexs[$panelist_row[0]]['l_file'];
    }
    return $user_row;
}

/**
 * Set default value
 * @param array $user_row
 * @return  array $user_row
 */
function set_default_value($row)
{

    for ($i = 0; $i <= 40; $i++) {

        if (! isset($row[$i]) ) {
            $row[$i] = 'NULL';
        }
    }

    // is_from_wenwen
    if(''=== $row[4] ) {
        $row[4] = 'NULL';
    }

    // wenwen_user
    if(''=== $row[5] ) {
        $row[5] = 'NULL';
    }

    // nick
    $row[7] = addslashes($row[7]);

//personalDes	text	YES		NULL
    $row[18] = addslashes($row[18]);
    // created_user_agent
    $row[34] = addslashes($row[32]);
// fav_music	varchar(255)	YES		NULL
    $row[37] = addslashes($row[35]);
//monthly_wish	varchar(255)	YES		NULL
    $row[38] = addslashes($row[36]);

    // sex
    if(''===$row[8] ) {
        $row[8] = 'NULL';
    }

// is_tel_confirmed
    if(''===$row[11] ) {
        $row[11] = 'NULL';
    }

    // province
    if(''===$row[12] ) {
        $row[12] = 'NULL';
    }

    // city
    if(''===$row[13] ) {
        $row[13] = 'NULL';
    }

    //education
    if(''===$row[14] ) {
        $row[14] = 'NULL';
    }

    //profession: detail.detail.job_code
    if( $row[15] === '') {
        $row[15] ='NULL';
    }

    //income
    if( $row[16] === '') {
        $row[16] ='NULL';
    }

    //register_complete_date
    $row[22] ='NULL';

    //delete_flag
    if( $row[26] === '') {
        $row[26] ='NULL';
    }

    //delete_date
    $row[27] ='NULL';

    // token_created_at
    if(''=== $row[31] ) {
        $row[31] = 'NULL';
    }

    //industry_code: detail.industry_code
    if(empty($row[39] )) {
        $row[39] ='NULL';
    }

    //work_section_code: detail.work_section_code
    if(empty($row[40] )) {
        $row[40] ='NULL';
    }
    return $row;
}

/**
 * Export the user_wenwen_login data
 * @param array $panelist_row One line data of panelist csv
 * @param integer $user_id
 * @return void
 */
function generate_user_wenwen_login_data($panelist_row, $user_id)
{
    //id
    $user_wenwen_login_row[0] = 'NULL';
    //user_id
    $user_wenwen_login_row[1] = $user_id;
    //login_password_salt
    $user_wenwen_login_row[2] = $panelist_row[7];
    //login_password_crypt_type
    $user_wenwen_login_row[3] = $panelist_row[6];
    //login_password
    $user_wenwen_login_row[4] = $panelist_row[5];
    export_csv_row($user_wenwen_login_row, Constants::$migrate_user_wenwen_login_name);
}

/**
 * Generate the weibo_user data
 * @param integer $panelist_id
 * @param integer $user_id
 * @return void
 */
function generate_weibo_user_data($panelist_id, $user_id)
{
    global $sina_connection_indexs;
    global $panelist_sina_connection_file_handle;

    global $weibo_user_indexs;
    global $weibo_user_file_handle;

    // look for ww_sina_conn
    if (isset($sina_connection_indexs[$panelist_id])) {
        $is_open_id_match = false ;
        $panelist_sina_row = use_file_index($sina_connection_indexs, $panelist_id, $panelist_sina_connection_file_handle);
        // look for jili_web_conn
        if (isset($weibo_user_indexs[$user_id])) {


            $weibo_user_row = use_file_index($weibo_user_indexs, $user_id, $weibo_user_file_handle);
            // different open_id
            if ($panelist_sina_row[1] != $weibo_user_row[2]) {
                global $log_handle;
                FileUtil::writeContents($log_handle, '绑定的微博账号不同, panelist_id: ' .$panelist_id .
                        ' panelist_sina_row[1]: ' . $panelist_sina_row[1] .
                        ' user_id: ' . $user_id .
                        ' weibo_user_row[2]: ' . $weibo_user_row[2]);
                //weibo_user :  change
                //$weibo_user[0] = 'NULL';
            } else {
                $is_open_id_match =  true;
            }

        }  else {
            //$weibo_user_row[0] = 'NULL';
            $weibo_user_row[1] = $user_id;
        }

        //weibo_user :  add
        export_weibo_csv_data($weibo_user_row, $panelist_sina_row);

        if( false == $is_open_id_match) {
            global $open_ids_dismatched;
            $open_ids_dismatched[] = $panelist_sina_row[1] ;
        }
    }
}

/**
 * Export the weibo_user data
 * @param array $weibo_user_row One line data of weibo_user csv
 * @param array $panelist_sina_row One line data of panelist_sina_connection csv
 * @return void
 */
function export_weibo_csv_data($weibo_user_row, $panelist_sina_row)
{

    //open_id
    $weibo_user_row[2] = $panelist_sina_row[1];
    //regist_date
    $weibo_user_row[3] = get_one_hour_ago_time($panelist_sina_row[7]);

    // id
    $weibo_user_row[0] = 'NULL';
    export_csv_row($weibo_user_row, Constants::$migrate_weibo_user_name);
}

/**
 * Export the sop_respondent data
 * @param integer $panelist_id
 * @param integer $user_id
 * @return void
 */
function generate_sop_respondent_data($panelist_id, $user_id)
{
    global $sop_respondent_indexs;
    global $sop_respondent_file_handle;

    if (isset($sop_respondent_indexs[$panelist_id])) {
        $sop_respondent_row = use_file_index($sop_respondent_indexs, $panelist_id, $sop_respondent_file_handle, true);
        $sop_respondent_row[1] = $user_id;
        $sop_respondent_row[4] = get_one_hour_ago_time($sop_respondent_row[4]);
        $sop_respondent_row[5] = get_one_hour_ago_time($sop_respondent_row[5]);
        export_csv_row($sop_respondent_row, Constants::$migrate_sop_respondent_name);
    }
}

function generate_ssi_respondent_data($panelist_id, $user_id)
{
    global $ssi_respondent_indexs;
    global $ssi_respondent_file_handle;

    if (isset($ssi_respondent_indexs[$panelist_id])) {
        $ssi_respondent_row = use_file_index($ssi_respondent_indexs, $panelist_id, $ssi_respondent_file_handle, true);
        $ssi_respondent_row[1] = $user_id;
        $ssi_respondent_row[4] = get_one_hour_ago_time($ssi_respondent_row[4]);
        $ssi_respondent_row[5] = get_one_hour_ago_time($ssi_respondent_row[5]);
        export_csv_row($ssi_respondent_row, Constants::$migrate_ssi_respondent_name);
    }
}

/**
 * Export the vote_answer data
 * @param integer $panelist_id
 * @param integer $user_id
 * @return void
 */
function generate_vote_answer_data($panelist_id, $user_id)
{
    global $vote_answer_indexs;
    global $vote_answer_file_handle;

    if (isset($vote_answer_indexs[$panelist_id])) {
        $vote_answer_row = use_file_index($vote_answer_indexs, $panelist_id, $vote_answer_file_handle, true);
        $vote_answer_row[1] = $user_id;
        $vote_answer_row[4] = get_one_hour_ago_time($vote_answer_row[4]);
        $vote_answer_row[5] = get_one_hour_ago_time($vote_answer_row[5]);
        export_csv_row($vote_answer_row, Constants::$migrate_vote_answer_name);
    }
}

/**
 * Export the task_history and point_history  data
 * @param Integer $point
 * @return void
 */
function export_history_data($panelist_id, $user_id)
{
    global $panelist_point_indexs;

    if (isset($panelist_point_indexs[$panelist_id])) {
        $wenwen_point = $panelist_point_indexs[$panelist_id]['point_value'];
        if ($wenwen_point > 0) {

            $index = $user_id % 10;
            $task_history_name = Constants::$migrate_task_history_name;
            $point_history_name = Constants::$migrate_point_history_name;

            // task_history : id, order_id, user_id, task_type, category_type, task_name, reward_percent, point, ocd_created_date, date, status
            //id
            $task_history[0] = 'NULL';
            //order_id
            $task_history[1] = 0;
            //user_id
            $task_history[2] = $user_id;
            //task_type
            $task_history[3] = 4;
            //category_type
            $task_history[4] = Constants::$ad_category_type_web_merge;
            //task_name
            $task_history[5] = '合并前91问问的积分数';
            //reward_percent
            $task_history[6] = 'NULL';
            //point
            $task_history[7] = $wenwen_point;
            //ocd_created_date
            $task_history[8] = date('Y-m-d H:i:s');
            //date
            $task_history[9] = date('Y-m-d H:i:s');
            //status
            $task_history[10] = 1;

            export_csv_row($task_history, $task_history_name . $index . ".csv");

            // point_history: # id, user_id, point_change_num, reason, create_time
            //id
            $point_history[0] = 'NULL';
            //user_id
            $point_history[1] = $user_id;
            //point_change_num
            $point_history[2] = $wenwen_point;
            //reason
            $point_history[3] = Constants::$ad_category_type_web_merge;
            //create_time
            $point_history[4] = date('Y-m-d H:i:s');

            export_csv_row($point_history, $point_history_name . $index . ".csv");
        }
    }
}

/**
 * Get the time of 1 hour ago
 * @param String $time
 * @return String
 */
function get_one_hour_ago_time($time)
{
    if (empty($time) || $time == 'NULL') {
        return 'NULL';
    }
    return date('Y-m-d H:i:s', strtotime("$time -1 hour"));
}

/**
 * Generate a CSV file
 * @param array $data
 * @param String $file_name
 * @return void
 */
function export_csv_row($data, $file_name )
{
    ksort($data);
     if (  isset(Constants::$environment) &&  Constants::$environment === 'test' ) {
         $file_name = 'test.'.$file_name;
         $handle = fopen(EXPORT_PATH . '/' . $file_name, 'w+');
     } else{
         $handle = fopen(EXPORT_PATH . '/' . $file_name, 'a');
     }
    fputcsv($handle, $data);
    return fclose($handle);
}

function strip_vote_description_links($description)
{
    return preg_replace('/<a\s+href="http:\/\/www\.91wenwen\.net\/user\/?\s*[\w\d]+\s*">(.*)<\/a>/s', '\1', $description);
}

/**
 * generate stash data
 */
function generate_vote_choice_stash_data($choice)
{
    $stash_data['choices'] = $choice;
    return json_encode($stash_data,JSON_UNESCAPED_UNICODE );
}

/**
 * migrate process
 * @param array $panelist_row One line data of panelist csv
 * @param integer $jili_user_id $time
 * @return void
 */
function migrate_common($panelist_row, $jili_user_id)
{
    //问问的账号的password数据迁移到user_wenwen_login
    generate_user_wenwen_login_data($panelist_row, $jili_user_id);

    //新浪数据迁移
    generate_weibo_user_data($panelist_row[0], $jili_user_id);

    //sop_respondent数据迁移
    generate_sop_respondent_data($panelist_row[0], $jili_user_id);


    //ssi_respondent数据迁移
    generate_ssi_respondent_data($panelist_row[0], $jili_user_id);

    //vote_answer 数据迁移
    generate_vote_answer_data($panelist_row[0], $jili_user_id);
}
