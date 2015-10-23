<?php
namespace Jili\ApiBundle\Tests\Services\Weibo;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserLoginTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $cotainer;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $container  = static::$kernel->getContainer();
        $this->container = $container;

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testContainerGet()
    {
        $container = $this->container;
        $login_service = $container->get('user_login');
        $this->assertInstanceOf('Jili\\ApiBundle\\Services\\Weibo\\UserLogin',$login_service,
            'login listener is instance of  Jili\\ApiBundle\\Services\\Weibo\\UserLogin');

    }

    public function test_checkLoginStatus()
    {
        $container = $this->container;
        $login_service = $container->get('user_login');

        $this->assertFalse( $login_service->checkLoginStatus(), 'no uid in session, false status');
        $container->get('session')->set('uid', 123);;
        $this->assertTrue( $login_service->checkLoginStatus(), 'has uid in session, true status');
    }

    public function test_getLoginUserId() 
    {
        $container = $this->container;
        $login_service = $container->get('user_login');

        $this->assertNull( $login_service->getLoginUserId(), 'no uid in session, null return');
        $container->get('session')->set('uid', 123);;

        $this->assertEquals( $login_service->getLoginUserId(), 123,  'has uid in session, 123 return');

    }
}
