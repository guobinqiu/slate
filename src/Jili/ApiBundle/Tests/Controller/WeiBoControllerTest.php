<?php
namespace Jili\ApiBundle\Tests\Controller;
use Jili\Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\WeiBoUser;

class WeiBoLoginControllerTest extends WebTestCase
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
        static::$kernel = static::createKernel( array('environment'=>'test') );
        static::$kernel->boot();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $container  = static::$kernel->getContainer();

        $tn = $this->getName();
        // load fixtures
        if( in_array($tn ,array('testCallBackAction', 'testCallBackActionI','testCallBackActionII','testCallBackActionIII','testweiboRegisteActionFailure'))) {
            $this->has_fixture = true;
            $fixture = new LoadWeiBoUserCallbackData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
        } elseif(in_array($tn ,array('testweiboRegisteActionValidation', 'testweiboBindActionWithUser')))   {
            $this->has_fixture = true;
            $fixture  = new LoadWeiboUserBindData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }elseif(in_array($tn, array('testweiboRegisteActionSuccess', ))) {
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
     * @group issue636
     */
    public function testCallBackAction()
    {
        $client = $this->client;
        $container  = $client->getContainer();
        $kernel = $container->get('kernel');
        $session = $container->get('session');
        $em = $this->em;

        $url = $this->container->get('router')->generate('weibo_api_callback');
        $this->assertEquals('/WeiBoLogin/weibocallback', $url);

        $crawler =  $client->request('GET', $url, array('code'=>''));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("对不起，微博用户授权失败，请稍后再试。")')->count() > 0);
    }

    /**
     * @group issue636
     */
    public function testCallBackAction0()
    {
        $client = $this->client;
        $container  = $client->getContainer();
        $kernel = $container->get('kernel');
        $session = $container->get('session');
        $em = $this->em;
        $url = $this->container->get('router')->generate('weibo_api_callback');

        // test no access_token no openid
        $stubWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\WeiBoAuth')
            ->setMethods(array('access_token'))
            ->disableOriginalConstructor()
            ->getMock();

        $stubWeiBoAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>''));

        $mockWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\WeiBoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockWeiBoAuth->expects($this->once())
            ->method('getWeiBoAuth')
            ->willReturn( $stubWeiBoAuth);
        $container->set('user_weibo_login', $mockWeiBoAuth);
        $session->remove('weibo_token');
        $session->remove('weibo_open_id');
        $session->save();

        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("对不起，微博用户授权失败，请稍后再试。")')->count() > 0, 'no access_token returned');
        $this->assertFalse( $session->has('weibo_token') );
    }

    /**
     * @group issue636
     */
    public function testCallBackActionI ()
    {
        $client = $this->client;
        $container  = $client->getContainer();
        $kernel = $container->get('kernel');
        $session = $container->get('session');
        $em = $this->em;
        $url = $this->container->get('router')->generate('weibo_api_callback');

        $session->remove('weibo_token');
        $session->remove('weibo_open_id');
        $session->save();

        // no weibouser
        $stubWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\WeiBoAuth')
            ->setMethods(array('access_token'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubWeiBoAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51'));
        $mockWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\WeiBoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockWeiBoAuth->expects($this->exactly(1))
            ->method('getWeiBoAuth')
            ->willReturn( $stubWeiBoAuth);
        $container->set('user_weibo_login', $mockWeiBoAuth);
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue636
     */
    public function testCallBackActionIII ()
    {
       $client = $this->client;
       $container  = $client->getContainer();
       $kernel = $container->get('kernel');
        $session = $container->get('session');
        $em = $this->em;
        $session->remove('weibo_token');
        $session->remove('weibo_open_id');
        $session->save();

        $url = $this->container->get('router')->generate('weibo_api_callback');
       // has weibouser, has jili user , login
        $stubWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\WeiBoAuth')
            ->setMethods(array('access_token','get_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubWeiBoAuth->expects($this->once())
            ->method('access_token')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51','uid'=>'973F697E97A60289C8C455B1D65FF5F0'));
        $stubWeiBoAuth->expects($this->once())
            ->method('get_user_info')
            ->willReturn(array('name'=>'testname'));
        $mockWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\WeiBoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockWeiBoAuth->expects($this->exactly(2))
            ->method('getWeiBoAuth')
            ->willReturn( $stubWeiBoAuth);
        $container->set('user_weibo_login', $mockWeiBoAuth);
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect();
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());
        $this->assertTrue($session->has('weibo_open_id') );
        $this->assertTrue( $session->has('weibo_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('weibo_token'),'weibo_token session is set');

        $this->assertTrue( $session->has('uid') );

        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user = $users[0];

       $this->assertEquals($user->getId(), $session->get('uid'),'');
        $this->assertTrue( $session->has('nick') );
        $this->assertEquals($user->getNick(), $session->get('nick'),'');
    }

    /**
     * @group dev-merge-ui-qq_weibo_move_register
     */
    public function testCallBackActionForMaintenance ()
    {
        $client = $this->client;
        $container  = $client->getContainer();
        $kernel = $container->get('kernel');
        $session = $container->get('session');
        $em = $this->em;
        $session->remove('weibo_token');
        $session->remove('weibo_open_id');
        $session->save();

        $url = $this->container->get('router')->generate('weibo_api_callback');
        // has weibouser, no jili user
        $stubWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\WeiBoAuth')
        ->setMethods(array('access_token','get_user_info'))
        ->disableOriginalConstructor()
        ->getMock();
        $stubWeiBoAuth->expects($this->once())
        ->method('access_token')
        ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C52','uid'=>'973F697E97A60289C8C455B1D65FF5F2'));

        $stubWeiBoAuth->expects($this->once())
        ->method('get_user_info')
        ->willReturn(array('name'=>'testname_maintenance','profile_image_url'=>'profile_image_url_test'));
        $mockWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\WeiBoLogin')
        ->disableOriginalConstructor()
        ->getMock();
        $mockWeiBoAuth->expects($this->exactly(2))
        ->method('getWeiBoAuth')
        ->willReturn( $stubWeiBoAuth);
        $container->set('user_weibo_login', $mockWeiBoAuth);
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7766'));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect();
        $this->assertEquals( '/WeiBoLogin/maintenance', $client->getRequest()->getRequestUri());
    }

    /**
     * @group issue636
     */
    public function testweiboFirstLoginAction()
    {
        $url = $this->container->get('router')->generate('weibo_first_login');
        $this->assertEquals('/WeiBoLogin/weiboFirstLogin', $url);

        $em = $this->em;
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('weibo_open_id', '112233');
        $session->set('weibo_name', 'test11');
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $session_request  = $client->getRequest()->getSession();
        $this->assertTrue( $session_request->has('weibo_open_id'));
        $this->assertTrue( $session_request->has('weibo_token') );
        $this->assertTrue( $session_request->has('weibo_name') );

        $form_register = $crawler->filter('form[name=form1]')->form();
        $this->assertEquals('test11',$form_register['weibonickname']->getValue());
        $form_binding = $crawler->filter('form[name=form2]')->form();

        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        // 2. without openid in session
        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->remove('weibo_open_id');
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("对不起，非法操作，请在微博完成授权后再试。")')->count() > 0);
    }

    /**
     * @group issue636
     */
    public function testweiboLoginAction()
    {
        $url = $this->container->get('router')->generate('weibo_api_login');
        $this->assertEquals('/WeiBoLogin/weibologin', $url);
        $client = $this->client;
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        // 1. set session weibo_token

        $session->set('weibo_token', '111');
        $session->save();
        // 1.1 user has login , redirect
        $session->set('uid', 1);
        $session->save();
        $crawler =  $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect();
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());
    }

    /**
     * @group issue636
     */
    public function testweiboLoginActionI()
    {
        $client = $this->client;
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;

        $url = $this->container->get('router')->generate('weibo_api_login');
        // 1. set session weibo_token
        // 1.2 no user login, mock the  login_url, redirect
        $stubWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\WeiBoAuth')
            ->setMethods(array('login_url'))
            ->disableOriginalConstructor()
            ->getMock();

        //        $uri_by_weibo = 'https://graph.weibo.com/oauth2.0/authorize?response_type=code&client_id=101163684&redirect_uri=http://testgroup.91jili.com/weibologin&scope=get_user_info';
        $uri_by_weibo = 'https://api.weibo.com/oauth2/authorize?client_id=693159936&redirect_uri=http%3A%2F%2Fwww.91jili.com%2FWeiBoLogin%2Fweibocallback&response_type=code';
        $stubWeiBoAuth->expects($this->once())
            ->method('login_url')
            ->willReturn($uri_by_weibo);

        $mockWeiBoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\WeiBoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockWeiBoAuth->expects($this->exactly(1))
            ->method('getWeiBoAuth')
            ->willReturn( $stubWeiBoAuth);

        $container->set('user_weibo_login', $mockWeiBoAuth);
        $session->remove('uid');
        $session->set('weibo_token', '222');
        $session->save();

        $crawler =  $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse() instanceof RedirectResponse);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawlerNew = $client->followRedirect();
        $this->assertEquals($uri_by_weibo,$client->getHistory()->current()->getUri(),
            'a sub-requst should be the target url ');
    }

    /**
     * @group issue636
     * @group issue_722
     */
    public function testweiboRegisteActionValidation()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before?
        // 注册成功，登陆并跳转主页
        $client = $this->client;
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        $url = $container->get('router')->generate('weibo_registe');
        $this->assertEquals('/WeiBoLogin/weiboRegiste', $url);
        $session->set('weibo_open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('weibo_name', 'test');
        $session->save();
        $this->assertTrue( $session->has('weibo_open_id') );
        $this->assertTrue( $session->has('weibo_token') );
        $this->assertTrue( $session->has('weibo_name') );
        $url_first_login = $container->get('router')->generate('weibo_first_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register = $crawler->filter('form[name=form1]')->form();

        $form_register['weibo_user_regist[email]'] = '';
        $form_register['pwd'] = '123123';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));

        $form_register['weibo_user_regist[email]'] = 'hwyf1229@163..com';
        $form_register['pwd'] = '123123';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));

        $form_register['weibo_user_regist[email]'] = 'm18713336976@.163.com';
        $form_register['pwd'] = '123123';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));

        $form_register['weibo_user_regist[email]'] = '@A';
        $form_register['pwd'] = '123123';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));

        // again
        $form_register['weibo_user_regist[email]'] = 'AAA';
        $form_register['pwd'] = '';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));

        $form_register['weibo_user_regist[email]'] = 'alicenima @voyagegroup.com.cn';
        $form_register['pwd'] = '123456';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请填写正确的邮箱或密码!', trim($crawler->filter('#regist_emailError')->text()));
    }

    /**
     * @group issue636
     */
    public function testweiboRegisteActionFailure()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before?
        // 注册成功，登陆并跳转主页
        $client = $this->client;
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;

        // [] 注册失败 an empty  open_id in session. check insert data as... before?
        $session->set('weibo_open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('weibo_name', 'test');
        $session->save();

        $this->assertTrue( $session->has('weibo_open_id') );
        $this->assertTrue( $session->has('weibo_token') );
        $this->assertTrue( $session->has('weibo_name') );
        $url_first_login = $container->get('router')->generate('weibo_first_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register = $crawler->filter('form[name=form1]')->form();
        $form_register['weibo_user_regist[email]'] = 'alice32@11.com';
        $form_register['pwd'] = '123123';
        $session->remove('weibo_open_id');
        $session->save();
        $this->assertFalse( $session->has('weibo_open_id') );
        // submit that form
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("对不起，微博用户注册失败，请稍后再试。")')->count() > 0);

        $form_register['weibo_user_regist[email]'] = 'alice32@gmail.com';
        $form_register['pwd'] = '123123';
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('此账号已存在，请点击下方【已有91问问账号】按钮进行绑定!', trim($crawler->filter('#regist_emailError')->text()));
    }

    /**
     * @group issue636
     */
    public function testweiboRegisteActionSuccess()
    {
        //form valid  验证不通过
        // 注册失败  check insert data as... before?
        // 注册成功，登陆并跳转主页
        $client = $this->client;
        $container  = $client->getContainer();
        $session = $container->get('session');

        $em = $this->em;
        $url = $container->get('router')->generate('weibo_registe');
        // [] 注册成功，登陆并跳转主页
        $session->set('weibo_open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('weibo_name', 'test');
        $session->save();
        $this->assertTrue( $session->has('weibo_open_id') );
        $this->assertTrue( $session->has('weibo_token') );
        $this->assertTrue( $session->has('weibo_name') );
        $url_first_login = $container->get('router')->generate('weibo_first_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form_register = $crawler->filter('form[name=form1]')->form();
        $form_register['weibo_user_regist[email]'] = 'alice32@aa.com';
        $form_register['pwd'] = '123123';
        $form_register['weibonickname'] = 'test';

        // submit that form
        $crawler = $client->submit($form_register);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        // check the result
        $records_user = $em->getRepository('JiliApiBundle:User')->findBy(array (
            'email' => 'alice32@aa.com',
            'nick'=>'weibo_test'
        ));
        //var_dump($records);
        $this->assertEquals(1, count($records_user), 'more then 1 record in user ');
        $records_weibouser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneByOpenId('973F697E97A60289C8C455B1D65FF5F0');
        $this->assertEquals(1, count($records_weibouser), 'more then 1 record in weibouser ');
        $weibo_user_actual = $this->em->getRepository('JiliApiBundle:WeiBoUser')->findOneBy(array(
            'userId'=> $records_user[0]->getId(),
            'openId'=>'973F697E97A60289C8C455B1D65FF5F0'
        ));
        $this->assertNotNull($weibo_user_actual,'check insert weibo_user');
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\WeiBoUser',$weibo_user_actual,'check insert weibo_user');
    }

    /**
     * @group issue636
     */
   public function testweiboBindActionWithUser()
   {
//        $code == 'ok'
 //           $result = $user_bind->weibo_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
       // []with wrong user
       // corret user
       $client = $this->client;
       $container  = $client->getContainer();
       $session = $container->get('session');
       $em = $this->em;
       $url = $this->container->get('router')->generate('weibo_bind');
       $this->assertEquals('/WeiBoLogin/weibobind', $url);
        $session->set('weibo_open_id', '973F697E97A60289C8C455B1D65FF5F0' );
        $session->set('weibo_token', 'D8E44D85A05AA374243CFE3911365C51');
        $session->set('weibo_name', 'testname');
        $session->save();
        $this->assertTrue( $session->has('weibo_open_id') );
        $this->assertTrue( $session->has('weibo_token') );
        $url_first_login = $container->get('router')->generate('weibo_first_login');
        $crawler =  $client->request('GET', $url_first_login );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user = $users[0];

        $form_binding = $crawler->filter('form[name=form2]')->form();
        $form_binding['jili_email'] = $user->getEmail();
        $form_binding['jili_pwd'] = '111111';
        $client->submit($form_binding);
        $session0 = $client->getRequest()->getSession();
        $this->assertTrue($session->has('uid'));
        $this->assertEquals($user->getId(),$session->get('uid'));

        $weibo_user_actual = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneBy(array('userId'=> $user->getId(), 'openId'=>'973F697E97A60289C8C455B1D65FF5F0' ));
        $this->assertNotNull($weibo_user_actual);
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\WeiBoUser',$weibo_user_actual,'check insert weibo_user');
   }

   /**
    * @group dev-merge-ui-qq_weibo_move_register
    */
   public function testMaintenanceAction()
   {
       $client = static::createClient();
       $container = $client->getContainer();

       $url = $container->get('router')->generate('weibo_maintenance');
       $crawler = $client->request('GET', $url);
       $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadWeiBoUserCallbackData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        //load data for testing .
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $weiboUser = new WeiBoUser();
        $weiboUser->setUserId($user->getId());
        $weiboUser->setOpenId('973F697E97A60289C8C455B1D65FF5F0');
        $manager->persist($weiboUser);
        $manager->flush();

        // weibo_user  without jili_user
        $weiboUser = new WeiBoUser();
        $weiboUser->setUserId(99);
        $weiboUser->setOpenId('973E697D97F60289B8B455A1C65CC5E1');
        $manager->persist($weiboUser);
        $manager->flush();
    }
}

class LoadWeiboUserBindData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('alic32');
        $user->setEmail('alice.nima@voyagegroup.com.cn');
        $user->setIsEmailConfirmed(1);
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
    }
}
