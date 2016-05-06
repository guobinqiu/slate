<?php

$f ='/tmp/lack_images.txt';
if( ! file_exists($f) ) {
    echo 'not found file ' , $f,PHP_EOL;
    exit(1);
}
$images = file($f);



foreach( $images as  $i) {
    echo 'select i.s_file, p.id, p.email from panel_91wenwen_panelist_profile_image i 
left  join panelist p  on p.id = i.panelist_id
where i.s_file = "'.trim($i).'";';

    echo PHP_EOL;
}
