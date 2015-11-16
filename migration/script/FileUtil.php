<?php

class FileUtil
{

    public static function readCsvContent($filename)
    {
        $contents = null;

        $file_handle = FileUtil::checkFile($filename);

        if ($file_handle !== FALSE) {
            while (($data = fgetcsv($file_handle, 2000, ",")) !== FALSE) {
                $contents[] = $data;
            }
        }
        fclose($file_handle);

        unset($contents[0]);

        FileUtil::checkCsv($filename, $contents);

        return $contents;
    }

    public static function checkFile($filename)
    {
        if (!file_exists($filename)) {
            die("The file : [" . $filename . "] does not exist, interruption!");
        }

        //读文件内容
        $file_handle = fopen($filename, "r");
        if (!$file_handle) {
            die("The file : [" . $filename . "] can't be opened, interruption!");
        }

        return $file_handle;
    }

    public static function joinCsv($row)
    {
        $csvline = '';

        $csv_array = array ();
        foreach ($row as $column) {
            //$csv_array[] = (preg_match('/[\",\n]/', $column)) ? '"' . preg_replace('/\"/', '""', $column) . '"' : $column;
            $csv_array[] = (preg_match('/[\"]/', $column)) ? '"' . preg_replace('/\"/', '""', $column) . '"' : '"' . $column . '"';
        }
        $csvline .= implode(',', $csv_array);

        return $csvline;
    }

    public static function closeFile($file_handle)
    {
        if (is_resource($file_handle)) {
            fclose($file_handle);
        }
    }

    /**
     * every row in the csv must have the same number of columns.
     * sample
     * check_csv( '/data/91jilivote', $vote);
     * check_csv( 'vote_choice', $vote_choice);
     */
    public static function checkCsv($file_path, $content)
    {
        $x = count($content[1]);
        foreach ($content as $l => $r) {
            $c = count($r);
            if (count($r) != $x) {
                throw new Exception("read file error: $file_path,\n\trequired cols number: $x;\n\tline number: $l\n\tcontent:" . json_encode($r, true));
            }
        }
        return true;
    }

    /**
     * csv_get_lines 读取CSV文件中的某几行数据
     * @param $csvfile csv文件路径
     * @param $lines 读取行数
     * @param $offset 起始行数
     * @return array
     * */
    public static function csv_get_lines($csvfile, $lines, $offset = 0)
    {
        $fp = FileUtil::checkFile($filename);
        $i = $j = 0;
        while (false !== ($line = fgets($fp))) {
            if ($i++ < $offset) {
                continue;
            }
            break;
        }
        $data = array ();
        while (($j++ < $lines) && !feof($fp)) {
            $data[] = fgetcsv($fp);
        }
        fclose($fp);
        return $data;
    }

    public static function writeContents($filename, $content) {
        $log_handle = fopen($filename, "a");
        fwrite($log_handle, date("Y-m-d H:i:s") . "  " . $content . "\r\n");
        fclose($log_handle);
    }
}

?>