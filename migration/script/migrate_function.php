<?php

# predefined global variables


//panel_91wenwen_panelist_mobile_number
$panelist_mobile_data = array ();
//migration_region_mapping
$region_mapping_data = array ();
//panel_91wenwen_panelist_detail
$panelist_detail_data = array ();
//panel_91wenwen_panelist_profile
$panelist_profile_data = array ();
//panel_91wenwen_panelist_point
$panelist_point_data = array ();
//panel_91wenwen_panelist_profile_image
$panelist_profile_image_data = array ();
//panel_91wenwen_panelist_sina_connection
$panelist_sina_data = array ();

# load csv lines into 2-dim array
function initialise_csv()
{
    //export csv file
    export_csv_row(Constants::$jili_user_title, Constants::$migrate_user_name);
    export_csv_row(Constants::$jili_user_title, Constants::$migrate_user_only_wenwen_name);
    export_csv_row(Constants::$user_wenwen_login_title, Constants::$migrate_user_wenwen_login_name);
    export_csv_row(Constants::$weibo_user_title, Constants::$migrate_weibo_user_name);
    export_csv_row(Constants::$sop_respondent_title, Constants::$migrate_sop_respondent_name);
    export_csv_row(Constants::$vote_answer_title, Constants::$migrate_vote_answer_name);

    //import csv
    $panelist_mobile_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_mobile_number.csv');
    $region_mapping_data = FileUtil::readCsvContent(IMPORT_JL_PATH . '/migration_region_mapping.csv');
    $panelist_detail_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_detail.csv');
    $panelist_profile_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_profile.csv');
    $panelist_point_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_point.csv');
    $panelist_profile_image_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_profile_image.csv');
    $panelist_sina_data = FileUtil::readCsvContent(IMPORT_WW_PATH . '/panel_91wenwen_panelist_sina_connection.csv');
}

/** 
 * @param $fh  the csv file handler
 * @param $key_name    索引的关键字列名
 * @param $val_name   索引的键值列名
 * @return array($key_name_data => array( $val_name_data) )
 */
function build_index_by_selected($fh, $key_name, $val_name) 
{
  rewind($fh);
  $title =  fgets($fh);
  $key_pos = strpos( $title, $key_name); 
  $val_pos = strpos( $title, $val_name); 


  if( false === $key_pos ||false === $val_pos ) {
    return ; // 
  }
  if( $key_pos > $val_pos) { 
    $min_pos =  $val_pos ;
    $max_pos =  $key_pos ;
  } else if($key_pos < $val_pos)  {
    $min_pos =  $key_pos ;
    $max_pos =  $val_pos ;
  } else {
    $min_pos =  $val_pos ;
    $max_pos =  $min_pos ;
  }


  $min_col_seq = substr_count(substr( $title, 0, $min_pos),',' );
  $max_col_seq = substr_count(substr( $title, 0, $max_pos),',' );


  $index = array(); 
  while ($row = fgets($fh, 1024)) {

    $min_head = 0;
    for( $i = $min_col_seq; $i>0; $i-- ) {
      $min_head = strpos($row, ',', $min_head) + 1;
    }
    $min_tail = strpos($row, ',',$min_head);
    $min_col_value =  substr($row, $min_head + 1 , $min_tail - $min_head - 2 ); 

    if( $key_pos ==  $val_pos) { 
      $index [$min_col_value] = array( $val_name => $min_col_value);
      continue;
    }
    $max_head = $min_tail ;
    for( $i = $max_col_seq - $min_col_seq; $i>0; $i-- ) {
      $max_head = strpos($row, ',',  $max_head ) + 1;
    }

    $max_tail = strpos($row, ',', $max_head );

    if( $max_tail === false ) {
      $max_tail = strlen($row) -1;
    }

    $max_col_value =  substr($row, $max_head + 1 , $max_tail - $max_head - 2 ); 

    if( $key_pos >   $val_pos) { 
      $index [$max_col_value] = array( $val_name => $min_col_value);
    } else {
      $index [$min_col_value] = array( $val_name => $max_col_value);
    }
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
function use_index_by_selected(&$index, $key_val,  $with_unset= true) 
{
  if(!  isset($index[$key_val])    ) {
    return;
  } 
  $found = $index[$key_val];
  if( $with_unset) {
    unset($index[$key_val]);
  }
  return $found;
}



/**
 * return array( 'col_value'=> array('pointer=> to_line));
 */
function build_index_by_panelist_id($fh, $col_name = 'panelist_id')
{
    rewind($fh);
    $title = fgets($fh);
    $col_pos = strpos($title, $col_name);
    if (false === $col_pos) {
        return; //
    }

    $col_seq = substr_count(substr($title, 0, $col_pos), ',');

    $p = ftell($fh);

    $built = array ();
    while ($row = fgets($fh)) {

        $head_pos = 0;

        for ($i = $col_seq; $i > 0; $i--) {
            $head_pos = strpos($row, ',', $head_pos) + 1;
        }

        $tail_pos = strpos($row, ',', $head_pos + 1);

        $col_value = substr($row, $head_pos + 1, $tail_pos - $head_pos - 2);

        $built[$col_value] = array (
            'point' => $p
        );

        $p = ftell($fh);
    }

    return $built;
}

function use_index_by_panelist_id(&$index, $col_val, $fh , $with_unset = true)
{
  if( ! isset( $index[$col_val]) ) {
    return ;
  }

  fseek($fh, $index[$col_val]['point']);
  if ( $with_unset) {
    unset($index[$col_val]);
  }
  return fgetcsv($fh);
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
 * "panelist_id","jili_email","status_flag","stash_data","updated_at","created_at"
 * "305","28216843@qq.com","1","NULL","2014-02-24 10:21:34","2014-02-20 11:58:08"
 * "2229759","syravia@gmail.com","0","{""activation_url"":""https://www.91jili.com/user/setPassFromWenwen/944966ca79a14e49c74009896922bf13/1436557""}","2015-11-16 11:38:00","2015-11-16 11:38:00"
 * @return array('matched'=> ,jili_email=> panelist_id=>)  or null
 */
function getPointExchangeByPanelistId($fh, $panelist_id_input, $current)
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

//user data of both exist on wenwen and jili
function generate_user_data_both_exsit($panelist_row, $user_row)
{
    $user_row = generate_user_data_wenwen_common($panelist_row, $user_row);

    //origin_flag
    $user_row[30] = Constants::$origin_flag['wenwen_jili'];

    return $user_row;
}

//user data of only exist on wenwen
function generate_user_data_only_wenwen($panelist_row, $user_id)
{
    $user_row = generate_user_data_wenwen_common($panelist_row);

    $user_row[0] = $user_id;

    //is_email_confirmed
    $user_row[3] = 1;

    //reward_multiple
    $user_row[20] = 1;

    //origin_flag
    $user_row[30] = Constants::$origin_flag['wenwen'];

    for ($i = 0; $i <= 38; $i++) {
        if (!isset($user_row[$i])) {
            $user_row[$i] = null;
        }
    }
    export_csv_row($user_row, Constants::$migrate_user_only_wenwen_name);
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
    $user_row[9] = $panelist_row[9];

    //register_date
    $user_row[21] = $panelist_row[9];

    //last_login_date(todo: 格式转化)
    $user_row[22] = $panelist_row[17];

    //created_remote_addr
    $user_row[31] = $panelist_row[10];

    //created_user_agent
    $user_row[32] = $panelist_row[11];

    //campaign_code
    $user_row[33] = $panelist_row[16];

    //password_choice
    $user_row[34] = Constants::$password_choice['pwd_wenwen'];

    //tel: panel_91wenwen_panelist_mobile_number.mobile_number
    global $panelist_mobile_data;
    foreach ($panelist_mobile_data as $panelist_mobile_row) {
        if ($panelist_row[0] == $panelist_mobile_row[0]) {
            $user_row[10] = $panelist_mobile_row[1];
            break;
        }
    }

    //province , city : panelist.panel_region_id
    global $region_mapping_data;
    foreach ($region_mapping_data as $region_mapping_row) {
        if ($panelist_row[1] == $region_mapping_row[0]) {
            //province
            $user_row[12] = $region_mapping_row[1];
            //city
            $user_row[13] = $region_mapping_row[2];
            break;
        }
    }

    global $panelist_detail_data;
    foreach ($panelist_detail_data as $panelist_detail_row) {
        if ($panelist_row[0] == $panelist_detail_row[0]) {

            //education: detail.graduation_code
            $user_row[14] = $panelist_detail_row[30];

            //profession: detail.detail.job_code
            $user_row[15] = $panelist_detail_row[27];

            //income : detail.income_personal_code
            $user_row[16] = $panelist_detail_row[26];

            //industry_code: detail.industry_code
            $user_row[37] = $panelist_detail_row[31];

            //work_section_code: detail.work_section_code
            $user_row[38] = $panelist_detail_row[29];

            break;
        }
    }

    global $panelist_profile_data;
    foreach ($panelist_profile_data as $panelist_profile_row) {

        if ($panelist_row[0] == $panelist_profile_row[1]) {
            //hobby: profile.hobby
            $user_row[17] = $panelist_profile_row[6];

            //personalDes: profile.biography
            $user_row[18] = $panelist_profile_row[5];

            //fav_music: profile.fav_music
            $user_row[35] = $panelist_profile_row[7];

            //monthly_wish:profile.monthly_wish
            $user_row[36] = $panelist_profile_row[8];

            break;
        }
    }

    //last_login_ip todo
    //$user_row[23] = 'todo';


    //points: panel_91wenwen_panelist_point.point_value
    global $panelist_point_data;
    foreach ($panelist_point_data as $panelist_point_row) {
        if ($panelist_row[0] == $panelist_point_row[0]) {
            $user_row[24] = $user_row[24] + $panelist_point_row[1];
            break;
        }
    }

    //icon_path:panelist_profile_image
    global $panelist_profile_image_data;
    foreach ($panelist_profile_image_data as $panelist_profile_image_row) {
        if ($panelist_row[0] == $panelist_profile_image_row[0]) {
            $user_row[27] = $panelist_profile_image_row[1];
            break;
        }
    }
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

    export_csv_row($user_wenwen_login_row, Constants::$migrate_user_wenwen_login_name);
}

//weibo_user data
function generate_weibo_user_data($panelist_id, $user_id)
{
    $weibo_user_row = array ();
    global $panelist_sina_data;
    foreach ($panelist_sina_data as $panelist_sina_key => $panelist_sina_row) {
        if ($panelist_id == $panelist_sina_row[0]) {
            //id
            $weibo_user_row[0] = null;

            //user_id
            $weibo_user_row[1] = $user_id;

            //open_id
            $weibo_user_row[2] = $panelist_sina_row[1];

            //regist_date
            $weibo_user_row[3] = $panelist_sina_row[7];

            export_csv_row($weibo_user_row, Constants::$migrate_weibo_user_name);
            unset($panelist_sina_data[$panelist_sina_key]);

            break;
        }
    }
}

function generate_sop_respondent_data($panelist_id, $user_id)
{
    global $sop_respondent_data;
    foreach ($sop_respondent_data as $sop_respondent_key => $sop_respondent_row) {
        if ($panelist_id == $sop_respondent_row[1]) {
            $sop_respondent_row[1] = $user_id;

            export_csv_row($sop_respondent_row, Constants::$migrate_sop_respondent_name);
            unset($sop_respondent_data[$sop_respondent_key]);
        }
    }
}

function generate_vote_answer_data($panelist_id, $user_id)
{
    global $vote_answer_data;
    foreach ($vote_answer_data as $vote_answer_key => $vote_answer_row) {
        if ($panelist_id == $vote_answer_row[1]) {
            $vote_answer_row[1] = $user_id;

            export_csv_row($vote_answer_row, Constants::$migrate_sop_respondent_name);
            unset($vote_answer_data[$vote_answer_key]);
        }
    }
}

function export_csv_row($data, $file_name)
{
    $csvline[] = FileUtil::joinCsv($data);

    // generate a csv file
    $path = EXPORT_PATH . "/" . $file_name;
    $handle = fopen($path, "w");
    fwrite($handle, implode("\n", $csvline));
    fclose($handle);
}

function export_csv($datas, $file_name)
{
    $csvline = array ();

    // prepare the output content
    foreach ($datas as $data) {
        $csvline[] = FileUtil::joinCsv($data);
    }
    // generate a csv file
    $path = EXPORT_PATH . "/" . $file_name;
    $handle = fopen($path, "w");
    fwrite($handle, implode("\n", $csvline));
    fclose($handle);
}
