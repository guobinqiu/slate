<?php
namespace Wenwen\AppBundle\Tests\WebService\Sop;

use Wenwen\AppBundle\WebService\Sop\SopDeliveryNotificationHandler;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSopData;

class SopDeliveryNotificationHandlerTest extends KernelTestCase
{
    private $em;
    private $container;
    private $sopRespondent;
    protected $handler = null;

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
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserSopData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->sopRespondent = LoadUserSopData::$SOP_RESPONDENT;

        $this->em = $em;
        $this->container = $container;

        $this->handler = new SopDeliveryNotificationHandler($this->getRespondentsTestData(), SopDeliveryNotificationHandler::TYPE_SOP, $em, $this->container);
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
     * @group dev-merge-ui-delivery-notification
     */
    public function testSetUpRespondentsToMail()
    {
        $this->handler->setUpRespondentsToMail();

        $unsubscribed_app_mids = $this->handler->getUnsubscribedAppMids();

        $this->assertContains('100', $unsubscribed_app_mids);
        $this->assertContains('101', $unsubscribed_app_mids);

        $respondents = $this->handler->getValidRespondents();

        $this->assertCount(1, $respondents);
        $this->assertArrayHasKey('app_mid', $respondents[0]);
        $this->assertArrayHasKey('extra_info', $respondents[0]);
        $this->assertArrayHasKey('recipient', $respondents[0]);
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
        $em = $this->em;
        $container = $this->container;

        $handler = new SopDeliveryNotificationHandler($this->getRespondentsTestData(), SopDeliveryNotificationHandler::TYPE_FULCRUM, $em, $container);

        $this->assertEquals($handler->getUtilClass(), 'FulcrumDeliveryNotification91wenwenUtil');
    }

    private function getRespondentsTestData()
    {
        $respondents = array (
            array (
                'app_mid' => $this->sopRespondent[0]->getId(),
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
                'app_mid' => '100',
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
                'app_mid' => '101',
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
        return $respondents;
    }
}