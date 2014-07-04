<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        $this->em  = $em;
#        ob_start();
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

    private function buildToken( $user , $secret) {
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

        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);

        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

// render password reset page
        print 'Render password reset page'.PHP_EOL;
        $passwordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findOneBy(array('userId'=>$user->getId(), 'isAvailable'=>1));

        if(! $passwordCode) {
            echo  ' code not found!',PHP_EOL;
            return false;
        }

        $code= $passwordCode->getCode();
        $url = $container->get('router')->generate('_user_forgetPass',array('code'=>$code,'id'=>$user->getId() ),true);

        print $url. PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('but')->form();

        // set some values
        print 'Set some values'.PHP_EOL;
        $form['pwd'] = 'aaaaaa';
        $form['que_pwd'] = 'aaaaaa';

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
     * @group user_reg
     */
    public function testReSend()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $client->request('GET', '/user/reset', array (
                'id'=>"1057699",
                'code'=>'testcode100',
                'nick'=>'',
                'email'=>'zhangmm@voyagegroup.com.cn'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());
    }

}
