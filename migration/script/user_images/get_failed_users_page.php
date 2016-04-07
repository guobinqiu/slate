<?php

//

    if($argc != 2 ||  ! file_exists($argv[1])) {

        $STDERR = fopen('php://stderr', 'w'); 
        fwrite($STDERR,'not found '.$argv[1]); 
        fclose($STDERR); 

        exit(1);
    }
$f=$argv[1];
$fh = fopen($f,'r');
while( $l=  fgetcsv($fh,1024,"\t") ) {

    $url ='http://www.91wenwen.net/user/'.$l[1];;

    $ch = curl_init();

    $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            );

    curl_setopt_array( $ch, $options );
    $body = curl_exec($ch); 
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); 

    if($httpCode == '404') {
        continue;
    } 

    echo $l[0],'  ',$url, ' ' , $httpCode,PHP_EOL; 

    if(! preg_match('/<img src="(http:\/\/d1909s8qem9bat\.cloudfront\.net\/user_profile\/.*\.jpg)"/i' ,$body,$m)) {
        echo 'ERROR: no body ', $url,PHP_EOL;  
        continue;
    }


// http://d1909s8qem9bat.cloudfront.net/user_profile/a/c/7/ac743f738a882f4ab14ee1fe2c42d8e31a57622f_m.jpg

    $url = substr($m[1], 0,-5).'s.jpg';
    $icon_path = substr(trim($m[1]), 50,46).'_s.jpg';

    if(strlen($icon_path)  == 52) {
        echo 'IMAGES_URL_PATH:',$icon_path,PHP_EOL;
        print_insert_sql( $icon_path , $l[2]);
    } else {
        echo 'IMAGES_URL_PATH_BAD:',$icon_path,PHP_EOL;

    }

};

function print_insert_sql ($icon_path, $email) {
    echo 'SQL:' ,'update user set icon_path= "'.$icon_path.'" where email =  "' .$email.'"  limit 1;',PHP_EOL;
}
