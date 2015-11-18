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
}

?>