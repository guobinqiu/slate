<?php

class FileUtil
{

    public static function readCsvContent($filename)
    {
        $contents = null;
        if (!file_exists($filename)) {
            die("The file : [" . $filename . "] does not exist, interruption!");
        }

        //读文件内容
        $file_handle = fopen($filename, "r");
        if (!$file_handle) {
            die("The file : [" . $filename . "] can't be opened, interruption!");
        }

        if ($file_handle !== FALSE) {
            while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
                $contents[] = $data;
            }
        }
        fclose($file_handle);

        unset($contents[0]);

        return $contents;
    }

    public static function closeFile($file_handle)
    {
        if (is_resource($file_handle)) {
            fclose($file_handle);
        }
 
        /**
         * every row in the csv must have the same number of columns.
         * sample 
         * check_csv( '/data/91jilivote', $vote);
         * check_csv( 'vote_choice', $vote_choice);
         */
        static function check_csv( $file_path, $content ) {
          $x = count($content[1]);
          foreach( $content as $l => $r) {
            $c =count($r);
            if ( count($r) != $x ) {
              throw new Exception("read file error: $file_path,\n\trequired cols number: $x;\n\tline number: $l\n\tcontent:". json_encode($r, true )  );
            }
          }
          return true;
        }
   }
}

?>
