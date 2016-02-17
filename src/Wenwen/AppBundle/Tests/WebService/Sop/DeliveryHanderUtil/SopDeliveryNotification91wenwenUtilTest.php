<?php
namespace Wenwen\AppBundle\Tests\WebService\Sop\DeliveryHanderUtil\SopDeliveryNotification91wenwenUtil;

use Wenwen\AppBundle\WebService\Sop\DeliveryHanderUtil\SopDeliveryNotification91wenwenUtil;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class SopDeliveryNotification91wenwenUtilTest extends KernelTestCase
{
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->em = $em;
        $this->container = static::$kernel->getContainer();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @dataProvider respondentsProvider
     * @group dev-merge-ui-delivery-notification
     */
    public function test_sendMailing($respondents)
    {
        $job_ids = SopDeliveryNotification91wenwenUtil::sendMailing($this->container, $respondents);
        //         $this->assertCount(1, $job_ids);


        //         $job = TheSchwartzJob::select($job_ids[0]);
        //         $arg = json_decode($job['arg'], true);
        //         $this->assertSame('message', $arg['platform']);
        //         $this->assertSame('89998', $arg['mailing_id']);
        //         $this->assertSame('23', $arg['campaign_id']);
        //         $this->assertCount(2, $arg['add_recipients']);
        //         $this->assertRegExp('/tmp_sop_notification-\d\d\d\d\d\d\d\d\d\d\d\d\d\d-........-page1/', $arg['add_group']['group_name']);
    }

    /**
     * @dataProvider respondentsProvider
     * @group dev-merge-ui-delivery-notification
     */
    public function test_getRecipientFromRespondent($respondents)
    {
        $this->assertEquals(array (
            'name1' => 'name_f_1',
            'email' => 'takafumi_sekiguchi+1@voyagegroup.com',
            'title' => '先生',
            'survey_title' => 'Example survey title',
            'survey_point' => '1234',
            'survey_length' => '10'
        ), SopDeliveryNotification91wenwenUtil::getRecipientFromRespondent($respondents[0]));
    }

    public function respondentsProvider()
    {
        return array (
            array (
                array (
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
                        ),
                        'recipient' => array (
                            'email' => 'takafumi_sekiguchi+1@voyagegroup.com',
                            'name1' => 'name_f_1',
                            'title' => '先生'
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
                        ),
                        'recipient' => array (
                            'email' => 'takafumi_sekiguchi+2@voyagegroup.com',
                            'name1' => 'name_f_2',
                            'title' => '先生'
                        )
                    )
                )
            )
        );
    }
}

