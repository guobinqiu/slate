<?php

class Constants
{
    public static $environment = 'dev';
    //income ww.income_code => merged.income_code
    public static $income = array (
        1 => 100,
        2 => 101,
        3 => 102,
        4 => 103,
        5 => 104,
        6 => 105,
        7 => 106,
        8 => 107,
        9 => 108,
        10 => 109,
        11 => 110,
        12 => 111,
        13 => 112,
        14 => 113,
        15 => 114,
        16 => 115,
        17 => 116,
        18 => 117,
        19 => 118,
        20 => 119
    );

    //ad_category : web_merge
    public static $ad_category_type_web_merge = 94;

    //origin_flag
    public static $origin_flag = array (
        'new' => 0,
        'jili' => 1,
        'wenwen' => 2,
        'wenwen_jili' => 3
    );

    //is_from_wenwen
    public static $is_from_wenwen = array (
        'jili_register' => 0,
        'wenwen_come' => 1,
        'wenwen_register' => 2,
        'wenwen_only' => 3
    );

    # password_choice ,== PWD_WENWEN, verify the user_wenwen_login
    # == PWD_JILI or NULL , verify by user.password
    public static $password_choice = array (
        'pwd_wenwen' => 1,
        'pwd_jili' => 2
    );

    //export csv title: user
    public static $jili_user_title = array(
        0  => 'id',
        1  => 'email',
        2  => 'pwd',
        3  => 'is_email_confirmed',
        4  => 'is_from_wenwen',
        5  => 'wenwen_user',
        6  => 'token',
        7  => 'nick',
        8  => 'sex',
        9  => 'birthday',
        10 => 'tel',
        11 => 'is_tel_confirmed',
        12 => 'province',
        13 => 'city',
        14 => 'education',
        15 => 'profession',
        16 => 'income',
        17 => 'hobby',
        18 => 'personalDes',
        19 => 'identity_num',
        20 => 'reward_multiple',
        21 => 'register_date',
        22 => 'last_login_date',
        23 => 'last_login_ip',
        24 => 'points',
        25 => 'delete_flag',
        26 => 'is_info_set',
        27 => 'icon_path',
        28 => 'uniqkey',
        29 => 'token_created_at',
        30 => 'origin_flag',
        31 => 'created_remote_addr',
        32 => 'created_user_agent',
        33 => 'campaign_code',
        34 => 'password_choice',
        35 => 'fav_music',
        36 => 'monthly_wish',
        37 => 'industry_code',
        38 => 'work_section_code'
);

    //export csv title: user_wenwen_login
    public static $user_wenwen_login_title = array (
        0=>'id',
        1=>'user_id',
        2=>'login_password_salt',
        3=>'login_password_crypt_type',
        4=>'login_password'
    );

    //export csv title: weibo_user
    public static $weibo_user_title = array (
        'id',
        'user_id',
        'open_id',
        'regist_date'
    );

    //export csv title: sop_respondent
    public static $sop_respondent_title = array (
        'id',
        'panelist_id',
        'status_flag',
        'stash_data',
        'updated_at',
        'created_at'
    );

    //export csv title: vote_answer
    public static $vote_answer_title = array (
        'id',
        'panelist_id',
        'vote_id',
        'answer_number',
        'updated_at',
        'created_at'
    );

    //export csv name
    public static $migrate_user_name = 'migrate_user.csv';
    public static $migrate_user_wenwen_login_name = 'migrate_user_wenwen_login.csv';
    public static $migrate_weibo_user_name = 'migrate_weibo_user.csv';
    public static $migrate_sop_respondent_name = 'migrate_sop_respondent.csv';
    public static $migrate_vote_answer_name = 'migrate_vote_answer.csv';
    public static $migrate_task_history_name = 'task_history0';
    public static $migrate_point_history_name = 'point_history0';

    //export csv for check 
    public static $chk_user_name= 'user.csv';
    public static $chk_user_wenwen_login_name= 'user_wenwen_login.csv';
}

?>
