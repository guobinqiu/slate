<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadDmdeliveryData;
use Doctrine\Common\DataFixtures\Loader;

class UserRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $tn = $this->getName();
        if (in_array($tn, array (
                'testGetRecentPoint'
            ))) {
            //
            $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath();
            $directory .= '/DataFixtures/ORM/Command/PointRecent';
            $loader = new DataFixtureLoader($container);
            $loader->loadFromDirectory($directory);
            $executor->execute($loader->getFixtures());
        }
        
        $this->container = $container;
        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group point_recent
     */
    public function testGetRecentPoint() {
        $em = $this->em;
        $date_str = '2014-03-04';
        $result = $em->getRepository('JiliApiBundle:User')->getRecentPoint($date_str);
        $this->assertCount(99, $result);
        $this->assertEquals('6a248c5c0dab72efbf0a8eab862d195b', md5(serialize($result)));
    }
    /**
     * @group issue_448
     * @group issue_453
     */
    public function testCreateOnSignup() {
        $em = $this->em;
        $param = array (
            'email' => 'chiangtor@gmail.com',
            'nick' => 'chiangtor'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup($param);
        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['nick'], $user->getNick());
        $param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }
    /**
     * @group issue_453
     */
    public function testCreateOnLanding() {
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
        $param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        unset ($param['pwd']);
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
        $param['points'] = 1;
        $param['isInfoSet'] = 1;
        $param['rewardMultiple'] = 1;
        unset ($param['pwd']);
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }
    /**
     * @group issue_453
     */
    public function testCreateOnWenwen() {
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
    }
    /**
     * @group issue_474
     */
    public function testqquser_quick_insert() {
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
    public function testGetUserByCrossId() {
        $em = $this->em;

        $user = new User;
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
     */
    public function testPointFail() {
        $em = $this->em;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $container = static :: $kernel->getContainer();
        // load fixtures
        $fixture = new LoadDmdeliveryData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        
        $user = $em->getRepository('JiliApiBundle:User')->pointFail(180);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1110, $user[0]['id']);
    }
    
}