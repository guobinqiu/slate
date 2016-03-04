<?php

namespace Wenwen\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSopData;

class SopApiControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $sopRespondent;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
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
     * @group dev-merge-ui-profile_point
     */
    public function testAddPointAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $sop_config = $container->getParameter('sop');
        $sopRespondent = $this->sopRespondent;

        $url = $container->get('router')->generate('_sop_profile_point');

        $crawler = $client->request('GET', $url, array (
            'hash' => 'hashhashhash',
            'name' => 'q001',
            'time' => '1234',
            'sig' => '1234abc'
        ));

        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: GET Request is forbidden');
        $this->assertContains('{"meta":{"code":405,"message":"method not allowed"}}', $client->getResponse()->getContent());

        /** API /sop/v1_1/profile_point **/
        // no http param
        {
            $crawler = $client->request('POST', $url, array (
                'hash' => 'hashhashhash',
                'name' => 'q001',
                'time' => '1234',
                'sig' => '1234abc'
            ));
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: no app_mid param');
            $this->assertContains('{"meta":{"code":400,"message":["app_mid is required"]}}', $client->getResponse()->getContent());
            $crawler = $client->request('POST', $url, array (
                'app_mid' => $sopRespondent[0]->getId(),
                'name' => 'q001',
                'time' => '1234',
                'sig' => '1234abc'
            ));
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: no hash param');
            $this->assertContains('{"meta":{"code":400,"message":["hash is required"]}}', $client->getResponse()->getContent());

            $crawler = $client->request('POST', $url, array (
                'app_mid' => $sopRespondent[0]->getId(),
                'hash' => 'hashhashhash',
                'time' => '1234',
                'sig' => '1234abc'
            ));
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: no name param');
            $this->assertContains('{"meta":{"code":400,"message":["name is required"]}}', $client->getResponse()->getContent());

            $crawler = $client->request('POST', $url, array ());
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: no param');
            $this->assertContains('{"meta":{"code":400,"message":["app_mid is required","hash is required","name is required","time is invalid","sig is invalid"]}}', $client->getResponse()->getContent());
        }

        // invalid app_mid
        {
            $crawler = $client->request('POST', $url, array (
                'hash' => 'hashhashhash',
                'app_mid' => '99999',
                'name' => 'q001',
                'time' => '1234',
                'sig' => '1234abc'
            ));
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: app_mid does\'nt exit');
            $this->assertContains('{"meta":{"code":400,"message":"invalid app_mid"}}', $client->getResponse()->getContent());

            $crawler = $client->request('POST', $url, array (
                'hash' => 'hashhashhash',
                'app_mid' => $sopRespondent[1]->getId(),
                'name' => 'q001',
                'time' => '1234',
                'sig' => '1234abc'
            ));
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: app_mid inactive');
            $this->assertContains('{"meta":{"code":400,"message":"invalid app_mid"}}', $client->getResponse()->getContent());
        }

        // invalid signature
        {
            $params = array (
                'hash' => 'hashhashhash',
                'app_id' => '27',
                'app_mid' => $sopRespondent[0]->getId(),
                'name' => 'q001',
                'time' => time()
            );
            $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, 'hoge');

            $crawler = $client->request('POST', $url, $params);
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: sig invalid');
            $this->assertContains('authentication failed', $client->getResponse()->getContent());
        }

        // duplicated hash
        {
            $params = array (
                'hash' => 'duplicated',
                'app_id' => '27',
                'app_mid' => $sopRespondent[0]->getId(),
                'name' => 'q001',
                'time' => time()
            );
            $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sop_config['auth']['app_secret']);

            $crawler = $client->request('POST', $url, $params);
            $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: duplicated hash');
            $this->assertContains('{"meta":{"code":400,"message":"point already added"}}', $client->getResponse()->getContent());
        }

        //  valid parameters and isnert record
        {
            $params = array (
                'hash' => 'my-hash' . time(),
                'app_id' => '27',
                'app_mid' => $sopRespondent[0]->getId(),
                'name' => 'q001',
                'time' => time()
            );
            $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sop_config['auth']['app_secret']);

            $crawler = $client->request('POST', $url, $params);
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'API /sop/v1_1/profile_point: valid parameters and isnert record (91wenwen)');
            $this->assertContains('{"meta":{"code":200}}', $client->getResponse()->getContent());
        }

        // check DB
        $user_id = $sopRespondent[0]->getUserId();
        $sopProfilePoint = $em->getRepository('WenwenAppBundle:SopProfilePoint')->findOneBy(array (
            'userId' => $user_id,
            'name' => 'q001'
        ));
        $this->assertEquals($sopProfilePoint->getHash(), $params['hash']);

        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(1, $task->getPoint());
        $this->assertEquals('q001 属性问卷', $task->getTaskName());

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(1, $point->getPointChangeNum());
        $this->assertEquals(93, $point->getReason());

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals(101, $user->getPoints());
    }
}
