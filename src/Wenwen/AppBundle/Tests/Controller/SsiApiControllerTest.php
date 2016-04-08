<?php

namespace Wenwen\AppBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SsiApiControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SsiApiControllerTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $em->close();
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
    }

    public function testRequestWithValidRespondentList()
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
                'respondentList' => [
               ['respondentId' => 'wwcn-' . SsiApiControllerTestFixture::$SSI_RESPONDENT->getId(), 'startUrlId' => 'sur1'],
               ['respondentId' => 'wwcn-9998', 'startUrlId' => ''],
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
    }

    public function respondentListProvider()
    {
        return [
          [
            [
                ['respondentId' => 'wwcn-9997', 'startUrlId' => 'sur1'],
                ['respondentId' => 'wwcn-9998', 'startUrlId' => 'sur2'],
                ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
            ],
            [
              'generalResponseCode' => '203',
            ],
          ],
         [
           [
               ['respondentId' => 'wwcn-9997', 'startUrlId' => ''],
               ['respondentId' => 'wwcn-9998', 'startUrlId' => 'sur2'],
               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
           ],
           [
             'generalResponseCode' => '203',
             'additionalResponseCodes' => [
               '202' => ['wwcn-9997'],
               ],
           ],
         ],
         [
           [
               ['respondentId' => 'wwcn-9997', 'startUrlId' => ''],
               ['respondentId' => 'wwcn-9998', 'startUrlId' => ''],
               ['respondentId' => 'wwcn-9999', 'startUrlId' => 'sur3'],
           ],
           [
             'generalResponseCode' => '202',
             'additionalResponseCodes' => [
               '203' => ['wwcn-9999'],
               ],
           ],
         ],
        ];
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiApiControllerTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $USER, $SSI_RESPONDENT;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        self::$USER = $user;
        self::$SSI_RESPONDENT = $ssi_respondent;
    }
}
