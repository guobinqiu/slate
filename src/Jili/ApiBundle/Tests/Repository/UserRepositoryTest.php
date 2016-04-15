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

        if (in_array($tn, array (
            'testGetSearchUserCount',
            'testGetSearchUserList',
            'testGetSearchUserSqlQuery'
        ))) {
            $fixture = new LoadMergedUserData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

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
     * @group point_recent
     */
    public function testGetRecentPoint()
    {
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
    public function testCreateOnSignup()
    {
        $em = $this->em;
        $param = array (
            'email' => 'chiangtor@gmail.com',
            'nick' => 'chiangtor'
        );
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);
        $param2 = $param;
        $param2['remote_address'] = '127.0.0.1';
        $param2['user_agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36';
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup($param2);
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
        $param['points'] = 1;
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
        $param['points'] = 1;
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

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserCount()
    {
        $result = $this->em->getRepository('JiliApiBundle:User')->getSearchUserCount(array (), "registered");
        $this->assertEquals(8, $result, "registered user count : " . $result);
        $result = $this->em->getRepository('JiliApiBundle:User')->getSearchUserCount(array (), "withdrawal");
        $this->assertEquals(5, $result, "withdrawal user count : " . $result);
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserList()
    {
        $pageSize = 3;
        $currentPage = 0;
        $result = $this->em->getRepository('JiliApiBundle:User')->getSearchUserList(array (), "registered", $pageSize, $currentPage);
        $this->assertCount(3, $result, 'registered user, pageSize:3, currentPage:0, user count: ' . count($result));

        $currentPage = ceil(8 / 3);
        $result = $this->em->getRepository('JiliApiBundle:User')->getSearchUserList(array (), "registered", $pageSize, $currentPage);
        $this->assertCount(2, $result, 'registered user, pageSize:3, currentPage:last page, user count: ' . count($result));

        $this->assertEquals(32, $result[0]['id']);
        $this->assertEquals('zhangmmX@voyagegroup.com.cn', $result[0]['email']);
        $this->assertEquals('1941-2', $result[0]['birthday']);
        $this->assertEquals(2, $result[0]['sex']);
        $this->assertEquals('atg', $result[0]['nick']);
        $this->assertEquals('', $result[0]['tel']);
        $this->assertEquals('2014-08-26 17:59:05', $result[0]['registerDate']->format('Y-m-d H:i:s'));
        $this->assertEquals('2015-02-13 10:09:18', $result[0]['lastLoginDate']->format('Y-m-d H:i:s'));
        $this->assertEquals(null, $result[0]['createdRemoteAddr']);
        $this->assertEquals(null, $result[0]['campaignCode']);
        $this->assertEquals(null, $result[0]['app_mid']);
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserSqlQuery0()
    {
        $query = $this->em->getRepository('JiliApiBundle:User')->createQueryBuilder('u');
        $query = $query->select('COUNT(u.id)');

        $type = 'withdrawal';
        $values = array ();
        $query = $this->em->getRepository('JiliApiBundle:User')->getSearchUserSqlQuery($query, $values, $type);
        $this->assertEquals('SELECT COUNT(u0_.id) AS sclr0 FROM user u0_ LEFT JOIN sop_respondent s1_ ON (u0_.id = s1_.user_id) WHERE 1 = 1 AND u0_.delete_flag = 1 ORDER BY u0_.id DESC', $query->getSql());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserSqlQuery1()
    {
        $query = $this->em->getRepository('JiliApiBundle:User')->createQueryBuilder('u');
        $query = $query->select('COUNT(u.id)');

        $type = 'withdrawal';
        $values['app_mid'] = 1;
        $values = array ();
        $query = $this->em->getRepository('JiliApiBundle:User')->getSearchUserSqlQuery($query, $values, $type);
        $this->assertEquals('SELECT COUNT(u0_.id) AS sclr0 FROM user u0_ LEFT JOIN sop_respondent s1_ ON (u0_.id = s1_.user_id) WHERE 1 = 1 AND u0_.delete_flag = 1 ORDER BY u0_.id DESC', $query->getSql());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserSqlQuery2()
    {
        $query = $this->em->getRepository('JiliApiBundle:User')->createQueryBuilder('u');
        $query = $query->select('COUNT(u.id)');

        $type = 'registered';
        $values = array ();
        $query = $this->em->getRepository('JiliApiBundle:User')->getSearchUserSqlQuery($query, $values, $type);
        $this->assertEquals('SELECT COUNT(u0_.id) AS sclr0 FROM user u0_ LEFT JOIN sop_respondent s1_ ON (u0_.id = s1_.user_id) WHERE 1 = 1 AND (u0_.delete_flag IS NULL OR u0_.delete_flag = 0) ORDER BY u0_.id DESC', $query->getSql());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetSearchUserSqlQuery3()
    {
        $query = $this->em->getRepository('JiliApiBundle:User')->createQueryBuilder('u');
        $query = $query->select('COUNT(u.id)');

        $type = 'registered';
        $values['app_mid'] = 1;
        $values['user_id'] = 31;
        $values['email'] = 'test@test.com';
        $values['nickname'] = 'test';
        $values['mobile_number'] = '123';
        $values['birthday'] = '1961-09';
        $values['registered_from'] = '2015-09-08 10:00:00';
        $values['registered_to'] = '2015-09-08 10:00:00';
        $query = $this->em->getRepository('JiliApiBundle:User')->getSearchUserSqlQuery($query, $values, $type);
        $this->assertEquals('SELECT COUNT(u0_.id) AS sclr0 FROM user u0_ INNER JOIN sop_respondent s1_ ON (u0_.id = s1_.user_id) WHERE 1 = 1 AND s1_.id = ? AND u0_.id = ? AND u0_.email = ? AND u0_.nick LIKE ? AND u0_.tel = ? AND u0_.birthday = ? AND u0_.register_date >= ? AND u0_.register_date <= ? AND (u0_.delete_flag IS NULL OR u0_.delete_flag = 0) ORDER BY u0_.id DESC', $query->getSql());
    }
}
