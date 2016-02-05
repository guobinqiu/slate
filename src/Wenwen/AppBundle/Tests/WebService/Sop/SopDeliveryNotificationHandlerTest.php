<?php

namespace Wenwen\AppBundle\Tests\WebService\Sop;

use Wenwen\AppBundle\WebService\Sop\SopDeliveryNotificationHandler;

class SopDeliveryNotificationHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $handler = null;
    private static $respondents = array (
        array (
            'app_mid' => '1',
            'survey_id' => '123',
            'quota_id' => '1234',
            'loi' => '10',
            'ir' => '50',
            'cpi' => '1.50',
            'title' => 'Example survey title',
            'extra_info' => array (
                'content' => '',
                'date' => array (
                    'start_at' => '1900-01-01',
                    'end_at' => '2000-01-01'
                ),
                'point' => array (
                    'complete' => '1234',
                    'screenout' => '2345',
                    'quotafull' => '3456'
                )
            )
        ),
        array (
            'app_mid' => '2',
            'survey_id' => '123',
            'quota_id' => '1234',
            'loi' => '10',
            'ir' => '50',
            'cpi' => '1.50',
            'title' => 'Example survey title',
            'extra_info' => array (
                'content' => '',
                'date' => array (
                    'start_at' => '1900-01-01',
                    'end_at' => '2000-01-01'
                ),
                'point' => array (
                    'complete' => '1234',
                    'screenout' => '2345',
                    'quotafull' => '3456'
                )
            )
        ),
        array (
            'app_mid' => '0',
            'survey_id' => '123',
            'quota_id' => '1234',
            'loi' => '10',
            'ir' => '50',
            'cpi' => '1.50',
            'title' => 'Example survey title',
            'extra_info' => array (
                'content' => '',
                'date' => array (
                    'start_at' => '1900-01-01',
                    'end_at' => '2000-01-01'
                ),
                'point' => array (
                    'complete' => '1234',
                    'screenout' => '2345',
                    'quotafull' => '3456'
                )
            )
        )
    );

    public function setUp()
    {
        $this->handler = new SopDeliveryNotificationHandler(self::$respondents, SopDeliveryNotificationHandler::TYPE_SOP);
    }

    /**
     * @group dev-merge-ui-delivery-notification
     */
    public function testSetUpRespondentsToMail()
    {
        $this->handler->setUpRespondentsToMail();

        $unsubscribed_app_mids = $this->handler->getUnsubscribedAppMids();
//         $this->assertContains('0', $unsubscribed_app_mids);
//         $this->assertContains('2', $unsubscribed_app_mids);

        $respondents = $this->handler->getValidRespondents();
//         $this->assertCount(1, $respondents);
//         $this->assertArrayHasKey('app_mid', $respondents[0]);
//         $this->assertArrayHasKey('extra_info', $respondents[0]);
//         $this->assertArrayHasKey('recipient', $respondents[0]);
    }

    /**
     * @group dev-merge-ui-delivery-notification
     */
    public function testSendMailingToRespondents()
    {
        $this->handler->setUpRespondentsToMail();
        $job_ids = $this->handler->sendMailingToRespondents();
//         $this->assertCount(1, $job_ids);
    }

    /**
     * @group dev-merge-ui-delivery-notification
     */
    public function testInstanciateFulcrum()
    {
        $handler = new SopDeliveryNotificationHandler(self::$respondents,  SopDeliveryNotificationHandler::TYPE_FULCRUM);

        $this->assertEquals($handler->getUtilClass(), 'FulcrumDeliveryNotification91wenwenUtil');
    }

    //todo: insert data to db
/*
    public static function setUpBeforeClass()
    {
        $con = Propel::getConnection(ResearchProjectPeer::DATABASE_NAME);
        TestDataUtils::load(<<<YAML
panel:
  -
    id: 2
  -
    id: 3
panel_country:
  -
    id: 2
  -
    id: 3
panel_region:
  -
    id: 2001
    panel_country_id: 2
  -
    id: 3630
    panel_country_id: 3
panelist:
  - # 91wenwen
    id: 1
    panel_region_id: 2001
    panel_id: 2
    email: rpa-dev+1@voyagegroup.info
    login_id: dev_test_1
    login_password: lCLf99oDZHc=
    login_password_crypt_type: blowfish
    login_password_salt: password
    login_valid_flag: 1
    sex_code: 1
    birthday: 1974-08-01
    panelist_status: 1
  - # 91wenwen
    id: 2
    panel_region_id: 2001
    panel_id: 2
    email: rpa-dev+2@voyagegroup.info
    login_id: dev_test_2
    login_password: lCLf99oDZHc=
    login_password_crypt_type: blowfish
    login_password_salt: password
    login_valid_flag: 1
    sex_code: 1
    birthday: 1974-08-01
    panelist_status: 1
  - # panelnow
    id: 3
    panel_region_id: 3630
    panel_id: 3
    email: rpa-dev+3@voyagegroup.info
    login_id: dev_test_3
    login_password: lCLf99oDZHc=
    login_password_crypt_type: blowfish
    login_password_salt: password
    login_valid_flag: 1
    sex_code: 1
    birthday: 1974-08-01
    panelist_status: 1

panel_91wenwen_panelist_point:
  -
    panelist_id: 1
    point_value: 0

panel_91wenwen_panelist_detail:
  -
    panelist_id: 1
    name_first: name_f_1

panel_panelnow_panelist_point:
  -
    panelist_id: 3
    point_value: 0

panel_panelnow_panelist_detail:
  -
    panelist_id: 3
    name: name_3

sop_respondent:
  - # 91wenwen
    id : 1
    panelist_id : 1
    status_flag : 1
    stash_data : NULL
    updated_at : "2014-07-23 14:30:00"
    created_at : "2014-07-23 14:30:00"

  - # 91wenwen
    id : 2
    panelist_id : 2
    status_flag : 0
    stash_data : NULL
    updated_at : "2014-07-23 14:30:00"
    created_at : "2014-07-23 14:30:00"

  - # panelnow
    id : 3
    panelist_id : 3
    status_flag : 1
    stash_data : NULL
    updated_at : "2014-08-27 00:00:00"
    created_at : "2014-08-27 00:00:00"

sop_profile_point:
  - # duplicated
    id : 1
    panelist_id : 1
    hash : duplicated
    status_flag : 1
    stash_data : NULL
    updated_at : "2014-07-23 14:30:00"
    created_at : "2014-07-23 14:30:00"
YAML
, $con);
    }*/
}