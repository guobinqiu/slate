<?php
include_once ('config.php');
include_once ('FileUtil.php');

// import file
$vote_file = IMPORT_WW_PATH . "/panel_91wenwen_vote.csv";
$vote_image_file = IMPORT_WW_PATH . "/panel_91wenwen_vote_image.csv";
$vote_choice_file = IMPORT_WW_PATH . "/panel_91wenwen_vote_choice.csv";

// get file content
$vote = FileUtil::readCsvContent($vote_file);
$vote_image = FileUtil::readCsvContent($vote_image_file);
$vote_choice = FileUtil::readCsvContent($vote_choice_file);

$votes = array ();

// get vote data
#id, title, description, yyyymm, start_time, end_time, point_value, delete_flag, updated_at, created_at
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

// get vote choice data
#id, vote_id, answer_number, name, updated_at, created_at
foreach ($vote_choice as $value) {
    $votes[$value[1]]['choice'][$value[2]] = $value[3];
}

// get vote image data
//todo: 问问图片长度是40位，积粒是32位，是否可以
#"id","vote_id","filename","description","width","height","sq_path","sq_width","sq_height","s_path","s_width","s_height","m_path","m_width","m_height","delete_flag","updated_at","created_at"
foreach ($vote_image as $value) {
    $votes[$value[1]]['vote_image'] = $value[2];
}

// generate a csv file
$migrate_vote_handle = fopen(EXPORT_PATH . "/migrate_vote.csv", "w");
#id,title,description,start_time,end_time,point_value,stash_data,vote_image,updated_at,created_at
// $title = array (
//     'id',
//     'title',
//     'description',
//     'start_time',
//     'end_time',
//     'point_value',
//     'stash_data',
//     'vote_image',
//     'updated_at',
//     'created_at'
// );
// fputcsv($migrate_vote_handle, $title);


foreach ($votes as $vote) {
    $data = array (
        $vote['id'],
        $vote['title'],
        $vote['description'],
        $vote['start_time'],
        $vote['end_time'],
        $vote['point_value'],
        generate_stash_data($vote['choice']),
        isset($vote['vote_image']) ? $vote['vote_image'] : 'NULL',
        $vote['updated_at'],
        $vote['created_at']
    );
    fputcsv($migrate_vote_handle, $data);
}

fclose($migrate_vote_handle);

/**
 * generate stash data
 */
function generate_stash_data($choice)
{
    $stash_data['choices'] = $choice;
    return json_encode($stash_data);
}

echo "\r\n\r\n" . date('c') . "   end!\r\n\r\n";
exit();
?>
