<?php
namespace Jili\ApiBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadQQUserCallbackData;
use Jili\ApiBundle\DataFixtures\ORM\Services\LoadUserBindData;

class QQLoginControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $has_fixture;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->has_fixture = false;
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container  = static::$kernel->getContainer();

        $tn = $this->getName();
        // load fixtures
        if( $tn === 'testCallBackAction') {
            $this->has_fixture = true;
            $fixture = new LoadQQUserCallbackData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
        } elseif($tn === 'testqqBindActionWithUser')   {
            $this->has_fixture = true;
            $fixture  = new LoadUserBindData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }elseif(in_array($tn, array('testqqRegisteActionSuccess', 'testqqRegisteActionValidation'))) {
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();
        }

        if($this->has_fixture) {
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();
            $executor->execute($loader->getFixtures());
        }


        $this->container = $container;
        $this->em  = $em;
        $this->client = static::CreateClient();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        if( $this->has_fixture){
            $this->em->close();
        }
        $this->client = null;
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
    public function testqqFirstLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_fist_login');
        $this->assertEquals('/QQLogin/qqFistLogin', $url);

        $em = $this->em;
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        // 1. request without openid in session
        // mock the 获取登录用户open id 
        //$qq_oid = $qq_auth->get_openid();
        //$result = $qq_auth->get_user_info($openid);
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_openid', 'get_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $openid_qq = array('client_id' => '101163684','openid' => '973F697E97A60289C8C455B1D65FF5F0' );
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

        // what if no qq_token session is set??
        $client->getContainer()->set('user_qq_login', $mockQQAuth);
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->remove('open_id');
        $session->save();

        $this->assertTrue( $session->has('qq_token') );
        $this->assertFalse( $session->has('open_id') );
        $crawler =  $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $session_request  = $client->getRequest()->getSession();
        $this->assertTrue( $session_request->has('open_id'));
        $this->assertEquals($openid_qq['openid'], $session_request->get('open_id'), 'the open id');

//  echo $client->getResponse()->getContent(), PHP_EOL;
// <input type="hidden" id="qqregist__token" name="qqregist[_token]" value="7ed7cbcce90ae5ef39bab53b259d091f0e841426" />
        $form_register  = $crawler->selectButton('register')->form();
        $this->assertEquals('Jin',$form_register['qqnickname']->getValue());
        $this->assertEquals('男',$form_register['sex']->getValue());
        $form_binding  = $crawler->selectButton('binding')->form();
        
        
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        // 2. with session
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array( 'get_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
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
//        static::$kernel->setKernelModifier(function($kernel) use ($mockQQAuth) {
//            $kernel->getContainer()->set('user_qq_login', $mockQQAuth);
//        });
        $client->getContainer()->set('user_qq_login', $mockQQAuth);

        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('open_id','973F697E97A60289C8C455B1D65FF5F9' );
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $session_request  = $client->getRequest()->getSession();

        $form_register0  = $crawler->selectButton('register')->form();
        $this->assertEquals('Jin',$form_register0['qqnickname']->getValue());
        $this->assertEquals('男',$form_register0['sex']->getValue());

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
     * @group debug 
     */
    public function testqqRegisteActionValidation()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before? 
        // 注册成功，登陆并跳转主页
        $client = $this->client; 
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        $url = $container->get('router')->generate('qq_registe');
        $this->assertEquals('/QQLogin/qqRegiste', $url);

        // []form valid  验证不通过: invalid token or empty email_id
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_user_info','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
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
        $container->set('user_qq_login', $mockQQAuth);
        $session->set('open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->save();
        $this->assertTrue( $session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $url_first_login = $container->get('router')->generate('qq_fist_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register  = $crawler->selectButton('register')->form();
        $form_register['qqregist[email_id]'] = '@A';
        $form_register['pwd'] = '123123';

        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('邮箱地址格式不正确', trim($crawler->filter('#regist_emailError')->text()));

//        // again
//        $form_register['qqregist[email_id]'] = 'A@';
//        $form_register['pwd'] = '123123';
//
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('邮箱地址格式不正确', trim($crawler->filter('#regist_emailError')->text()));

    }

    /**
     * @group issue_474
     */
    public function testqqRegisteActionFailure()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before? 
        // 注册成功，登陆并跳转主页
        $client = $this->client; 
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;

        // [] 注册失败 an empty  open_id in session. check insert data as... before? 
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_user_info','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
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
        $container->set('user_qq_login', $mockQQAuth);
        $session->set('open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->save();

        $this->assertTrue( $session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $url_first_login = $container->get('router')->generate('qq_fist_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register  = $crawler->selectButton('register')->form();
        $form_register['qqregist[email_id]'] = 'alice32';
        $form_register['pwd'] = '123123'; 
        //$session->set('open_id','D8E44D85A05AA374243CFE3911365C51');
        $session->remove('open_id');
        $session->save();
        $this->assertFalse( $session->has('open_id') );
        // submit that form
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       $this->assertEquals( '对不起，QQ用户注册失败，请稍后再试。', $crawler->filter('div.errorMessage')->text()); 
    }

    /**
     * @group issue_474
     */
    public function testqqRegisteActionSuccess()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before? 
        // 注册成功，登陆并跳转主页
        $client = $this->client; 
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        $url = $container->get('router')->generate('qq_registe');
        // [] 注册成功，登陆并跳转主页
        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_user_info','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
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
        $container->set('user_qq_login', $mockQQAuth);
        $session->set('open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->save();
        $this->assertTrue( $session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $url_first_login = $container->get('router')->generate('qq_fist_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register  = $crawler->selectButton('register')->form();
        $form_register['qqregist[email_id]'] = 'alice32';
        $form_register['pwd'] = '123123';

        // submit that form
        $crawler = $client->submit($form_register);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());

        // check the result
        $user_actual = $this->em->getRepository('JiliApiBundle:User')->findOneBy( array(
            'email'=>'alice32@qq.com',
            'nick'=>'QQJin'
        ));
        $this->assertNotNull($user_actual,'check insert user');
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\User', $user_actual,'check insert user');
        $qq_user_actual = $this->em->getRepository('JiliApiBundle:QQUser')->findOneBy(array(
            'userId'=> $user_actual->getId(),
            'openId'=>'973F697E97A60289C8C455B1D65FF5F0'
        ));
        $this->assertNotNull($qq_user_actual,'check insert qq_user');
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\QQUser',$qq_user_actual,'check insert qq_user');
    }

    /**
     * @group issue_474
     */
   public function testqqBindActionWithUser()
   {
//        $code == 'ok' 
 //           $result = $user_bind->qq_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
       // []with wrong user
       // corret user
       $client = $this->client;
       $container  = $client->getContainer();
       $session = $container->get('session');
       $em = $this->em;
       $url = $this->container->get('router')->generate('qq_bind');
       $this->assertEquals('/QQLogin/qqbind', $url);

        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_user_info','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
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
        $container->set('user_qq_login', $mockQQAuth);
        $session->set('open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->save();
        $this->assertTrue( $session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $url_first_login = $container->get('router')->generate('qq_fist_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $user = LoadUserBindData::$USERS[0];
        $form_binding = $crawler->selectButton('binding')->form();
        $form_binding['jili_email'] = $user->getEmail();
        $form_binding['jili_pwd'] = '111111';
        $client->submit($form_binding);
        $session0 = $client->getRequest()->getSession();
        $this->assertTrue($session->has('uid'));
        $this->assertEquals($user->getId(),$session->get('uid'));

        $qq_user_actual = $em->getRepository('JiliApiBundle:QQUser')->findOneBy(array('userId'=> $user->getId(), 'openId'=>'973F697E97A60289C8C455B1D65FF5F0' ));
        $this->assertNotNull($qq_user_actual);
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\QQUser',$qq_user_actual,'check insert qq_user');
   }

    /**
     * @group issue_474
     */
   public function testqqBindActionWithBadUser()
   {
       $client = $this->client;
       $container  = $client->getContainer();
       $session = $container->get('session');
       $em = $this->em;
       $url = $this->container->get('router')->generate('qq_bind');
       $this->assertEquals('/QQLogin/qqbind', $url);

//        $code == 'ok' 
 //           $result = $user_bind->qq_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
       // []with wrong user
       // corret user

        $stubQQAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\QQAuth')
            ->setMethods(array('get_user_info','get_openid'))
            ->disableOriginalConstructor()
            ->getMock();
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
        $container->set('user_qq_login', $mockQQAuth);
        $session->set('open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('qq_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->save();
        $this->assertTrue( $session->has('open_id') );
        $this->assertTrue( $session->has('qq_token') );
        $url_first_login = $container->get('router')->generate('qq_fist_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form_binding = $crawler->selectButton('binding')->form();
        $form_binding['jili_email'] = 'xxxzzz@voyagegroup.com';
        $form_binding['jili_pwd'] = '111222';
        $crawler = $client->submit($form_binding);
        $session0 = $client->getRequest()->getSession();
        $this->assertFalse($session->has('uid'));
        $this->assertEquals('邮箱地址或密码输入错误', trim($crawler->filter('#bind_emailError')->text()));
   }
}
