<?php
namespace VendorIntegration\SSI\tests\PC1\Model\Query;

use \VendorIntegration\SSI\PC1\Model\Query\SsiRespondentQuery;

class SsiRespondentQueryTest extends \PHPUnit_Framework_TestCase
{
    private static $dbh = null;

    public function testRetrieveValidRespondentRow()
    {
       $this->assertNull(SsiRespondentQuery::retrieveValidRespondentRow(self::$dbh, 1));
       $this->assertNull(SsiRespondentQuery::retrieveValidRespondentRow(self::$dbh, 2));
       $this->assertNotNull(SsiRespondentQuery::retrieveValidRespondentRow(self::$dbh, 3));
    }

    public static function setUpBeforeClass()
    {
        self::$dbh = \TestConnectionUtils::getConnection();

        \TestDataUtils::buildDatabase(self::$dbh, \Symfony\Component\Yaml\Yaml::parse(<<<YAML
ssi_respondent:
  - id: 1
    user_id: 1
    status_flag: 0
  - id: 2
    user_id: 2
    status_flag: 1
  - id: 3
    user_id: 3
    status_flag: 10
YAML
        ));
    }
}
