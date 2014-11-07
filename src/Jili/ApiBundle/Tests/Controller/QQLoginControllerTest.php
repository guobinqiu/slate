<?php
namespace Jili\ApiBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadQQUserCallbackData;

class QQLoginControllerTest extends WebTestCase
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

        $cn = get_class(static::$kernel);
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container  = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();

        // load fixtures
        if( $tn === 'testCallBackAction') {
            $fixture = new LoadQQUserCallbackData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

        $this->container = $container;
        $this->em  = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->em->close();
        parent::tearDown();
    }

    /**
     * @group issue_474
     * @group debug
     */
    public function testCallBackAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $session = $container->get('session');
        $em = $this->em;

        $url = $this->container->get('router')->generate('qq_api_callback');
        $this->assertEquals('/QQLogin/qqcallback', $url);

        $crawler =  $client->request('GET', $url, array('code'=>''));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，QQ用户授权失败，请稍后再试。', $crawler->filter('div.errorMessage')->text());

        // test no access_token
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('access_token','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubQQAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>''));

        $mockQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\QQLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockQQAuth->expects($this->once())
            ->method('getQQAuth')
            ->willReturn( $stubQQAuth);

        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
        });
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，QQ用户授权失败，请稍后再试。', $crawler->filter('div.errorMessage')->text(),'no access_token returned');
        $this->assertFalse( $session->has('qq_token') );


        // no qquser , go to register
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('access_token','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubQQAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51'));
        $stubQQAuth->expects($this->once())
            ->method('get_openid')
            ->willReturn( array('client_id' => '101163684',
                'openid' => '973F697E97A60289C8C455B1D65FF5F0' ));
        $mockQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\QQLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockQQAuth->expects($this->exactly(2))
            ->method('getQQAuth')
            ->willReturn( $stubQQAuth);
        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
        });
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect(); 
//        $new_url_expeced  = $container->get('router')->generate('qq_fist_login');
//        $this->assertEquals('/QQLogin/qqFistLogin', $new_url_expeced);
       $this->assertEquals( '/QQLogin/qqFistLogin', $client->getRequest()->getRequestUri());

        $this->assertTrue( $session->has('qq_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('qq_token'),'qq_token session is set');

        $this->assertTrue($session->has('open_id') );
        $this->assertEquals('973F697E97A60289C8C455B1D65FF5F0', $session->get('open_id'),'open_id session is set' );

        
        // has qquser, no jili user  , error message 
        // has qquser, has jili user , login
        return true;
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('access_token','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubQQAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51'));
        $stubQQAuth->expects($this->once())
            ->method('get_openid')
            ->willReturn( array('client_id' => '101163684',
                'openid' => '973F697E97A60289C8C455B1D65FF5F0' ));
        $mockQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\QQLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockQQAuth->expects($this->exactly(2))
            ->method('getQQAuth')
            ->willReturn( $stubQQAuth);
        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
        });
        

    }

    /**
     * @group issue_474
     */
    public function testqqLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_api_login');
        $this->assertEquals('/QQLogin/qqlogin', $url);
    }

    /**
     * @group issue_474
     */
    public function testqqRegisteAction()
    {
        $url = $this->container->get('router')->generate('qq_registe');
        $this->assertEquals('/QQLogin/qqRegiste', $url);
    }

    /**
     * @group issue_474
     */
   public function testqqBindAction()
    {
        $url = $this->container->get('router')->generate('qq_bind');
        $this->assertEquals('/QQLogin/qqbind', $url);
    }

    /**
     * @group issue_474
     */
    public function testqqFirstLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_fist_login');
        $this->assertEquals('/QQLogin/qqFistLogin', $url);
    }
}
