<?php
$output_filename = "D:/xampp/htdocs/PointMedia/app/logs/wenwen/vote.csv";
//$output_filename = "/data/91jili/logs/wenwen/vote.csv";
$url = "http://www.91wenwen.net/vote/active";

//请求api接口
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
$return = curl_exec($ch);
curl_close($ch);
var_dump($return);

$data['image_url'] = "http://d1909s8qem9bat.cloudfront.net/vote_image/b/0/b071ac0a2670ab935b1e72d048ede994664d6e38_s.jpg";
$data['title'] = "【生活】肝脏健康9个杀手你知道吗？";
$data['vote_url'] = "http://www.91wenwen.net/vote/?c=91jili#active";
$data['start_time'] = "2014-8-23 00:00:00";

$return = json_encode($data);

//$return = '{"image":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/b\/0\/b071ac0a2670
//ab935b1e72d048ede994664d6e38_s.jpg","title":"\u3010\u751f\u6d3b\u3011\u809d\u810
//f\u5065\u5eb79\u4e2a\u6740\u624b\u4f60\u77e5\u9053\u5417\uff1f"}';
//echo $return;

//解析接口数据
$data = json_decode($return, true);

echo "<pre>";
print_r($data);

//保存接口数据
$handle = fopen($output_filename, 'w');
if ($handle) {
    fputcsv($handle, $data);
}
fclose($handle);
echo "ok";
?>
