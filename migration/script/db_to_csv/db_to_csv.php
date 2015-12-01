<?php

$fh = fopen('php://stdout','w');


if($argc != 7 ) {
  file_put_contents('php://stderr','bad args');
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
} catch (PDOException $e) {
  file_put_contents('php://stderr','Connection failed: ' . $e->getMessage());
  fclose($fh);
  exit;
}

$q = $dbh->prepare("DESCRIBE $table");
$q->execute();
$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
fputcsv( $fh, $table_fields);

$q->closeCursor();

foreach ($dbh->query($sql) as $row) {
  fputcsv( $fh, $row );
}


fclose($fh);

