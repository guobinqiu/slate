<?php


include __DIR__.'/../config.php';
include __DIR__.'/../Constants.php';
include __DIR__.'/../FileUtil.php';
include __DIR__.'/../migrate_function.php';

$log_handle = fopen(LOG_PATH.'/'.basename(__FILE__, '.php').'_'. date('Ymd_Hi').'.log', 'a');

// check the password of merged users, to make sure that the user can log in with the rigth password

function do_process() 
{
    // merged user index 

    global $log_handle;

    // ww_panelist_file hanlder 
    // jl_user_file hanlder 

    global $panelist_file_handle;
    global $user_file_handle;


    $panelist_file_handle = FileUtil::checkFile(IMPORT_WW_PATH . '/panelist.csv');
    $user_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . '/user.csv');

    $user_indexs = build_file_index($user_file_handle, 'email');
    $panelist_indexs = build_file_index($panelist_file_handle, 'email');
    $user_file_handle = FileUtil::checkFile(IMPORT_JL_PATH . '/user.csv');



    // read  the merged user , and build index on email
// id, email, origin_flag, password_choice 
    $merged_user_file_hanlder = fopen(CHECK_PATH. '/' . Constants::$chk_user_name, 'r');
    $merged_user_indexes = build_file_index($merged_user_file_hanlder, 'email');

    $merged_password_file_hanlder = fopen(CHECK_PATH . '/' . Constants::$chk_user_wenwen_login_name, 'r');
    $merged_password_indexes = build_file_index($merged_password_file_hanlder, 'user_id');

    $i=0; //failed
    $j=0;//passed
// check each merged user
    foreach($merged_user_indexes as $email_merged => $merged_user_file_pointer) {

        $email_merged = strtolower($email_merged);

        $user_row_merged = use_file_index($merged_user_indexes, $email_merged, $merged_user_file_hanlder);


        if( $user_row_merged ) {

            if(isset($panelist_indexs[$email_merged])) {
                // I.email exits in wenwen.panelist.index , then check the passwrod info  and the  ORIGIN & password choie  
                $panelist = use_file_index($panelist_indexs, $email_merged, $panelist_file_handle,true);

                // must exists  wenwen 
                $pass= array(
                        'origin_flag'=>0,
                        'password_choice'=>0,
                        'password'=>0,
                        );
                // origin_flag must equals  1 or 3 
                if(isset($user_row_merged[2]) && ( Constants::$origin_flag['wenwen'] == $user_row_merged[2] || 
                  Constants::$origin_flag['wenwen_jili'] == $user_row_merged[2]) ) {
                    $pass['origin_flag'] =1;
                }

                // pwd_wenwen must equals  1 
                if( Constants::$password_choice['pwd_wenwen'] ==  $user_row_merged[3] ) {
                    $pass['password_choice'] =1;
                }


                // II. then email must exists in jili.user.index 

                // compare the PASSWORD_CHOICE & PASSWORD_DATA
                $password_row_merged = use_file_index($merged_password_indexes, $user_row_merged[0], $merged_password_file_hanlder, true);

                if(isset( $password_row_merged[2]) && isset( $password_row_merged[3]) && isset( $password_row_merged[4]) &&
                        $password_row_merged[2] == $panelist[7] && 
                        $password_row_merged[3] == $panelist[6] && 
                        $password_row_merged[4] == $panelist[5] 
                  ) {
                    $pass['password'] =1;
                }

                if(array_sum($pass) != 3 ) {

                        $log_msg = "FAILED: wenwen or both\n". 
                            "\t".'panelist:'.  json_encode($panelist, true).
                            "\t".'user_row_merged:'.  json_encode($user, true).
                            "\t".'password_row_merged:'.  json_encode($password_row_merged, true).
                            "\t".'pass:'.  json_encode($pass);
                        FileUtil::writeContents($log_handle, $log_msg);
                        $i ++;
                } else {
                    $j ++;
                }

            } else {

                $user = use_file_index($user_indexs, $email_merged, $user_file_handle,true);
                // must  FROM JILI ONLY;
                $pass= array(
                        'origin_flag'=>0,
                        'password_choice'=>0,
                        'password'=>0,
                        );

                // origin_flag must equals  1 or 3 
                if(isset($user_row_merged[2]) &&  ( Constants::$origin_flag['new'] == $user_row_merged[2] || 
                  Constants::$origin_flag['jili'] == $user_row_merged[2]) ) {
                    $pass['origin_flag'] =1;
                }

                // pwd_wenwen must equals  1 
                if( isset( $user_row_merged[3]) && Constants::$password_choice['pwd_jili'] ==  $user_row_merged[3] ) {
                    $pass['password_choice'] =1;
                }

                if(isset($user_row_merged[4]) &&  $user_row_merged[4] == $user[2] ) {
                    $pass['password'] =1;
  
                }

                if(array_sum($pass) != 3 ) {

                        $log_msg = "FAILED: jili only\n". 
                            "\t".'user:'.  json_encode($user ).
                            "\t".'user_row_merged:'.  json_encode($user_row_merged).
                            "\t".'pass:'.  json_encode($pass);
                        FileUtil::writeContents($log_handle, $log_msg);
                        $i ++;
                } else {
                    $j ++;
                }

            }  

        } 

    }

    $log_msg="\n".
        "\t failed: ". $i.
        "\t passed: ".$j.
            "\n completed!";
    // build panelist_user_index
    FileUtil::writeContents($log_handle, $log_msg);
}

do_process();
