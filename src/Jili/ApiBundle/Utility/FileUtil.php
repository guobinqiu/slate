<?php
namespace Jili\ApiBundle\Utility;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\FileSystem\FileSystem;
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
        fclose($file_handle);

        return $contents;
    }

    /**
     * @params: $filename
     * @return: $contents
     */
    public static function readJosnFile($filename) {
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
            $contents = json_decode(file_get_contents($filename), true);
        }
        fclose($file_handle);

        return $contents;
    }

    public static function isUTF8($filename) {
        $string = file_get_contents($filename);
        if ($string === mb_convert_encoding(mb_convert_encoding($string, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $uploaded the post file .
     * @param sring $directory the targe path.
     * @return string $filename
     */
    static public function moveUploadedFile(UploadedFile $uploaded , $directory  ) 
    {
        $name = md5(md5_file($uploaded->getPathname()). time() ). '.'.$uploaded->getClientOriginalExtension();

        //  if( ! isset($params['pic_target_path'])) {
        //      throw new ParameterNotFoundException('taobao_self_promotion_picture_dir');
        //  }
        
        $fs = new FileSystem();
        if( ! $fs->exists($directory) ) {
            $fs->mkdir( $directory);
        }

        // mv upload image
        $uploaded->move($directory ,$name);
        return $name;
    }

    public static function writeContents($filename, $content) {
        $log_handle = fopen($filename, "a");
        fwrite($log_handle, date("Y-m-d H:i:s") . "  " . $content . "\r\n");
        fclose($log_handle);
    }
}
