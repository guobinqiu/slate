<?php
$output_filename = "/data/91jili/logs/wenwen/vote.csv";
$url = "http://www.91wenwen.net/index.php/vote/activeList";

//请求api接口
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);
curl_close($ch);

//解析接口数据
$data = json_decode($return, true);

if ($data['meta'] && $data['meta']['code'] == 200) {

    $votes = $data['data'];

    //保存接口数据
    $handle = fopen($output_filename, 'w');
    if ($handle) {
        fputcsv($handle, $votes[count($votes) - 1]);
    }
    fclose($handle);

}

echo "ok";
?>
