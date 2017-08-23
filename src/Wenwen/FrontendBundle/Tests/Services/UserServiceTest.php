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

    public function testGetSopCredentialsByOwnerType()
    {
        $sopCredentials = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);
        $this->assertEquals(27, $sopCredentials['app_id']);
        $this->assertEquals('1436424899-bd6982201fb7ea024d0926aa1b40d541badf9b4a', $sopCredentials['app_secret']);

        $sopCredentials = $this->userService->getSopCredentialsByOwnerType(OwnerType::INTAGE);
        $this->assertEquals(92, $sopCredentials['app_id']);
        $this->assertEquals('1502940122-f44c65a0fde9d389b8426f26d0519f474f29e54b', $sopCredentials['app_secret']);

        $sopCredentials = $this->userService->getSopCredentialsByOwnerType(OwnerType::ORGANIC);
        $this->assertEquals(93, $sopCredentials['app_id']);
        $this->assertEquals('1502940657-dac41e231c82caa4a5f56451dbd8cc7869afd5ba', $sopCredentials['app_secret']);
    }

    public function testGetSopCredentialsByAppId()
    {
        $sopCredentials = $this->userService->getSopCredentialsByAppId(27);
        $this->assertEquals('1436424899-bd6982201fb7ea024d0926aa1b40d541badf9b4a', $sopCredentials['app_secret']);
        $this->assertEquals(OwnerType::DATASPRING, $sopCredentials['owner_type']);

        $sopCredentials = $this->userService->getSopCredentialsByAppId(92);
        $this->assertEquals('1502940122-f44c65a0fde9d389b8426f26d0519f474f29e54b', $sopCredentials['app_secret']);
        $this->assertEquals(OwnerType::INTAGE, $sopCredentials['owner_type']);

        $sopCredentials = $this->userService->getSopCredentialsByAppId(93);
        $this->assertEquals('1502940657-dac41e231c82caa4a5f56451dbd8cc7869afd5ba', $sopCredentials['app_secret']);
        $this->assertEquals(OwnerType::ORGANIC, $sopCredentials['owner_type']);
    }

    public function testGetAllSopCredentials()
    {
        $sopCredentialsList = $this->userService->getAllSopCredentials();
        $this->assertEquals(3, count($sopCredentialsList));
    }

    public function testGetAppIdByOwnerType()
    {
        $this->assertEquals(27, $this->userService->getAppIdByOwnerType(OwnerType::DATASPRING));
        $this->assertEquals(92, $this->userService->getAppIdByOwnerType(OwnerType::INTAGE));
        $this->assertEquals(93, $this->userService->getAppIdByOwnerType(OwnerType::ORGANIC));
    }

    public function testGetAppSecretByAppId()
    {
        $this->assertEquals('1436424899-bd6982201fb7ea024d0926aa1b40d541badf9b4a', $this->userService->getAppSecretByAppId(27));
        $this->assertEquals('1502940122-f44c65a0fde9d389b8426f26d0519f474f29e54b', $this->userService->getAppSecretByAppId(92));
        $this->assertEquals('1502940657-dac41e231c82caa4a5f56451dbd8cc7869afd5ba', $this->userService->getAppSecretByAppId(93));
    }

    public function testAll()
    {
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();

        $this->userService->createSopRespondent($users[0]->getId(), OwnerType::DATASPRING);
        $sopRespondent = $this->userService->getSopRespondentByUserId($users[0]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[0]->getId());

        $this->userService->createSopRespondent($users[1]->getId(), OwnerType::INTAGE);
        $sopRespondent = $this->userService->getSopRespondentByUserId($users[1]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[1]->getId());

        $this->userService->createSopRespondent($users[2]->getId(), OwnerType::ORGANIC);
        $sopRespondent = $this->userService->getSopRespondentByUserId($users[2]->getId());
        $user = $this->userService->getUserBySopRespondentAppMid($sopRespondent->getAppMid());
        $this->assertEquals($user->getId(), $users[2]->getId());
    }
}
