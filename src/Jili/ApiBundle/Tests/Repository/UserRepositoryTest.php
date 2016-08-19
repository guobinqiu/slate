<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadDmdeliveryData;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoTaskHistoryData;

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
        $param['rewardMultiple'] = 1;
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
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

}
