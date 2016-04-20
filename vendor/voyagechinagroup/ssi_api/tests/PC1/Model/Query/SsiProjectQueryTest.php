<?php

namespace VendorIntegration\SSI\tests\PC1\Model\Query;

use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectQuery;

class SsiProjectQueryTest extends \PHPUnit_Framework_TestCase
{
    private static $dbh = null;

    public function testGetRowById()
    {
        $row = SsiProjectQuery::getRowById(self::$dbh, 0);
        $this->assertNull($row, 'Not found row');

        $row = SsiProjectQuery::getRowById(self::$dbh, 1);
        $this->assertEquals(1, $row['id']);
        $this->assertEquals(1, $row['status_flag']);
    }

    /**
     * @expectedException Exception
     */
    public function testInsertProjectWithInvalidParams()
    {
        $params = array(
          'id' => 2,
        );
        SsiProjectQuery::insertProject(self::$dbh, $params);
    }

    public function testInsertProjectWithValidParams()
    {
        $params = array(
          'id' => 2,
          'status_flag' => 1,
        );
        SsiProjectQuery::insertProject(self::$dbh, $params);

        $row = SsiProjectQuery::getRowById(self::$dbh, 2);
        $this->assertEquals(2, $row['id']);
        $this->assertEquals(1, $row['status_flag']);
    }

    public static function setUpBeforeClass()
    {
        self::$dbh = \TestConnectionUtils::getConnection();

        \TestDataUtils::buildDatabase(self::$dbh, \Symfony\Component\Yaml\Yaml::parse(<<<YAML
ssi_project:
  - id: 1
    status_flag: 1
YAML
        ));
    }
}
