<?php

namespace Wenwen\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;
use Wenwen\FrontendBundle\Model\OwnerType;

class SopApiControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $appId;
    private $appSecret;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();


        $paramSopApps = $this->container->get('app.parameter_service')->getParameter('sop_apps');

        $this->appId = $paramSopApps[0]['app_id'];
        $this->appSecret = $paramSopApps[0]['app_secret'];
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->clear();
        $this->em->close();
    }

    /**
     * @group DeliveryNotificationFor91wenwen
     */
    public function testDeliveryNotificationFor91wenwenAction200andNoNotFoundRespondent()
    {
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterCompleteDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $sopRespondent->setAppId($this->appId);
        $this->em->persist($sopRespondent);
        $this->em->flush();

        $url = $this->container->get('router')->generate('sop_delivery_notification_v1_1_91wenwen');

        $params = array (
            'app_id' => $sopRespondent->getAppId(),
            'time' => time(),
            'data' => array (
                'respondents' => array (
                    array (
                        'app_mid' => $sopRespondent->getAppMid(),
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
                )
            )
        );

        $requestBody = json_encode($params);
        $sig = \SOPx\Auth\V1_1\Util::createSignature($requestBody, $this->appSecret);

        $client = static::createClient(array(),array('HTTPS' => true));
        $crawler = $client->request('POST', $url, array (
            'request_body' => $requestBody
        ), array (), array (
            'HTTP_X-Sop-Sig' => $sig,
            'HTTPS' => true
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Valid request to 91wenwen');

        $res = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(array (
            'meta' => array (
                'code' => 200,
                'message' => ''
            )
        ), $res);
    }

    /**
     * @group DeliveryNotificationFor91wenwen
     */
    public function testDeliveryNotificationFor91wenwenAction403()
    {
        $params = array (
            'app_id' => $this->appId,
            'time' => '123456',
            'data' => array (
                'respondents' => array (
                    array (
                        'app_mid' => '12345',
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
                )
            )
        );

        $requestBody = json_encode($params);
        $sig = \SOPx\Auth\V1_1\Util::createSignature($requestBody, $this->appSecret);

        $url = $this->container->get('router')->generate('sop_delivery_notification_v1_1_91wenwen');

        $client = static::createClient(array(),array('HTTPS' => true));
        $crawler = $client->request('POST', $url, array (
            'request_body' => $requestBody
        ), array (), array (
            'HTTP_X-Sop-Sig' => $sig,
            'HTTPS' => true
        ));

        $this->assertEquals(403, $client->getResponse()->getStatusCode(), ' invalid request');

        $res = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(array (
            'meta' => array (
                'code' => 403,
                'message' => ' invalid request'
            )
        ), $res);
    }



    /**
     * @group DeliveryNotificationFor91wenwen
     */
    public function testDeliveryNotificationFor91wenwenAction400()
    {
        $params = array (
            'app_id' => $this->appId,
            'time' => time(),
            'data' => array (
                )
        );

        $requestBody = json_encode($params);
        $sig = \SOPx\Auth\V1_1\Util::createSignature($requestBody, $this->appSecret);

        $url = $this->container->get('router')->generate('sop_delivery_notification_v1_1_91wenwen');

        $client = static::createClient(array(),array('HTTPS' => true));
        $crawler = $client->request('POST', $url, array (
            'request_body' => $requestBody
        ), array (), array (
            'HTTP_X-Sop-Sig' => $sig,
            'HTTPS' => true
        ));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'data.respondents was not found');

        $res = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(array (
            'meta' => array (
                'code' => 400,
                'message' => 'data.respondents was not found!'
            )
        ), $res);
    }
}
