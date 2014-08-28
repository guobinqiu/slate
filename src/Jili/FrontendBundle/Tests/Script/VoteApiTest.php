<?php
namespace  Jili\FrontendBundle\Tests\Script;

/**
 * 
 **/
class VoteApiTest extends \PHPUnit_Framework_TestCase 
{
    
    /**
     * @group issue_437 
     */
    public function testRun()
    {
        // run the vote_api.php
        $output_filename = "/data/91jili/logs/wenwen/vote.csv";
        $url = "http://www.91wenwen.net/index.php/vote/activeList";

        // remove previouse file
        exec('rm -rf '.$output_filename);
        $this->assertFileNotExists($output_filename);
        $script = dirname(__FILE__).'/../../../../../scripts/wenwen/vote_api.php';
        exec('php -f '.$script);

        // check  the file 
        $this->assertFileExists($output_filename);

        // build the expected content with fputcsv()
        $content = file_get_contents($url);
        $rows = json_decode($content, true);
        $last_item  = array_pop($rows['data']);
        $fh = fopen('php://memory', 'r+');
        fputcsv($fh , $last_item);
        rewind($fh);
        $expected = fread($fh, 8096);
        fclose($fh);

        // check  the  content 
        $actual = file_get_contents($output_filename);
        $this->assertEquals($expected, $actual, 'compare the output file content');
        // mkdir -p /data/91jili/logs/wenwen/
        $this->assertEquals('1', 1);
    }
}

?>
