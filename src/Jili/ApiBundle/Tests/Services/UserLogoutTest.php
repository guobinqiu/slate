<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\Services\LoadUserLoginData;

class UserLogoutTest extends WebTestCase
{
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));

        static::$kernel->boot();
        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $loader = new Loader();
        $fixture = new UserLogoutTestFixture();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

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
     */
    public function testLogout()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_login', array (), true);
        $client->request('POST', $url, array (
            'email' => 'test@d8aspring.com',
            'pwd' => '111111q',
            'remember_me' => '1'
        ));
        $client->followRedirect();

        $session = $container->get('session');
        $this->assertTrue($session->has('uid'));
        $this->assertTrue($session->has('nick'));
        $session->clear();

        $logout_service = $container->get('user_logout');
        $logout_service->logout($client->getRequest());

        $this->assertFalse($session->has('uid'));
        $this->assertFalse($session->has('nick'));
        $this->assertFalse($session->has('referer'));

        $cookies = $client->getCookieJar();
        $this->assertEmpty($cookies->get('jili_uid', '/'));
        $this->assertEmpty($cookies->get('jili_nick', '/'));
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\User;

class UserLogoutTestFixture implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setIsEmailConfirmed(1);
        $user->setPwd('111111q');
        $manager->persist($user);
        $manager->flush();
    }
}
