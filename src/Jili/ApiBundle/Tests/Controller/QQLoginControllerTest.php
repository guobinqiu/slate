<?php
namespace Jili\ApiBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

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

    //    // load fixtures
    //    $fixture = new LoadApiDupEmailCodeData();
    //    $fixture->setContainer($container);

    //    $loader = new Loader();
    //    $loader->addFixture($fixture);

    //    $executor->execute($loader->getFixtures());
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
     */
    public function testCallBackAction() 
    {
        $client = static::createClient();

        $url = $this->container->get('router')->generate('qq_api_callback');
        $this->assertEquals('/QQLogin/qqcallback', $url);

        $crawler =  $client->request('GET', $url, array('code'=>''));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，QQ用户授权失败，请稍后再试。', $crawler->filter('div.errorMessage')->text());

        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('access_token','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();

        $stubQQAuth->method('access_token')
            ->willReturn(array('access_token'=>'xxxx'));

        $stubQQAuth->method('get_openid')
            ->willReturn('1111');


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
//        $client->
        //
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
