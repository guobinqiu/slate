<?php

class Constants
{
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

    public static $origin_flag = array (
        'new' => 0,
        'jili' => 1,
        'wenwen' => 2,
        'wenwen_jili' => 3
    );

    # password_choice ,== PWD_WENWEN, verify the user_wenwen_login
    # == PWD_JILI or NULL , verify by user.password
    public static $password_choice = array (
        'PWD_WENWEN' => 1,
        'PWD_JILI' => 2
    );
}

?>