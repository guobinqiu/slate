<?php
header("Content-type:text/html;charset=utf-8");

$base_path = '/data/91jili/merge/ww_csv';
$export_path = '/data/91jili/merge/script/vote';

//$base_path = __DIR__;
//$export_path = __DIR__;

$vote_file = $base_path . "/panel_91wenwen_vote.csv";
$vote_image_file = $base_path . "/panel_91wenwen_vote_image.csv";
$vote_choice_file = $base_path . "/panel_91wenwen_vote_choice.csv";

$migrate_vote_csv = $export_path . "/migrate_vote_" . date('Ymd') . ".csv";

$vote_handle = fopen($vote_file, "r");
$vote_image_handle = fopen($vote_image_file, "r");
$vote_choice_handle = fopen($vote_choice_file, "r");

$migrate_vote_handle = fopen($migrate_vote_csv, "w");

$vote = read_csv($vote_handle);

$vote_image = read_csv($vote_image_handle);

$vote_choice = read_csv($vote_choice_handle);

$votes = array ();
#id, title, description, yyyymm, start_time, end_time, point_value, delete_flag, updated_at, created_at
#id,title,description,start_time,end_time,point_value,stash_data,vote_image,updated_at,created_at
foreach ($vote as $value) {
    $votes[$value[0]]['id'] = $value[0];
    $votes[$value[0]]['title'] = $value[1];
    $votes[$value[0]]['description'] = $value[2];
    $votes[$value[0]]['start_time'] = $value[4];
    $votes[$value[0]]['end_time'] = $value[5];
    $votes[$value[0]]['point_value'] = $value[6];
    $votes[$value[0]]['updated_at'] = $value[8];
    $votes[$value[0]]['created_at'] = $value[9];
}

#id, vote_id, answer_number, name, updated_at, created_at
foreach ($vote_choice as $value) {
    $votes[$value[1]]['choice'][$value[2]] = $value[3];
}

//todo: 问问图片长度是40位，积粒是32位，是否可以
foreach ($vote_image as $value) {
    $votes[$value[1]]['vote_image'] = $value[2];
}

fputcsv($migrate_vote_handle, array (
    'id',
    'title',
    'description',
    'start_time',
    'end_time',
    'point_value',
    'stash_data',
    'vote_image',
    'updated_at',
    'created_at'
));

foreach ($votes as $vote) {
    fputcsv($migrate_vote_handle, array (
        $vote['id'],
        $vote['title'],
        $vote['description'],
        $vote['start_time'],
        $vote['end_time'],
        $vote['point_value'],
        generate_stash_data($vote['choice']),
        $vote['vote_image'],
        $vote['updated_at'],
        $vote['created_at']
    ));
}

function read_csv($file_handle)
{
    $contents = null;
    if ($file_handle !== FALSE) {
        // todo: 1000 是否合适
        while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
            $contents[] = $data;
        }
    }
    fclose($file_handle);
    unset($contents[0]);
    return $contents;
}

function generate_stash_data($choice)
{
    $stash_data['choices'] = $choice;
    return json_encode($stash_data);
}

fclose($vote_handle);
fclose($vote_image_handle);
fclose($vote_choice_handle);
fclose($migrate_vote_handle);
echo "\r\n\r\n" . date('c') . "   end!\r\n\r\n";
exit();
?>
