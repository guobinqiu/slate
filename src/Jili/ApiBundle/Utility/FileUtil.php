<?php
namespace Jili\ApiBundle\Utility;

/**
 *
 */
class FileUtil {
    /**
     * @params: $filename
     * @return: $contents
     */
    public static function readCsvContent($filename) {
        $contents = null;
        if (!file_exists($filename)) {
            //die("指定文件不存在，操作中断!");
            return $contents;
        }

        //读文件内容
        $file_handle = fopen($filename, "r");
        if (!$file_handle) {
            //die("指定文件不能打开，操作中断!");
            return $contents;
        }

        if ($file_handle !== FALSE) {
            while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
                $contents[] = $data;
            }
        }

        return $contents;
    }
}