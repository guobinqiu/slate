<?php
// require 'AWSSDKforPHP/aws.phar';
// use Aws\S3\S3Client;
// use Aws\Common\Aws;

// $client = S3Client::factory(array(
//     'key'    => 'AKIAJUFYZ3ESCUADAMWQ',
//     'secret' => '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'
// ));
// $bucket = 'eight-gees-reward-productio';

// $result = $client->createBucket(array(
//     'Bucket' => $bucket
// ));



require '../vendor/aws/aws-autoloader.php';

use Aws\S3\S3Client;

$time = $_POST['start_time'];

if($time){
	 // Instantiate the S3 client with your AWS credentials and desired AWS region
	$bucket = 'gees-reward-production';
	$client = S3Client::factory(array(
	    'key'    => 'AKIAJUFYZ3ESCUADAMWQ',
	    'secret' => '4b1BiAiZLx7+XC2l9vADQcLVwlOW2+8lfYd/cPqe',
	));

	// $result = $client->createBucket(array(
	//     'Bucket' => $bucket
	// ));
	// $filename = 'jili/2013-11-10.csv';
	$filename = 'jili/'.$time.'.csv';
	$result = $client->getObject(array(
	'Bucket' => $bucket,
	'Key' => $filename
	));
	$fp = fopen($time.".csv", "w");
	fwrite($fp, $result['Body']);
	fclose($fp);
	$url=$time.".csv";
	downfile($url);
	unlink($time.".csv");
}

function downfile($fileurl)
{
	ob_start();
	$filename=$fileurl;
	header( "Content-type:   application/octet-stream ");
	header( "Accept-Ranges:   bytes ");
	header( "Content-Disposition:   attachment;   filename= $filename");
	$size=readfile($filename);
	header( "Accept-Length: " .$size);
}
	


?>