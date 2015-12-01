<?php

$fh = fopen('php://stdout','w');


if($argc != 6 ) {
  file_put_content('php://stderr','bad args');
  exit;
  
}
$server = $argv[1];
$user = $argv[2];
$password = $argv[3];
$db = $argv[4];
//$table = $argv[5];
$sql  = $argv[5];


$dsn = "mysql:dbname=$db;host=$server";

try {
  $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
  file_put_content('php://stderr','Connection failed: ' . $e->getMessage());
  fclose($fh);
  exit;
}

foreach ($dbh->query($sql) as $row) {
  fputcsv( $fh, $row );
}


fclose($fh);
