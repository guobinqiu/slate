<?php

class Constants
{
    //income
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
    public static $jili_user_title = array (
        "id",
        "email",
        "pwd",
        "is_email_confirmed",
        "is_from_wenwen",
        "wenwen_user",
        "token",
        "nick",
        "sex",
        "birthday",
        "tel",
        "is_tel_confirmed",
        "province",
        "city",
        "education",
        "profession",
        "income",
        "hobby",
        "personalDes",
        "identity_num",
        "reward_multiple",
        "register_date",
        "last_login_date",
        "last_login_ip",
        "points",
        "delete_flag",
        "is_info_set",
        "icon_path",
        "uniqkey",
        "token_created_at",
        "origin_flag",
        "created_remote_addr",
        "created_user_agent",
        "campaign_code",
        "password_choice",
        "fav_music",
        "monthly_wish",
        "industry_code",
        "work_section_code"
    );

    //export csv title: user_wenwen_login
    public static $user_wenwen_login_title = array (
        "id",
        "user_id",
        "login_password_salt",
        "login_password_crypt_type",
        "login_password"
    );

    //export csv title: weibo_user
    public static $weibo_user_title = array (
        "id",
        "user_id",
        "open_id",
        "regist_date"
    );

    //export csv title: sop_respondent
    public static $sop_respondent_title = array (
        "id",
        "panelist_id",
        "status_flag",
        "stash_data",
        "updated_at",
        "created_at"
    );

    //export csv title: vote_answer
    public static $vote_answer_title = array (
        "id",
        "panelist_id",
        "vote_id",
        "answer_number",
        "updated_at",
        "created_at"
    );

    //export csv name
    public static $migrate_user_name = "/migrate_user.csv";
    public static $migrate_user_wenwen_login_name = "/migrate_user_wenwen_login.csv";
    public static $migrate_weibo_user_name = "/migrate_weibo_user.csv";
    public static $migrate_sop_respondent_name = "/migrate_sop_respondent.csv";
    public static $migrate_vote_answer_name = "/migrate_vote_answer.csv";
}

?>