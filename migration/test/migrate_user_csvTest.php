<?php

require __DIR__.'/../script/migrate_user_csv.php';

class migrate_user_csvTest extends PHPUnit_Framework_TestCase
{
  public function test_getJiliConnectionByPanelistId() {
    $fh = FileUtil::checkFile(IMPORT_WW_PATH . '/panel_91wenwen_panelist_91jili_connection.csv');


    $return = getJiliConnectionByPanelistId($fh , '305');
    $this->assertEquals(16980, $return);

    $return = getJiliConnectionByPanelistId($fh , '');
    $this->assertNull($return);
    fclose($fh);
    $fh = tmpfile();
    fwrite($fh,'"panelist_id","jili_id","status_flag","stash_data","updated_at","created_at"
"1835509","23662","0","NULL","2015-02-09 21:13:29","2015-02-09 21:13:29"
"355","4646","1","NULL","2014-11-25 13:16:39","2014-11-25 13:16:39"'

);

    $return = getJiliConnectionByPanelistId($fh , '1835509');
    $this->assertNull($return);

    $return = getJiliConnectionByPanelistId($fh , '355');
    $this->assertEquals(4646, $return);
    
    fclose($fh);
  }

  function test_getUserWenwenCrossById( ) {

    $fh = tmpfile();
    fwrite($fh, <<<EOD
'"id","user_id","created_at","email"
"32283","1324216","2015-04-03 14:42:11","z_2004wangxu@163.com"
"9523","1132079","2014-12-04 22:27:18","_stlinshaohe@163.com"
"5629","1270570","2014-11-26 16:32:00","NULL"

EOD
);
    $return = getUserWenwenCrossById($fh, '');
    $this->assertNull($return);

    $return = getUserWenwenCrossById($fh, '5629');
    $this->assertNull( $return);

    $return = getUserWenwenCrossById($fh, '9523');
    $this->assertEquals('_stlinshaohe@163.com', $return);
    fclose($fh);
  }
}

