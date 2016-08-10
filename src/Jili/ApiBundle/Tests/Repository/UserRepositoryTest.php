<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadDmdeliveryData;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoTaskHistoryData;
use Jili\ApiBundle\DataFixtures\ORM\LoadMergedUserData;

class UserRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->container = $container;
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
     * @group issue_448
     * @group issue_453
     */
    public function testCreateOnSignup()
    {
        $em = $this->em;
        $param = array (
            'email' => 'chiangtor@gmail.com',
            'nick' => 'chiangtor',
            'createdRemoteAddr' => '1.1.1.1',
            'createdUserAgent' => 'testAgent'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);
        $param2 = $param;
        $param2['remote_address'] = '127.0.0.1';
        $param2['user_agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36';
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup($param2);
        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['nick'], $user->getNick());
        //$param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }

    /**
     * @group issue_453
     */
    public function testCreateOnLanding()
    {
        $em = $this->em;
        $param = array (
            'email' => 'chiangtor@gmail.com',
            'nick' => 'chiangtor'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        // call the create()
        $param['pwd'] = '123123';
        $user = $em->getRepository('JiliApiBundle:User')->createOnLanding($param);
        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['nick'], $user->getNick());
        $this->assertEmpty($user->getUniqkey());

        // check the create user
        //$param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        unset($param['pwd']);
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);

        // case 2
        $param = array (
            'email' => 'alice.nima@gmail.com',
            'nick' => 'alice32'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        $param['pwd'] = '123123';
        $param['uniqkey'] = '0ce9189316c563fcc9f42047c2a2cf46a0144051';
        $param['isFromWenwen'] = 1;
        $user = $em->getRepository('JiliApiBundle:User')->createOnLanding($param);
        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['nick'], $user->getNick());
        $this->assertEquals($param['uniqkey'], $user->getUniqkey());

        // the the result
        //$param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        unset($param['pwd']);
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }

    /**
     * @group issue_453
     * @group issue_646
     */
    public function testCreateOnWenwen()
    {
        $em = $this->em;
        $param = array (
            'uniqkey' => '0ce9189316c563fcc9f42047c2a2cf46a0144051',
            'email' => 'chiangtor@gmail.com'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        $user = $em->getRepository('JiliApiBundle:User')->createOnWenwen($param);

        $this->assertEquals(0, $user->getPoints());
        $this->assertEquals(0, $user->getIsInfoSet());
        $this->assertEquals(1, $user->getRewardMultiple());
        $this->assertEquals(2, $user->getIsFromWenwen());
        $this->assertEmpty($user->getPwd());
        $this->assertEmpty($user->getNick());

        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['uniqkey'], $user->getUniqkey());

        $param = array (
            'uniqkey' => '123',
            'email' => 'chiangtor@gmail.com'
        );
        $user2 = $em->getRepository('JiliApiBundle:User')->createOnWenwen($param);
        $this->assertEquals($param['uniqkey'], $user2->getUniqkey());
    }

    /**
     * @group issue_474
     */
    public function testqquser_quick_insert()
    {
        $params = array (
            'nick' => 'alice32',
            'email' => 'alice_nima@gmail.com',
            'pwd' => '123qwe'
        );
        $i = $this->em->getRepository('JiliApiBundle:User')->qquser_quick_insert($params);
        $this->assertEquals('QQalice32', $i->getNick());
        $this->assertEquals('alice_nima@gmail.com', $i->getEmail());
        $this->assertEquals($i->pw_encode('123qwe'), $i->getPwd());

        $j = $this->em->getRepository('JiliApiBundle:User')->findOneBy(array (
            'nick' => 'QQalice32',
            'email' => 'alice_nima@gmail.com'
        ));
        $this->assertNotEmpty($j);
        $this->assertEquals($i->pw_encode('123qwe'), $j->getPwd());
    }

    /**
     * @group issue_535
     * @group getUserByCrossId
     */
    public function testGetUserByCrossId()
    {
        $em = $this->em;

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPwd('123456');
        $user->setDeleteFlag(0);
        $em->persist($user);
        $em->flush();

        $cross = $em->getRepository('JiliApiBundle:UserWenwenCross')->create($user->getId());

        $user = $em->getRepository('JiliApiBundle:User')->getUserByCrossId($cross->getId());
        $this->assertEquals('test@test.com', $user['email']);
    }

    /**
     * @group issue548
     * @group issue619
     */
    public function testPointFail()
    {
        $em = $this->em;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $container = static::$kernel->getContainer();
        // load fixtures
        $fixture = new LoadDmdeliveryData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $user = $em->getRepository('JiliApiBundle:User')->pointFail(180);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1110, $user[0]['id']);
        $this->assertEquals(1115, $user[1]['id']);

        $user = $em->getRepository('JiliApiBundle:User')->pointFail(150);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1110, $user[0]['id']);

        $user = $em->getRepository('JiliApiBundle:User')->pointFail(173);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1115, $user[1]['id']);
    }

    /**
     * @group issue_600
     */
    public function testAddPointHistorySearch()
    {
        $em = $this->em;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $container = static::$kernel->getContainer();

        // load fixtures
        $fixture = new LoadUserInfoCodeData();
        $fixture->setContainer($container);

        $fixture1 = new LoadUserInfoTaskHistoryData();
        $fixture1->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $loader->addFixture($fixture1);
        $executor->execute($loader->getFixtures());

        $start_time = '';
        $end_time = '';
        $category_id = '';
        $email = '';
        $user_id = '';
        $user = $em->getRepository('JiliApiBundle:User')->addPointHistorySearch($start_time, $end_time, $category_id, $email, $user_id);
        ;
        $this->assertCount(3, $user);

        $email = 'alice.nima@gmail.com';
        $start_time = date('Y-m-d');
        $end_time = date('Y-m-d');
        $user = $em->getRepository('JiliApiBundle:User')->addPointHistorySearch($start_time, $end_time, $category_id, $email, $user_id);
        ;
        $this->assertCount(3, $user);

        $user = LoadUserInfoCodeData::$USERS[0];
        $user_id = $user->getId();
        $user = $em->getRepository('JiliApiBundle:User')->addPointHistorySearch($start_time, $end_time, $category_id, $email, $user_id);
        ;
        $this->assertCount(3, $user);
    }

    /**
     * @group issue636
     */
    public function testWeiBo_user_quick_insert()
    {
        $params = array (
            'nick' => 'alice32',
            'email' => 'alice_nima@gmail.com',
            'pwd' => '123qwe'
        );
        $i = $this->em->getRepository('JiliApiBundle:User')->weibo_user_quick_insert($params);
        $this->assertEquals('WeiBo_alice32', $i->getNick());
        $this->assertEquals('alice_nima@gmail.com', $i->getEmail());
        $this->assertEquals($i->pw_encode('123qwe'), $i->getPwd());
        $j = $this->em->getRepository('JiliApiBundle:User')->findOneBy(array (
            'nick' => 'weibo_alice32',
            'email' => 'alice_nima@gmail.com'
        ));
        $this->assertNotEmpty($j);
        $this->assertEquals($i->pw_encode('123qwe'), $j->getPwd());
    }
}
