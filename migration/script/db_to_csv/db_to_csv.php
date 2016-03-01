<?php

$fh = fopen('php://stdout','w');


if($argc != 7 ) {
  file_put_contents('php://stderr','bad args:'. var_export($argv, true));
  exit;
}

$server = $argv[1];
$user = $argv[2];
$password = $argv[3];
$db = $argv[4];
$table = $argv[5];
$sql  = $argv[6];


$dsn = "mysql:dbname=$db;host=$server";
$options = array(
  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
try {
  $dbh = new PDO($dsn, $user, $password,$options);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  file_put_contents('php://stderr','Connection failed: ' . $e->getMessage());
  fclose($fh);
  exit;
}
# get csv head line
$q = $dbh->prepare($sql . ' limit 1');
$q->execute();
$column_count = $q->columnCount();
$table_fields =array(); 
for($i=0;$i<$column_count; $i++ ) {
    $meta  =  $q->getColumnMeta($i);
    $table_fields [] = $meta['name'];
}
fputcsv( $fh, $table_fields);

$q->closeCursor();

 # pagenation
 $q = $dbh->prepare("select count(*) from $table" );
 $q->execute();
 $row = $q->fetch(PDO::FETCH_NUM);
 $total = $row[0];
 $page_size= 100000;
 
 $pages =  ceil($total/$page_size );
 $start = 0;
 for( $page_no = 0; $page_no < $pages ; $page_no++ ) {

  $query  =  $sql . " limit $start, $page_size";
#  echo $query,PHP_EOL;
  $result = $dbh->query($query);
  $result->setFetchMode(PDO::FETCH_NUM);
  while($row = $result->fetch()) { 

    fputcsv( $fh, $row );
  }

  $result->closeCursor();

  $start =  $start + $page_size ; 
 }
 



#$result = $dbh->query($sql);
#$result->setFetchMode(PDO::FETCH_NUM);
#while($row = $result->fetch()) { 
#  fputcsv( $fh, $row );
#}
#if(empty($sql)) {
#  file_put_contents('php://stderr','bad args:'. var_export($argv, true));
#  exit;
#}
#$line = 0;
#$stmt=$dbh->prepare($sql);
#$stmt->execute();
#do {
#  $row =  $stmt->fetch(); 
#  fputcsv( $fh, $row );
#  $stmt->closeCursor(); 
#  ++$line; 
#} while($stmt->nextRowset());


fclose($fh);

# echo 'line:', $line, PHP_EOL;

