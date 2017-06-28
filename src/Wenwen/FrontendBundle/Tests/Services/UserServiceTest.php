<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserServiceTest extends WebTestCase
{
    private $container;
    private $em;
    private $userService;

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
        echo $str;

        $user = $serializer->deserialize($str, 'Wenwen\FrontendBundle\Entity\User', 'json');
        $this->assertEquals('user1', $user->getNick());
    }

    public function testIsRegisteredFingerPrint(){
        $fingerprint = 1234567890; // any fingerprint
        $key = CacheKeys::REGISTER_FINGER_PRINT_PRE . $fingerprint;
        $redis = $this->container->get('snc_redis.default');

        $redis->del($key);

        // first time return false
        $this->assertEquals(0, $this->userService->isRegisteredFingerPrint($fingerprint));
        $this->assertEquals(1, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT >= $redis->ttl($key));

        // second time return true
        $this->assertEquals(1, $this->userService->isRegisteredFingerPrint($fingerprint));
        $this->assertEquals(2, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT * 2 >= $redis->ttl($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT < $redis->ttl($key));

        // exceeded maximum return true
        $redis->del($key);
        $redis->set($key, CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT);
        $redis->expire($key, CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT);
        $this->assertEquals(CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT, $this->userService->isRegisteredFingerPrint($fingerprint));
        $this->assertEquals(CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT, $redis->get($key));
        $this->assertTrue(CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT <= $redis->ttl($key));

        // after test clean up
        $redis->del($key);
    }
}
