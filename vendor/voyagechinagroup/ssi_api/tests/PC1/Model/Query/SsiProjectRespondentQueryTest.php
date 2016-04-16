<?php
namespace VendorIntegration\SSI\tests\PC1\Model\Query;

use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;
use \VendorIntegration\SSI\PC1\Model\Row\SsiProjectRespondent;
use \VendorIntegration\SSI\PC1\Constants;

class SsiProjectRespondentQueryTest extends \PHPUnit_Framework_TestCase
{
    private static $dbh = null;

    public function testGetBySsiRespondentIdAndProjectId()
    {
        $res = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 1, 1000);
        $this->assertNull($res, 'Not found row');

        $res = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 1, 1001);
        $this->assertInstanceOf('VendorIntegration\SSI\PC1\ProjectSurvey', $res);
        $this->assertEquals(1, $res->getId());
    }

    /**
     * @expectedException Exception
     * @dataProvider invalidProjectRespondentParamsProvider
     */
    public function testInsertRespondentWithInvalidValues($values)
    {
        SsiProjectRespondentQuery::insertRespondent(self::$dbh, 2, $values);
    }

    public function testInsertRespondent1stTime()
    {
        $params = [
            'ssi_project_id' => 1001,
            'ssi_mail_batch_id' => 1,
            'start_url_id' => 'fuga',
            'stash_data' => [
              'foo' => 'bar',
            ]
        ];
        $this->assertEquals(1, SsiProjectRespondentQuery::insertRespondent(self::$dbh, 2, $params));
        $res = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 2, 1001);

        $this->assertEquals(5, $res->getId());
        $this->assertEquals(1001, $res->getSsiProjectId());
        $this->assertEquals(2, $res->getSsiRespondentId());
        $this->assertEquals('fuga', $res->getStartUrlId());
        $this->assertEquals(1, $res->getAnswerStatus());
        $this->assertEquals(array('foo' => 'bar'), $res->getStashData());
    }

    public function testInsertRespondent2ndTime()
    {
        $params = [
            'ssi_project_id' => 1001,
            'ssi_mail_batch_id' => 1,
            'start_url_id' => 'fuga',
            'stash_data' => [
              'foo' => 'bar',
            ]
        ];
        $this->assertEquals(2, SsiProjectRespondentQuery::insertRespondent(self::$dbh, 1, $params));
        $res = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 1, 1001);
        $this->assertEquals(date('Y-m-d'), $res->getUpdatedAt()->format('Y-m-d'));

        $this->assertEquals(1, $res->getId());
        $this->assertEquals(1001, $res->getSsiProjectId());
        $this->assertEquals(1, $res->getSsiRespondentId());
        $this->assertEquals('fuga', $res->getStartUrlId());
        $this->assertEquals(2, $res->getAnswerStatus());
        $this->assertEquals(array('foo' => 'bar'), $res->getStashData());
        $this->assertEquals(date('Y-m-d'), $res->getUpdatedAt()->format('Y-m-d'));
    }

    public function testUpdateAnswerStatusForSurvey()
    {
        $this->assertEquals(1, SsiProjectRespondentQuery::updateAnswerStatusForSurvey(self::$dbh, 1, 1001, 11));

        $res = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId(self::$dbh, 1, 1001);
        $this->assertEquals(11, $res->getAnswerStatus());
    }

    public function testRetrieveSurveysForRespondent()
    {
        $res = SsiProjectRespondentQuery::retrieveSurveysForRespondent(self::$dbh, 1);
        $this->assertCount(3, $res);
        $this->assertInstanceOf('VendorIntegration\SSI\PC1\ProjectSurvey', $res[0]);
    }

    public function testCompletSurveysForRespondent()
    {
        $this->assertSame(3, SsiProjectRespondentQuery::completeSurveysForRespondent(self::$dbh, 1));

        $res = SsiProjectRespondentQuery::retrieveSurveysForRespondent(self::$dbh, 1);
        $this->assertCount(0, $res);
    }

    public function invalidProjectRespondentParamsProvider()
    {
        return [
        [ [], ], # w/o ssi_project_id
        [ [ 'ssi_project_id' => 1] ], # w/o ssi_mail_batch_id
        [ [ 'ssi_project_id' => 1, 'ssi_mail_batch_id' => 1,], ], # w/o start_url_id
        [ [ 'ssi_project_id' => 1, 'ssi_mail_batch_id' => 1, 'start_url_id' => 'hoge'], ], # w/o stash_data
        ];
    }

    public static function setUpBeforeClass()
    {
        self::$dbh = \TestConnectionUtils::getConnection();
    }
    public function setUp()
    {
        $day_ago_1 = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $day_ago_2 = date('Y-m-d 00:00:00', strtotime('-2 day'));
        $day_ago_5 = date('Y-m-d 00:00:00', strtotime('-5 day'));
        \TestDataUtils::buildDatabase(self::$dbh, \Symfony\Component\Yaml\Yaml::parse(<<<YAML
ssi_respondent:
  - id: 1
    user_id: 1
    status_flag: 1
    created_at: "$day_ago_1"
    updated_at: "$day_ago_1"
  - id: 2
    user_id: 2
    status_flag: 1
ssi_project:
  - id:   1001
    status_flag:  1
  - id:   2001
    status_flag:  1
  - id:   2002
    status_flag:  1
  - id:   2003
    status_flag:  1

ssi_project_respondent:
  - id:   1
    ssi_project_id:   1001
    ssi_respondent_id:    1
    start_url_id:     hoge
    answer_status:    1
    stash_data: '{"hoge":"fuga"}'
  - id:   2
    ssi_project_id:   2001
    ssi_respondent_id:    1
    start_url_id:     hoge
    answer_status:    1
    stash_data: '{"hoge":"fuga"}'
    updated_at: "$day_ago_5"
  - id:   3
    ssi_project_id:   2002
    ssi_respondent_id:    1
    start_url_id:     hoge
    answer_status:    2
    stash_data: '{"hoge":"fuga"}'
    updated_at: "$day_ago_1"
  - id:   4
    ssi_project_id:   2003
    ssi_respondent_id:    1
    start_url_id:     hoge
    answer_status:    5
    stash_data: '{"hoge":"fuga"}'
    updated_at: "$day_ago_2"
YAML
        ));
    }
}
