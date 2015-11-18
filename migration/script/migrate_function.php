<?php
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
    $user_row = generate_user_data_wenwen_common($panelist_row);

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
    $user_row[34] = 1; //todo:定义常量


    //tel: panel_91wenwen_panelist_mobile_number.mobile_number
    $panelist_mobile_number_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_mobile_number.csv";
    $panelist_mobile_data = FileUtil::readCsvContent($panelist_mobile_number_file);
    foreach ($panelist_mobile_data as $panelist_mobile_row) {
        if ($panelist_row[0] == $panelist_mobile_row[0]) {
            $user_row[10] = $panelist_mobile_row[1];
            break;
        }
    }

    //province , city : panelist.panel_region_id
    $migration_region_mapping_file = IMPORT_JL_PATH . "/migration_region_mapping.csv";
    $region_mapping_data = FileUtil::readCsvContent($migration_region_mapping_file);
    foreach ($region_mapping_data as $region_mapping_row) {
        if ($panelist_row[1] == $region_mapping_row[0]) {
            //province
            $user_row[12] = $region_mapping_row[1];
            //city
            $user_row[13] = $region_mapping_row[2];
            break;
        }
    }

    $panelist_detail_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_detail.csv";
    $panelist_detail_data = FileUtil::readCsvContent($panelist_detail_file);
    foreach ($panelist_detail_data as $panelist_detail_row) {
        if ($panelist_row[0] == $panelist_detail_row[0]) {

            //education: detail.graduation_code
            $user_row[14] = 'todo';

            //profession: detail.detail.job_code
            $user_row[15] = 'todo';

            //income : detail.income_personal_code
            $user_row[16] = 'todo';

            //industry_code: detail.industry_code
            $user_row[37] = 'todo';

            //work_section_code: detail.work_section_code
            $user_row[38] = 'todo';

            break;
        }
    }

    $panelist_profile_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile.csv";
    $panelist_profile_data = FileUtil::readCsvContent($panelist_profile_file);
    foreach ($panelist_profile_data as $panelist_profile_row) {

        if ($panelist_row[0] == $panelist_profile_row[1]) {
            //hobby: profile.hobby
            $user_row[17] = 'todo';

            //personalDes: profile.biography
            $user_row[18] = 'todo';

            //fav_music: profile.fav_music
            $user_row[35] = 'todo';

            //monthly_wish:profile.monthly_wish
            $user_row[36] = 'todo';

            break;
        }
    }

    //last_login_ip todo
    //$user_row[23] = 'todo';


    //points: panel_91wenwen_panelist_point.point_value
    $panelist_point_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_point.csv";
    $panelist_point_data = FileUtil::readCsvContent($panelist_point_file);
    foreach ($panelist_point_data as $panelist_point_row) {
        if ($panelist_row[0] == $panelist_point_row[0]) {
            $user_row[24] = $user_row[24] + $panelist_point_row[1];
            break;
        }
    }

    //icon_path:panelist_profile_image
    $panelist_profile_image_file = IMPORT_WW_PATH . "/panel_91wenwen_panelist_profile_image.csv";
    $panelist_profile_image_data = FileUtil::readCsvContent($panelist_profile_image_file);
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

    return $user_wenwen_login_row;
}
