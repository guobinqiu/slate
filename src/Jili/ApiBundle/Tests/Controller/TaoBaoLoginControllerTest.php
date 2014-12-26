<?php
namespace Jili\ApiBundle\Tests\Controller;
use Jili\Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadTaoBaoUserCallbackData;
use Jili\ApiBundle\DataFixtures\ORM\Services\LoadUserBindData;

class TaoBaoLoginControllerTest extends WebTestCase
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
            $fixture = new LoadTaoBaoUserCallbackData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
        } elseif($tn === 'testTaoBaoBindActionWithUser')   {
            $this->has_fixture = true;
            $fixture  = new LoadUserBindData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }elseif(in_array($tn, array('testTaoBaoRegisteActionSuccess', 'testTaoBaoRegisteActionValidation'))) {
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
     * @group issue_5366
     */
    public function testCallBackAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();

        $session = $container->get('session');
        $em = $this->em;

        $url = $this->container->get('router')->generate('taobao_login_callback');
        $this->assertEquals('/TaoBaoLogin/taobaocallback', $url);

        $crawler =  $client->request('GET', $url, array('code'=>''));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，淘宝用户授权失败，请稍后再试。', $crawler->filter('div.errorMessage')->text());

        // test no access_token
        $stubTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\TaoBaoAuth')
            ->setMethods(array('access_token_and_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubTaoBaoAuth->expects($this->once())
            ->method('access_token_and_user_info')
            ->willReturn(array('access_token'=>''));

        $mockTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\TaoBaoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockTaoBaoAuth->expects($this->once())
            ->method('getTaoBaoAuth')
            ->willReturn( $stubTaoBaoAuth);

        static::$kernel->setKernelModifier(function($kernel) use ($mockTaoBaoAuth) {
            $kernel->getContainer()->set('user_TaoBao_login', $mockTaoBaoAuth);
        });
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，淘宝用户授权失败，请稍后再试。', $crawler->filter('div.errorMessage')->text(),'no access_token returned');
        $this->assertFalse( $session->has('taobao_token') );
        $session->remove('taobao_token');
        $session->remove('open_id');
        $session->save();

        // no TaoBaouser to openid, go to register
        $stubTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\TaoBaoAuth')
            ->setMethods(array('access_token_and_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubTaoBaoAuth->expects($this->once())
            ->method('access_token_and_user_info')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51','taobao_user_id'=>"sfasfsafaf","taobao_user_nick"=>"testnick"));
        $mockTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\TaoBaoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockTaoBaoAuth->expects($this->once())
            ->method('getTaoBaoAuth')
            ->willReturn( $stubTaoBaoAuth);
        static::$kernel->setKernelModifier(function($kernel) use ($mockTaoBaoAuth) {
            $kernel->getContainer()->set('user_TaoBao_login', $mockTaoBaoAuth);
        });

        //echo $url;
        //echo $client->getRequest()->getRequestUri();exit;
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $session = $client->getRequest()->getSession();

        $this->assertTrue($session->has('open_id') );
        $crawlerNew = $client->followRedirect(); 
        $this->assertEquals( '/TaoBaoLogin/taobaoFirstLogin', $client->getRequest()->getRequestUri());
        $this->assertEquals('sfasfsafaf', $session->get('open_id'),'open_id session is set' );
        $session->remove('taobao_token');
        $session->remove('open_id');
        $session->save();
        
        // has TaoBaouser, no jili user  , error message 
        $stubTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\TaoBaoAuth')
            ->setMethods(array('access_token_and_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubTaoBaoAuth->expects($this->once())
            ->method('access_token_and_user_info')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51','taobao_user_id'=>'973E697D97F60289B8B455A1C65CC5E0'));
        $mockTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\TaoBaoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockTaoBaoAuth->expects($this->once())
            ->method('getTaoBaoAuth')
            ->willReturn( $stubTaoBaoAuth);
        static::$kernel->setKernelModifier(function($kernel) use ($mockTaoBaoAuth) {
            $kernel->getContainer()->set('user_TaoBao_login', $mockTaoBaoAuth);
        });
        
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('对不起，找不到该用户，请联系客服。', $crawler->filter('div.errorMessage')->text());
        $session = $client->getRequest()->getSession();
        $this->assertTrue( $session->has('taobao_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('taobao_token'),'TaoBao_token session is set');
        $session->remove('taobao_token');
        $session->remove('open_id');
        $session->save();

       // has TaoBaouser, has jili user , login
        $stubTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\OAuths\\TaoBaoAuth')
            ->setMethods(array('access_token_and_user_info'))
            ->disableOriginalConstructor()
            ->getMock();
        $stubTaoBaoAuth->expects($this->once())
            ->method('access_token_and_user_info')
            ->willReturn(array('access_token'=>'D8E44D85A05AA374243CFE3911365C51','taobao_user_id'=>'973F697E97A60289C8C455B1D65FF5F0',"taobao_user_nick"=>"testnick"));
        $mockTaoBaoAuth = $this->getMockBuilder('Jili\\ApiBundle\\Services\\TaoBaoLogin')
            ->disableOriginalConstructor()
            ->getMock();
        $mockTaoBaoAuth->expects($this->once())
            ->method('getTaoBaoAuth')
            ->willReturn( $stubTaoBaoAuth);
        static::$kernel->setKernelModifier(function($kernel) use ($mockTaoBaoAuth) {
            $kernel->getContainer()->set('user_TaoBao_login', $mockTaoBaoAuth);
        });
        
        $crawler =  $client->request('GET', $url, array('code'=>'0A188F5A7881938E405DA8D1E01D7765'));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawlerNew = $client->followRedirect(); 
        $this->assertEquals( '/', $client->getRequest()->getRequestUri());

        $this->assertFalse($session->has('open_id') );
        $this->assertTrue( $session->has('taobao_token') );
        $this->assertEquals('D8E44D85A05AA374243CFE3911365C51', $session->get('taobao_token'),'TaoBao_token session is set');

        $this->assertTrue( $session->has('uid') );
        $this->assertTrue( $session->has('nick') );
        $user = LoadTaoBaoUserCallbackData::$USERS[0];
        $this->assertEquals($user->getId(), $session->get('uid'),'');
        $this->assertTrue( $session->has('nick') );
        $this->assertEquals($user->getNick(), $session->get('nick'),'');

    }
}
