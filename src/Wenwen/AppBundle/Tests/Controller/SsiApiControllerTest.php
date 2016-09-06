<?php

namespace Wenwen\AppBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SsiApiControllerTest extends WebTestCase
{


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

        public function testRequestWithValidRespondentList()
    {

    	$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();


        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('test');
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $em->persist($user);
        $em->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $em->persist($ssiRespondent);
        $em->flush();

        $ssiRespondentId1 = $ssiRespondent->getId();

        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('test2');
        $user->setEmail('test2@d8aspring.com');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $em->persist($user);
        $em->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $em->persist($ssiRespondent);
        $em->flush();

        $ssiRespondentId2 = $ssiRespondent->getId();



        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/ssi_pc1_protocol/request_api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
                ],
                'startUrlHead' => 'http://www.d8aspring.com/?test=',
                'respondentList' => [
               ['respondentId' => 'wwcn-' . $ssiRespondentId1, 'startUrlId' => 'sur1'],
               ['respondentId' => 'wwcn-9998', 'startUrlId' => ''],
               ['respondentId' => 'wwcn-' . $ssiRespondentId2, 'startUrlId' => 'sur2'],
               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
           ],
                ]
            )
        );

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(
           [
             'generalResponseCode' => '201',
             'additionalResponseCodes' => [
               '202' => ['wwcn-9998'],
               '203' => ['wwcn-9999'],
               ],
           ], json_decode($client->getResponse()->getContent(), true));

        $jobs = $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(1, $jobs);
        $this->assertInstanceOf('JMS\JobQueueBundle\Entity\Job', $jobs[0]);
 
    }

    public function testHandleRequestAction201(){

		$em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();


        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('xiaoyi.chai@d8aspring.com');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $em->persist($user);
        $em->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $em->persist($ssiRespondent);
        $em->flush();

        $ssiRespondentId1 = $ssiRespondent->getId();

        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('test2');
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $em->persist($user);
        $em->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $em->persist($ssiRespondent);
        $em->flush();

        $ssiRespondentId2 = $ssiRespondent->getId();

        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/ssi_pc1_protocol/request_api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
                ],
                'startUrlHead' => 'http://www.d8aspring.com/?test=',
                'respondentList' => [
               ['respondentId' => 'wwcn-' . $ssiRespondent->getId(), 'startUrlId' => 'sur1'],
               ['respondentId' => 'wwcn-9998', 'startUrlId' => ''],
               ['respondentId' => 'wwcn-' . $ssiRespondentId2, 'startUrlId' => 'sur2'],
               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
           ],
                ]
            )
        );
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(
           [
             'generalResponseCode' => '201',
             'additionalResponseCodes' => [
               '202' => ['wwcn-9998'],
               '203' => ['wwcn-9999'],
               ],
           ], json_decode($client->getResponse()->getContent(), true));

        $jobs = $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(1, $jobs);
        $this->assertInstanceOf('JMS\JobQueueBundle\Entity\Job', $jobs[0]);
    }

    public function testWithoutBody()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/ssi_pc1_protocol/request_api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertEquals(
            [
            'generalResponseCode' => '202',
            ],
            json_decode(
                $client->getResponse()->getContent(),
                true
            )
        );
    }

    public function testWithInvalidRequest()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/ssi_pc1_protocol/request_api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"requestHeader":{}}'
        );
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertEquals(
            [
            'generalResponseCode' => '202',
            ],
            json_decode(
                $client->getResponse()->getContent(),
                true
            )
        );
    }

    /**
     * @dataProvider respondentListProvider
     */
    /*
    public function testRequestWithInvalidRespondentList($respondentList, $expected)
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/ssi_pc1_protocol/request_api',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
                ],
                'startUrlHead' => 'http://www.d8aspring.com/?test=',
                'respondentList' => $respondentList,
                ]
            )
        );
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals($expected, json_decode($client->getResponse()->getContent(), true));

        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $jobs = $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(0, $jobs);
    }
    */


//    public function respondentListProvider()
//    {
//        return [
//          [
//            [
//                ['respondentId' => 'wwcn-9997', 'startUrlId' => 'sur1'],
//                ['respondentId' => 'wwcn-9998', 'startUrlId' => 'sur2'],
//                ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
//            ],
//            [
//              'generalResponseCode' => '203',
//            ],
//          ],
//         [
//           [
//               ['respondentId' => 'wwcn-9997', 'startUrlId' => ''],
//               ['respondentId' => 'wwcn-9998', 'startUrlId' => 'sur2'],
//               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
//           ],
//           [
//             'generalResponseCode' => '203',
//             'additionalResponseCodes' => [
//               '202' => ['wwcn-9997'],
//               ],
//           ],
//         ],
//         [
//           [
//               ['respondentId' => 'wwcn-9997', 'startUrlId' => ''],
//               ['respondentId' => 'wwcn-9998', 'startUrlId' => ''],
//               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
//           ],
//           [
//             'generalResponseCode' => '202',
//             'additionalResponseCodes' => [
//               '203' => ['wwcn-9999'],
//               ],
//           ],
//         ],
//        ];
//    }
//}
//
//use Doctrine\Common\DataFixtures\FixtureInterface;
//use Doctrine\Common\Persistence\ObjectManager;
//use Symfony\Component\DependencyInjection\ContainerAwareInterface;
//use Symfony\Component\DependencyInjection\ContainerInterface;
//
//class SsiApiControllerTestFixture implements FixtureInterface, ContainerAwareInterface
//{
//    public static $USER, $SSI_RESPONDENT;
//    private $container;
//
//    public function setContainer(ContainerInterface $container = null)
//    {
//        $this->container = $container;
//    }
//
//    public function load(ObjectManager $manager)
//    {
//        $user = new \Jili\ApiBundle\Entity\User();
//        $user->setNick(__CLASS__);
//        $user->setEmail('test@d8aspring.com');
//        $user->setPoints(100);
//        $user->setIconPath('test/test_icon.jpg');
//        $user->setRewardMultiple(1);
//        $user->setPwd('password');
//        $user->setIsEmailConfirmed(1);
//        $user->setRegisterDate(new \DateTime());
//        $manager->persist($user);
//        $manager->flush();
//
//        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
//        $ssi_respondent->setUser($user);
//        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
//        $manager->persist($ssi_respondent);
//        $manager->flush();
//
//        self::$USER = $user;
//        self::$SSI_RESPONDENT = $ssi_respondent;
//    }
}
