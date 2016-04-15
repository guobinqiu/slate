<?php
namespace Jili\ApiBundle\Tests\Utility;
use Jili\ApiBundle\Utility\FileUtil;

class FileUtilTest extends \PHPUnit_Framework_TestCase {

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
            )
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
    * @group issue_437
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

    /**
    * @group issue_560
    */
    public function testIsUTF8() {
        $file_name = dirname(__FILE__) . "/test.log";

        $content = "test测试";
        $content = iconv('utf-8', 'utf-8', $content); /*转换为utf-8编码*/
        file_put_contents($file_name, $content);
        $is_utf8 = FileUtil :: isUTF8($file_name);
        $this->assertTrue($is_utf8);

        $content = iconv('utf-8', 'gb2312', $content); /*转换为ISO-8859-1编码*/
        file_put_contents($file_name, $content);
        $is_utf8 = FileUtil :: isUTF8($file_name);
        $this->assertFalse($is_utf8);

        unlink($file_name);
    }

    /**
    * @group issue_578
    */
    public function testWriteContents() {
        $file_name = dirname(__FILE__) .'/test/create_dir/'. "/test.log";
        $content = "testWriteContents";
        FileUtil :: writeContents($file_name, $content);

        $this->assertFileExists(dirname($file_name));
        $this->assertContains("testWriteContents\r\n", file_get_contents($file_name));
        $aa = unlink($file_name);
    }
}
