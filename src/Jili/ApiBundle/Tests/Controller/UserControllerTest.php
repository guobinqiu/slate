<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\ApiBundle\Controller\UserController;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSetPasswordCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserResetPasswordCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserReSendCodeData;


class UserControllerTest extends WebTestCase
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
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container  = static::$kernel->getContainer();


        $tn = $this->getName();
        if (in_array( $tn, array('testResetPasswordAction','testReSend'))) {
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            // load fixtures
            if (in_array( $tn, array('testResetPasswordAction'))) {
                $fixture = new LoadUserResetPasswordCodeData();
                $fixture->setContainer($container);
                $loader = new Loader();
                $loader->addFixture($fixture);
                $executor->execute($loader->getFixtures());
            } else if (in_array( $tn, array('testReSend'))) {
                $fixture = new LoadUserReSendCodeData();
                $fixture->setContainer($container);
                $loader = new Loader();
                $loader->addFixture($fixture);
                $executor->execute($loader->getFixtures());
            }
        }
        $this->container = $container;
        $this->em  = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
#        header_remove();
        parent::tearDown();
       $this->em->close();
    }

    /**
     * @group user
     * @group login
     */
    public function testLogoutWithTokenAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');
        // login
        $url = $container->get('router')->generate('_login', array(), true);
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $query = array('email'=> 'chiangtor@gmail.com');
        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }
        $uid = $user->getId();
        unset($user);
        // clean token
        $em->getRepository('JiliApiBundle:User')->cleanToken($uid);
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);//$query['email']);

        $this->assertEmpty($user->getToken());
        unset($user);

        //login
        $form = $crawler->selectButton('loginSubmit')->form();
        $form['email'] = $query['email'];
        $form['pwd'] = 'aaaaaa';
        $form['remember_me']->tick();

        $client->submit($form);


        $secret = $container->getParameter('secret');
        $token = $this->buildToken( array('email'=> $query['email'], 'pwd'=> 'aaaaaa'), $secret);
        $user =$container->get('doctrine')->getEntityManager()->getRepository('JiliApiBundle:User')->find($uid);
        $this->assertEquals($token, $user->getToken());
        unset($user);

        //logout
        $url_logout = $router->generate('_user_logout' , array(), true);
        echo $url_logout,PHP_EOL;
        $crawler = $client->request('GET', $url_logout ) ;

        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $this->assertEmpty($user->getToken());
        unset($user);
    }
    /**
     * @group user
     * @group login
     */
    public function testLogoutAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');
        // logout
        $url_logout = $router->generate('_user_logout' , array(), true);
        echo $url_logout,PHP_EOL;
        $crawler = $client->request('GET', $url_logout ) ;

        $em = $this->em;
        $query = array('email'=> 'chiangtor@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        $this->assertEquals(302, $client->getResponse()->getStatusCode() );
        $session = $container->get('session');
        $this->assertFalse( $session->has('uid'));
        $this->assertFalse( $session->has('nick'));

        $cookies  = $client->getCookieJar() ;
        $this->assertEmpty(  $cookies->get('jili_uid' ,'/'));
        $this->assertEmpty(  $cookies->get('jili_nick' ,'/'));
        $this->assertEmpty(  $cookies->get('jili_rememberme' ,'/'));



    }
    /**
     * @group user
     * @group login
     */
    public function testLoginRemeberMeAction()
    {
        //todo assert the session config. reduce the configuration on gc_lifetime.
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');
        $session = array(
            'gc_maxlifetime'=>  ini_get('session.gc_maxlifetime')
        );

        $em = $this->em;
        $query = array('email'=> 'chiangtor@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        $url = $container->get('router')->generate('_login', array(), true);
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('loginSubmit')->form();
        $form['email'] = $query['email'];
        $form['pwd'] = 'aaaaaa';
        $form['remember_me']->tick();

        $client->submit($form);

        $this->assertEquals(301, $client->getResponse()->getStatusCode() );

        $session = $container->get('session');

        $this->assertTrue( $session->has('uid'));
        $this->assertEquals($user->getId(), $session->get('uid'));

        $cookies  = $client->getCookieJar() ;

        //$this->assertEquals( $user->getId(), $cookies->get('jili_uid' ,'/')->getRawValue());

        $secret = $container->getParameter('secret');
        $token = $this->buildToken( array('email'=> $query['email'], 'pwd'=> 'aaaaaa'), $secret);

        $this->assertEquals( $token, $cookies->get('jili_rememberme' ,'/')->getRawValue());

        $this->assertEmpty(  $cookies->get('jili_uid' ,'/'));
        $this->assertEmpty(  $cookies->get('jili_nick' ,'/'));
    }

    private function buildToken($user , $secret)
    {
        $token = implode('|',$user) .$secret;//.$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }

    /**
     * @group user
     */
    public function testResetPasswordAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

// reset email
        $query = array('email'=> 'alice.nima@gmail.com');
        $url = $container->get('router')->generate('_user_reset', $query ) ;
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());
$user = LoadUserResetPasswordCodeData::$ROWS[0];
//        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
//        if(! $user) {
//            echo 'bad email:',$query['email'], PHP_EOL;
//            return false;
//        }

// render password reset page
        print 'Render password reset page'.PHP_EOL;
//        $passwordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneBy(array('userId'=>$user->getId(), 'isAvailable'=>1));
//
//        if(! $passwordCode) {
//            echo  ' code not found!',PHP_EOL;
//            return false;
//        }
$passwordCode =LoadUserResetPasswordCodeData::$SET_PASSWORD_CODE[0]; 
        $code= $passwordCode->getCode();
        $url = $container->get('router')->generate('_user_forgetPass',array('code'=>$code,'id'=>$user->getId() ),true);

        print $url. PHP_EOL;
        $client->request('GET', $url ) ;
        $this->assertEquals(302, $client->getResponse()->getStatusCode() , 'GET forget pass url status check' );
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('but')->form();

        // set some values
        print 'Set some values'.PHP_EOL;
        #$form['pwd'] = 'aaaaaa';
        #$form['que_pwd'] = 'aaaaaa';
        $form['password[first]'] ->setValue( 'aaaaaa');
        $form['password[second]'] ->setValue( 'aaaaaa');
        $form['agreement']->tick() ;

        // submit the form
        print 'Submit the form'.PHP_EOL;
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
    }

#    public function testFastLoginAction()
#    {
#        $client = static::createClient();
#        $container = $client->getContainer();
#        $logger= $container->get('logger');
#
#        $query = array('email'=> 'alice.nima@gmail.com', 'pwd'=>'aaaaaa' );
#        $url = $container->get('router')->generate('_default_fastLogin', $query ) ;
#        // $crawler = $client->request('GET', '/hello/Fabien');
#        echo $url, PHP_EOL;
#
#        $client->request('POST', $url ) ;
#        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
#        $this->assertEquals('1', $client->getResponse()->getContent());
#
#        $this->assertEquals('0', '0');
#        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
#    }
    /**
     * @group user-password
     * @group issue_381
     */
    public function testForgetPassAction()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');
        // purge tables;
        $purger = new ORMPurger($em);

        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserSetPasswordCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $uid = LoadUserSetPasswordCodeData::$USER[0]->getId();
        $code =  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0]->getCode();

        $query = array( 'code'=> $code, 'id'=> $uid );

        $url = $container->get('router')->generate('_user_forgetPass', $query ) ;

        $url_expected = '/user/forgetPass/'. $code. '/'. $uid;
        $this->assertEquals($url_expected, $url);

        $crawler =$client->request('GET', $url ) ;
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();

        //  check the redirected url.
        $url_expected = '/user/activate/'. $code. '/'. $uid;
        $this->assertEquals( $url_expected, $client->getRequest()->getRequestUri());

    }
    /**
     * @group user-password
     * @group issue_381
     */
    public function testPasswordAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserSetPasswordCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $uid = LoadUserSetPasswordCodeData::$USER[0]->getId();
        $code =  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0]->getCode();

        $query = array( 'token'=> $code, 'uid'=> $uid );

        //todo: add https
        $url = $container->get('router')->generate('_user_signup_activate', $query ) ;
        $url_expected = '/user/activate/'. $code. '/'. $uid;
        $this->assertEquals($url_expected, $url);

        $crawler =$client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('创建账号')->form();

        $form['password[first]'] ->setValue( 'qwe123');
        $form['password[second]'] ->setValue( 'qwe123');
        $form['agreement']->tick() ;
        unset($form['but']); //NOTICE, this is an extra field.

        $client->submit($form );
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();

        //  check the redirected url.
        $url_expected = $container->get('router')->generate('_user_regSuccess') ;
        $this->assertEquals( $url_expected, $client->getRequest()->getRequestUri());

        //  check session messages
        $this->assertEquals('恭喜，密码设置成功！', $crawler->filter('h2')->text());

    }
    /**
     * @group user_reg
     */
    public function testReSend()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $user = LoadUserReSendCodeData::$ROWS[0];
        $client->request('GET', '/user/reSend', array (
                'id'=>$user->getId(),
                'code'=>'testcode100',
                'nick'=>'',
                'email'=>$user->getEmail()
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());
    }
    
    public function testRegAction() 
    {
        $client = static::createClient(array(), array('HTTP_USER_AGENT'=>'symonfy/2.0' ,'REMOTE_ADDR'=>'121.199.27.128') );
        $container = $client->getContainer();
        $router = $container->get('router');
        $em = $this->em;

        $url = $container->get('router')->generate('_user_reg', array(), false);

        $this->assertRegExp('/^https:\/\/.*\/user\/reg$/', $url, ' /user/reg url ');
        $crawler = $client->request('GET', $url  ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() ,'get the register page return 200');

        $session = $container->get('session'); 
        $captcha = $session->get('phrase');
        $phrase = $captcha ['phrase'] ;
        $email = 'alice.nima@gmail.com';

        $form = $crawler->filter('form[name=form1]')->form();
        $form['email']->setValue( $email );
        $form['nick']->setValue( 'alice32');
        $form['captcha']->setValue( $phrase );

        $crawler = $client->submit($form );

        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email );
        $this->assertEquals('symonfy/2.0',$user->getCreatedUserAgent(), 'user_agent should be symfony/2.0');
        $this->assertEquals('121.199.27.128',$user->getCreatedRemoteAddr(), 'client ip when reg should be 121.199.27.128');
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

    }
}
