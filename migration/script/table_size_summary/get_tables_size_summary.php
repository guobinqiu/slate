<?php
if($argc < 2) {
    echo 'php -f a.php jili';
    echo PHP_EOL;
        echo 'php -f a.php wenwen';
    echo PHP_EOL;
        exit;
}

if( $argv[1] == 'jili' )  {
  $a=array(
    'user',
    'weibo_user',
    'user_wenwen_cross',
    'exchange_from_wenwen',
    'migration_region_mapping',
    'weibo_user',
  );
  $db = 'jili_db';
}else if( $argv[1] == 'wenwen' )  {
  $a = array(
    'panelist',
    'panel_91wenwen_panelist_profile',
    'panel_91wenwen_panelist_profile_image',
    'panel_91wenwen_panelist_detail',
    'panel_91wenwen_panelist_point',
    'panel_91wenwen_panelist_mobile_number',
    'panel_91wenwen_pointexchange_91jili_account',
    'panel_91wenwen_panelist_91jili_connection',
    'sop_respondent',
    'panel_91wenwen_panelist_sina_connection',
    'panel_91wenwen_vote_answer_201604',
    'panel_91wenwen_vote',
    'panel_91wenwen_vote_image',
    'panel_91wenwen_vote_choice',
    'panel_faq',// (根据情况)
    'panel_faq_category',//(根据情况)

  );
  $db = 'wenwen';
} else {
  exit;
}

$s1 =<<<EOD
SELECT table_name AS "Tables", 
round(((data_length + index_length) / 1024 / 1024), 2) "Size in MB" 
FROM information_schema.TABLES 
WHERE table_schema = '%1\$s' and table_name = '%2\$s';
EOD;

$s2 =<<<EOD
SELECT count(*) as count  FROM %1\$s.%2\$s; 
EOD;

foreach ($a as $v){
  printf( $s1,$db, $v);
  print "\n";

  printf( $s2,$db, $v);
  print "\n";
};
print "\n";

