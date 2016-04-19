<?php

namespace VendorIntegration\SSI\tests\PC1;

use \VendorIntegration\SSI\PC1\RequestHandler;
use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectQuery;
use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    private static $dbh = null;
    private $handler = null;

    public function setUp()
    {
        $this->handler = new RequestHandler(self::$dbh);
    }
    public function testSetUpNewProject()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(json_encode(
            [
              'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'http://www.d8aspring.com/?test=',
              'respondentList' => ['test'],
            ]
        ));
        $this->handler->setUpProject($request);

        $row = SsiProjectQuery::getRowById(self::$dbh, 2);
        $this->assertSame('2', $row['id']);
    }

    public function testSetUpExistingProject()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(json_encode(
            [
              'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 1,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'http://www.d8aspring.com/?test=',
              'respondentList' => ['test'],
            ]
        ));
        $this->handler->setUpProject($request);

        $row = SsiProjectQuery::getRowById(self::$dbh, 1);
        $this->assertSame('1', $row['id']);
    }

    public function testSetUpProjectRespondents()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(json_encode(
            [
              'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 1,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'http://www.d8aspring.com/?test=',
              'respondentList' => [
                [ 'respondentId' => 'wwcn-1', 'startUrlId' => 'sur1', ],
                [ 'respondentId' => 'wwcn-2', 'startUrlId' => 'sur2', ],
                [ 'respondentId' => 'wwcn-3', 'startUrlId' => 'sur3', ],
              ],
            ]
        ));
        $this->handler->setUpProject($request);
        $this->handler->setUpProjectRespondents($request);

        $this->assertEquals(['wwcn-1', 'wwcn-3'], $this->handler->getUnsubscribedRespondentIds());
        $this->assertEquals(['wwcn-2'], $this->handler->getSucceededRespondentIds());
        $this->assertEquals([], $this->handler->getFailedRespondentIds());

        $this->assertNull(SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 1, 1));
        $this->assertNotNull(SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 2, 1));
        $this->assertNull(SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 3, 1));
    }

    public static function setUpBeforeClass()
    {
        self::$dbh = \TestConnectionUtils::getConnection();

        \TestDataUtils::buildDatabase(self::$dbh, \Symfony\Component\Yaml\Yaml::parse(<<<YAML
ssi_respondent:
  - id: 1
    user_id: 1
    status_flag: 1
  - id: 2
    user_id: 2
    status_flag: 10
YAML
        ));
    }
}
