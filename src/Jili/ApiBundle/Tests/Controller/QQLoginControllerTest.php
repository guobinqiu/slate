<?php
namespace Jili\ApiBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $session->remove('qq_token');
        $session->remove('open_id');
        $session->save();

        // no qquser to openid, go to register
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
                'openid' => '973F697E97A60289C8C455B1D65FAAAA' ));
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

        $session = $client->getRequest()->getSession();

        $this->assertTrue($session->has('open_id') );
        $crawlerNew = $client->followRedirect(); 
        $this->assertEquals( '/QQLogin/qqFistLogin', $client->getRequest()->getRequestUri());
        $this->assertEquals('973F697E97A60289C8C455B1D65FAAAA', $session->get('open_id'),'open_id session is set' );
        $session->remove('qq_token');
        $session->remove('open_id');
        $session->save();
        
        // has qquser, no jili user  , error message 
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
                'openid' => '973E697D97F60289B8B455A1C65CC5E0' ));
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，找不到该用户，请联系客服。', $crawler->filter('div.errorMessage')->text());
        $session = $client->getRequest()->getSession();
        $this->assertTrue( $session->has('qq_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('qq_token'),'qq_token session is set');

        $session->remove('qq_token');
        $session->remove('open_id');
        $session->save();

       // has qquser, has jili user , login
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
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());

        $this->assertFalse($session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('qq_token'),'qq_token session is set');

        $this->assertTrue( $session->has('uid') );
        $user = LoadQQUserCallbackData::$USERS[0];
        $this->assertEquals($user->getId(), $session->get('uid'),'');
        $this->assertTrue( $session->has('nick') );
        $this->assertEquals($user->getNick(), $session->get('nick'),'');

    }

    /**
     * @group issue_474
     */
    public function testqqLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_api_login');
        $this->assertEquals('/QQLogin/qqlogin', $url);
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        // 1. set session qq_token
        
        $session->set('qq_token', '111');
        $session->save();

        // 1.1 user has login , redirect
        $session->set('uid', 1);
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect(); 
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());

        // 1.2 no user login, mock the  login_url, redirect
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('login_url'))
            ->disableOriginalConstructor()
            ->getMock();

        $uri_by_qq = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=101163684&redirect_uri=http://testgroup.91jili.com/qqlogin&scope=get_user_info';
        $stubQQAuth->expects($this->once())
            ->method('login_url')
            ->willReturn($uri_by_qq);

        $mockQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\QQLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockQQAuth->expects($this->exactly(1))
            ->method('getQQAuth')
            ->willReturn( $stubQQAuth);

        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
        });

        $session->remove('uid');
        $session->set('qq_token', '222');
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse() instanceof RedirectResponse);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawlerNew = $client->followRedirect(); 
    //    $this->assertEquals( '/',$client->getRequest()->getRequestUri(),' the primary request uri is not changed');;
        $this->assertEquals($uri_by_qq,$client->getHistory()->current()->getUri(),
            'a sub-requst should be the target url ');
        // /oauth2.0/authorize?client_id=101163684&redirect_uri=www.91jili.com&response_type=code&scope=get_user_info
    }

    /**
     * @group issue_474
     */
    public function testqqRegisteAction()
    {
        $url = $this->container->get('router')->generate('qq_registe');
        $this->assertEquals('/QQLogin/qqRegiste', $url);

        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        // form post
        // form valid 
        // check insert data as... before
                //注册失败
                //注册成功，登陆并跳转主页
            //验证不通过

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
     * @group debug
     */
    public function testqqFirstLoginAction()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $session = $container->get('session');

        $em = $this->em;

        $url = $this->container->get('router')->generate('qq_fist_login');
        $this->assertEquals('/QQLogin/qqFistLogin', $url);

        // . request without openid in session
        // mock the 获取登录用户open id 
        //$qq_oid = $qq_auth->get_openid();
        //$result = $qq_auth->get_user_info($openid);
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_openid', 'get_user_info'))
            ->disableOriginalConstructor()
            ->getMock();

        $openid_qq = array('client_id' => '101163684',
                'openid' => '973F697E97A60289C8C455B1D65FF5F0' );
        $stubQQAuth->expects($this->once())
            ->method('get_openid')
            ->willReturn($openid_qq );

        $user_info_qq =<<<EOD
{ "ret": 0, "msg": "", "is_lost":0, "nickname": "Jin", "gender": "男", "province": "上海", "city": "杨浦", "year": "1985", "figureurl": "http:\/\/qzapp.qlogo.cn\/qzapp\/101155200\/905EF40580E05666FFF1675F9E38E9B5\/30", "figureurl_1": "http:\/\/qzapp.qlogo.cn\/qzapp\/101155200\/905EF40580E05666FFF1675F9E38E9B5\/50", "figureurl_2": "http:\/\/qzapp.qlogo.cn\/qzapp\/101155200\/905EF40580E05666FFF1675F9E38E9B5\/100", "figureurl_qq_1": "http:\/\/q.qlogo.cn\/qqapp\/101155200\/905EF40580E05666FFF1675F9E38E9B5\/40", "figureurl_qq_2": "http:\/\/q.qlogo.cn\/qqapp\/101155200\/905EF40580E05666FFF1675F9E38E9B5\/100", "is_yellow_vip": "0", "vip": "0", "yellow_vip_level": "0", "level": "0", "is_yellow_year_vip": "0" } 
EOD;

        $stubQQAuth->expects($this->once())
            ->method('get_user_info')
            ->willReturn( json_decode($user_info_qq,true));

        $mockQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\QQLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockQQAuth->expects($this->once())
            ->method('getQQAuth')
            ->willReturn( $stubQQAuth);

        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
        });

        // what if no qq_token session is set??
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->remove('openid');
        $session->save();
        $this->assertTrue( $session->has('qq_token') );
        $this->assertFalse( $session->has('openid') );
        $crawler =  $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $session_request  = $client->getRequest()->getSession();
        $this->assertTrue( $session_request->has('open_id'));
        $this->assertEquals($openid_qq['openid'], $session_request->get('open_id'), 'the open id');
        echo $client->getResponse()->getContent(), PHP_EOL;

        //                                <input type="hidden" id="qqregist__token" name="qqregist[_token]" value="7ed7cbcce90ae5ef39bab53b259d091f0e841426" />
        //
//        $form = $crawler->selectButton('validate')->form();
// 2. with session


    }
}
