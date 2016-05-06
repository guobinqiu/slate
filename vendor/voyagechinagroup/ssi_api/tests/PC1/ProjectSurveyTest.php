<?php

namespace VendorIntegration\SSI\tests\PC1\Model\Row\SsiProjectRespondent;

use \VendorIntegration\SSI\PC1\Constants;
use \VendorIntegration\SSI\PC1\ProjectSurvey;

class ProjectSurveyTest extends \PHPUnit_Framework_TestCase
{
    protected $respondent = null;
    public function setUp()
    {
        $this->respondent = new ProjectSurvey(
            [
            'id' => 1,
            'ssi_project_id' => 2,
            'ssi_respondent_id' => 3,
            'start_url_id' => 'sui',
            'answer_status' => 4,
            'created_at' => '2016-01-01 12:00:00',
            'updated_at' => '2016-01-01 15:00:00',
            'stash_data' => json_encode([ 'startUrlHead' => 'suh' ]),
            ]
        );
    }
    public function testGetId()
    {
        $this->assertSame(1, $this->respondent->getId());
    }
    public function testGetSsiProjectId()
    {
        $this->assertSame(2, $this->respondent->getSsiProjectId());
    }
    public function testGetSsiRespondentId()
    {
        $this->assertSame(3, $this->respondent->getSsiRespondentId());
    }
    public function testGetStartUrlId()
    {
        $this->assertSame('sui', $this->respondent->getStartUrlId());
    }
    public function testGetAnswerStatus()
    {
        $this->assertSame(4, $this->respondent->getAnswerStatus());
    }
    public function testGetStashData()
    {
        $this->assertEquals(['startUrlHead' => 'suh'], $this->respondent->getStashData());
    }
    public function testGetCreatedAt()
    {
        $this->assertInstanceOf('\DateTime', $this->respondent->getCreatedAt());
        $this->assertSame('2016-01-01 12:00:00', $this->respondent->getCreatedAt()->format('Y-m-d H:i:s'));
    }
    public function testGetUpdatedAt()
    {
        $this->assertInstanceOf('\DateTime', $this->respondent->getUpdatedAt());
        $this->assertSame('2016-01-01 15:00:00', $this->respondent->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    /**
     * @dataProvider isOpenTestProvider
     */
    public function testIsOpen($answer_status, $expected)
    {
        $this->assertTrue($this->respondent->isOpen());

        $respondent = new ProjectSurvey(
            [
            'answer_status' =>  $answer_status,
            ]
        );
        $this->assertSame($expected, $respondent->isOpen());
    }

    public function testGetStartUrlTestWithValidParams()
    {
        $this->assertSame('suhsui', $this->respondent->getStartUrl());
    }

    /**
     * @expectedException Exception
     * @dataProvider invalidRespondentsProvider
     */
    public function testGetStartUrlTestWithInvalidParams($respondent)
    {
        $respondent->getStartUrl();
    }

    public function isOpenTestProvider()
    {
        return [
        [ Constants::SSI_PROJECT_RESPONDENT_STATUS_INIT, true],
        [ Constants::SSI_PROJECT_RESPONDENT_STATUS_REOPENED, true],
        [ Constants::SSI_PROJECT_RESPONDENT_STATUS_FORWARDED, true],
        [ Constants::SSI_PROJECT_RESPONDENT_STATUS_COMPLETE, false],
        ];
    }

    public function invalidRespondentsProvider()
    {
        return [
            [ new ProjectSurvey(
                [
                'start_url_id' => null,
                'stash_data' => json_encode([ 'startUrlHead' => 'suh' ]),
                ]
            ),
            ],
            [ new ProjectSurvey(
                [
                'start_url_id' => 'sui',
                'stash_data' => '{}',
                ]
            ),
            ],
            [ new ProjectSurvey(
                [
                'start_url_id' => 'sui',
                'stash_data' => json_encode([ 'startUrlHead' => null ]),
                ]
            ),
            ],
        ];

    }
}
