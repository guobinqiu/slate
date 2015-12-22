<?php

include __DIR__.'/../migrate_function.php';


function output( $a_w , $b_j, $a_j, $b_w ) {

    printf( "%20s%-50s", 'email:',$a_w[3]);
    printf( "%10s%10s%-10s\n", ' ', 'email:',$b_j[1]);

    printf( "%20s%-50s",'last_login_date:' ,$a_w[17]);
    printf( "%20s%-10s\n",'points:',$b_j[24]);

    printf( "%90s%-20s\n", 'last_login_ip:',$b_j[23]);
    printf( "%90s%-20s\n",'last_login_date:', $b_j[22]);


    if( $a_j ) {
        printf( "%90s%-20s\n",  'email:',$a_j[1]);
        printf( "%90s%-20s\n", 'points:',$a_j[24]);
        printf( "%90s%-20s\n", 'last_login_ip:',$a_j[23]);
        printf( "%90s%-20s\n",'last_login_date:', $b_j[22]);
    }

    if( $b_w) {
        printf( "%20s%-50s\n", 'email:',$b_w[3]);
        printf( "%20s%-50s\n",'last_login_date:' ,$b_w[17]);
    }


}

function do_process() {

    $fh_jl  = fopen('/tmp/conn_email_diff_jl.user.csv','r');
    $fh_ww = fopen('/tmp/conn_email_diff_ww.panelist.csv','r');
    $fh_diff = fopen('/tmp/conn_email_diff','r');

    $i = 1;
    $ind_ww =  build_file_index( $fh_ww, 'email'); 
    $ind_jl =  build_file_index( $fh_jl, 'email'); 

    while( $diff_row  = fgetcsv($fh_diff, 2048)) {
        $i++;
        if(count($diff_row ) != 2 || empty($diff_row[0]) || empty($diff_row[1])) {
            echo 'i:', $i,PHP_EOL;
            var_dump($diff_row);
            return 1;
        }
        echo "\n",str_repeat('-', 100),sprintf("------------seq_number: %'#10d",$i),"\n";

        $a_w = use_file_index($ind_ww, $diff_row[0], $fh_ww, false);
        $b_j = use_file_index($ind_jl, $diff_row[1], $fh_jl, false);
        $a_j = use_file_index($ind_jl, $diff_row[0], $fh_jl, false);
        $b_w = use_file_index($ind_ww, $diff_row[1], $fh_ww, false);
//        output( $a_w , $b_j, $a_j,  $b_w ) ;
    }
}
// 
function do_static() {
    $fh_jl  = fopen('/tmp/conn_email_diff_jl.user.csv','r');
    $fh_ww = fopen('/tmp/conn_email_diff_ww.panelist.csv','r');
    $fh_diff = fopen('/tmp/conn_email_diff','r');

    $fh_ww_point = fopen('/data/91jili/merge/ww_csv/panel_91wenwen_panelist_point.csv','r');

    $i = 1;
    $ind_ww =  build_file_index( $fh_ww, 'email'); 
    $ind_jl =  build_file_index( $fh_jl, 'email'); 

    $ind_ww_point = build_key_value_index($fh_ww_point, 'panelist_id','point_value');

    $return= array(
            '11' => 0,
            '01' => 0,
            '10' => 0,
            '00' => 0,
            '100' => 0,
            );
    $points = array(
            '11' => array( 0,0) ,
            '01' => 0,
            '10' => 0,
            '00' => 0,
            '100' => 0,
            );


    while( $diff_row  = fgetcsv($fh_diff, 2048)) {
        $i++;
        if(count($diff_row ) != 2 || empty($diff_row[0]) || empty($diff_row[1])) {
            echo 'i:', $i,PHP_EOL;
            var_dump($diff_row);
            return 1;
        }

        $a_w = use_file_index($ind_ww, $diff_row[0], $fh_ww, false);

        $b_j = use_file_index($ind_jl, $diff_row[1], $fh_jl, false);

        $a_j = use_file_index($ind_jl, $diff_row[0], $fh_jl, false);

        $b_w = use_file_index($ind_ww, $diff_row[1], $fh_ww, false);



        $now = time();
        // 
        $gap = 180 * 24 * 60 * 60 ;

        # a_w is active user ? 
        $is_a_w = false;
        $is_a_j = false;
        $is_b_w = false;
        $is_b_j = false;

        $pts_a_w = 0;
        $pts_a_j = 0;

        $pts_b_w = 0;
        $pts_b_j = 0;

        // if login < 180 day,then is active user  
        if( $a_w) {

            if(! empty( $a_w[17]) &&  $now -  strtotime($a_w[17])  <= $gap) {
                $is_a_w = true;
            }
            $pts= use_key_value_index($ind_ww_point, $a_w[0] );
            $pts_a_w = $pts['point_value'];
        }



        if( $b_j) {
            if(!empty($b_j[23])  # last login ip is not empty 
                    && ! empty( $b_j[22])   # last login at 
                    &&  $now -  strtotime($b_j[22])  <= $gap   ) {
                $is_b_j = true;
            }

            $pts_b_j = $b_j[24];
        }

        if( $a_j) {
            $pts_a_j = $a_j[24];
        }

        if( $b_w) {
            $pts= use_key_value_index($ind_ww_point, $b_w[0] );
            $pts_b_w = $pts['point_value'];
        }

        if( $is_a_w  && $is_b_j ) {
            $return['11'] ++;
            $pts = $pts_a_w + $pts_b_j;
            if(  $pts > 2000 ) {
                $points['11'][0] +=  $pts_a_w ;
                $points['11'][1] += $pts_b_j;
            }
        }  elseif(! $is_a_w &&  $is_b_j  ) {
            $return['01'] ++;

            $points['01'] =   $pts_b_j > 2000 ? $pts_b_j: 0;

        }  elseif($is_a_w  && ! $is_b_j) {
            $return['10'] ++;

            $points['10'] += $pts_a_w  > 2000 ? $pts_a_w: 0;;
        }  elseif( ! $is_a_w  && ! $is_b_j) {
            $return['00'] ++;
        } else {
            $return['100'] ++;
        }


    }

    echo 'accounts_count:',PHP_EOL;
    print_r($return);
    echo PHP_EOL;
    echo "\t",'sum:', array_sum($return);
    echo PHP_EOL;
    echo 'points exchangeable:',PHP_EOL;
    print_r($points);
$points[11] = array_sum($points[11]);
    echo PHP_EOL;
    echo "\t",'sum:', array_sum($points);
    
}



do_static();
