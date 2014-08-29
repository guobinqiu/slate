<?php
namespace Jili\ApiBundle\Tests\Utility;
use Jili\ApiBundle\Utility\FileUtil;

class FileUtilTest extends\PHPUnit_Framework_TestCase {

    /**
    * @group ReadFileContent
    */
    public function testReadFileContent() {
        $file_name = dirname(__FILE__) . '\filetext.txt';

        $list = array (
            array (
                'aaa',
                'bbb',
                'ccc',
                'dddd'
            ),
            array (
                '123',
                '456',
                '789'
            ),

        );
        $fp = fopen($file_name, 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        $content = FileUtil :: readCsvContent($file_name);
        $this->assertEquals('789', $content[1][2]);
        $this->assertEquals('2', count($content));

        unlink($file_name);
    }

    /**
    * @group ReadJosnFile
    */
    public function testReadJosnFile() {
        $file_name = dirname(__FILE__) . '\filetext.txt';

        $list = array (
            array (
                'aaa',
                'bbb',
                'ccc',
                'dddd'
            ),
            array (
                '123',
                '456',
                '789'
            ),

        );
        $fp = fopen($file_name, 'w');
        fwrite($fp, json_encode($list));
        fclose($fp);

        $content = FileUtil :: readJosnFile($file_name);

        $this->assertEquals('789', $content[1][2]);
        $this->assertEquals('2', count($content));

        unlink($file_name);
    }
}