<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Model\OwnerType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserServiceTest extends WebTestCase
{
    private $container;
    private $em;
    private $userService;
    private $sopRespondentService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;

        $this->userService = $container->get('app.user_service');
        $this->sopRespondentService = $container->get('app.sop_respondent_service');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testSerializer()
    {
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');

        $serializer = $this->container->get('jms_serializer');
        $str = $serializer->serialize($user, 'json');

        $user = $serializer->deserialize($str, 'Wenwen\FrontendBundle\Entity\User', 'json');
        $this->assertEquals('user1', $user->getNick());
    }

    public function testIsRegisteredFingerPrint(){
        $fingerprint = 1234567890; // any fingerprint
        $key = CacheKeys::REGISTER_FINGER_PRINT_PRE . $fingerprint;
        $redis = $this->container->get('snc_redis.default');

        $redis->del($key);

        // first time return count 1
        $this->assertEquals(1, $this->userService->getRegisteredFingerPrintCount($fingerprint));
        $this->assertEquals(1, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT >= $redis->ttl($key));

        // second time return count 2
        $this->assertEquals(2, $this->userService->getRegisteredFingerPrintCount($fingerprint));
        $this->assertEquals(2, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT * 2 >= $redis->ttl($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT < $redis->ttl($key));

        // exceeded maximum return count maximum
        $redis->del($key);
        $redis->set($key, CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT);
        $redis->expire($key, CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT);
        $this->assertEquals(CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT, $this->userService->getRegisteredFingerPrintCount($fingerprint));
        $this->assertEquals(CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT <= $redis->ttl($key));

        // after test clean up
        $redis->del($key);
    }

    public function testGetUserBySopRespondentAppMid()
    {
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();

        $this->sopRespondentService->createSopRespondent($users[0]->getId());
        $sopRespondent = $this->sopRespondentService->getSopRespondentByUserId($users[0]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[0]->getId());

        $this->sopRespondentService->createSopRespondent($users[1]->getId());
        $sopRespondent = $this->sopRespondentService->getSopRespondentByUserId($users[1]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[1]->getId());

        $this->sopRespondentService->createSopRespondent($users[2]->getId());
        $sopRespondent = $this->sopRespondentService->getSopRespondentByUserId($users[2]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[2]->getId());
    }
}
