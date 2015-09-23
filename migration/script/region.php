<?php
$log = "log_" . date('Ymd') . ".sql";
$log_handle = fopen($log, "w");

$file_handle = fopen('panel_region.csv', "r");

$contents = null;
if ($file_handle !== FALSE) {
    while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
        $contents[] = $data;
    }
}
fclose($file_handle);
unset($contents[0]);

//根据命令行参数，决定是否正式执行 definitive为true时正式执行
$definitive = false;
if (isset($argv[1])) {
    parse_str($argv[1]);
}

echo "definitive:" . $definitive . "\r\n";

try {
    $dsn = "mysql:host=localhost;dbname=jili_db_new";
    $dbh = new PDO($dsn, 'root', 'ecnavi');
    $dbh->exec("SET NAMES 'utf8';");

    $dbh->beginTransaction();

    foreach ($contents as $value) {

        $region_id = trim($value[0]);
        $provinceName = trim($value[5]);
        $cityName = trim($value[2]);

        $municipality = array (
            '上海市',
            '北京市',
            '天津市',
            '重庆市'
        );
        if (in_array($provinceName, $municipality)) {
            $cityName = $provinceName;
            $provinceName = '直辖市';
        }

        if ($provinceName == '甘肃' && $cityName == '兰州') {
            $cityName = '兰州市';
        }

        if ($provinceName == '珠海' && $cityName == '广东省') {
            $cityName = '珠海市';
        }

        if ($provinceName == '襄樊市' && $cityName == '湖北省') {
            $cityName = '襄阳市';
        }

        if ($provinceName == '新疆维吾尔自治区' && $cityName == '省直辖行政单位') {
            $cityName = '其他省直辖行政单位';
        }

        $sql = "SELECT id FROM provinceList WHERE provinceName = '" . $provinceName . "'";
        fwrite($log_handle, $value[0] . ": " . $sql . "\r\n");
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $provinceId = $sth->fetchColumn();

        if (!$provinceId) {
            $p_sql = "insert into provinceList(provinceName) values('$provinceName')";
            fwrite($log_handle, $value[0] . ": " . $p_sql . "\r\n");
            $sth = $dbh->prepare($p_sql);
            $sth->execute();
            $provinceId = $dbh->lastInsertId();
        }

        $sql = "SELECT id FROM cityList WHERE provinceId = $provinceId and cityName = '" . $cityName . "'";
        fwrite($log_handle, $value[0] . ": " . $sql . "\r\n");
        $sth = $dbh->prepare($sql);
        $sth->execute();
        $cityId = $sth->fetchColumn();

        if (!$cityId) {
            $c_sql = "insert into cityList(cityName,provinceId) values('$cityName', $provinceId)";
            fwrite($log_handle, $value[0] . ": " . $c_sql . "\r\n");
            $sth = $dbh->prepare($c_sql);
            $sth->execute();
            $cityId = $dbh->lastInsertId();
        }

        $sql = "insert into migration_region_mapping values($region_id, $provinceId, $cityId)";
        fwrite($log_handle, $value[0] . ": " . $sql . "\r\n");
        $sth = $dbh->prepare($sql);
        $sth->execute();
    }

    if ($definitive) {
        $dbh->commit();
        echo "commit" . "\r\n";
    } else {
        echo "not commit" . "\r\n";
    }
} catch (PDOException $ex) {
    $dbh->rollBack();
    echo "rollBack" . "\r\n";
    echo $ex->getMessage();
}

fwrite($log_handle, "end" . "\r\n");
fclose($log_handle);
echo "Done!";
exit();
?>

__END__

=head1 dry-run

php region.php "definitive=0"

=head1 run

php region.php "definitive=1"