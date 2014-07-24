<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadLandingWenwenCodeData;

class DefaultControllerTest extends WebTestCase
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
     * @group user
     */
    public function testAdLoginAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $query = array('email'=> 'alice.nima@gmail.com');
        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        $this->assertEquals(1, count($user));

        $url = $router->generate('_default_ad_login',array(), true);
        $post =  array( 'email'=>$query['email'] , 'pwd'=> 'aaaaaa') ;

        echo $url, PHP_EOL;
        $crawler = $client->request('POST', $url, $post) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'ad login in '  );
        $this->assertEquals('ok', $client->getResponse()->getContent());
        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));
        $this->assertEquals($user->getId(), $session->get('uid'));
        //
        echo $url, PHP_EOL;
        $post['remember_me'] ='1';
        $crawler = $client->request('POST', $url,$post);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'ad login in '  );
        $this->assertEquals('ok', $client->getResponse()->getContent());
        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));
        $this->assertEquals($user->getId(), $session->get('uid'));

        $cookies  = $client->getCookieJar() ;

        $secret = $container->getParameter('secret');
        $token = $this->buildToken( array('email'=> $query['email'], 'pwd'=> 'aaaaaa'), $secret);

        $this->assertEquals( $token, $cookies->get('jili_rememberme' ,'/')->getRawValue());

        #$this->assertEquals( $user->getId(), $cookies->get('jili_uid' ,'/')->getRawValue());
        #$this->assertEquals( $user->getNick(), $cookies->get('jili_nick' ,'/')->getRawValue());
    }
    /**
     * landingAction with not exists: wenwen code exists email
     * @group debug
     * @group issue_396  
     * @group signup_trace 
     */
    public function testLandingWithSignUpTrace()
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
        $fixture = new LoadLandingWenwenCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $user = LoadLandingWenwenCodeData::$USER[0];
        $wenwenUserToken = LoadLandingWenwenCodeData::$WENWEN_USER_TOKEN[0];
        
        // add session
        $session = $container->get('session');
        $session->set('id', '1234567890');

        // add cookie
        $time =time();
        $cookie = new Cookie('source_route', 'baidu_parntera', time() + 60, '/', null, false, false);
        $client->getCookieJar()->set($cookie);

        $cookie = new Cookie('pv', hash( 'ripemd160','baidu_partnera'. $time), time() + 60, '/', null, false, false);
        $client->getCookieJar()->set($cookie);

        $cookie = new Cookie('pv_unique', hash('md5','baidu_partnera'. $time), time() + 60, '/', null, false, false);
        $client->getCookieJar()->set($cookie);

        // build query with add spm without token;
        $spm = 'baidu_partnera'; 
//        $secret_token= $wenwenUserToken->getToken(); 
//        $url = $container->get('router')->generate('_default_landing', array('secret_token'=>$secret_token, 'spm'=> $spm));
        $url = $container->get('router')->generate('_default_landing', array( 'spm'=> $spm));

        // follow to the redirect
        $client->request('GET', $url );
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'visit landing page with spm , but no secret_token'  );
        // post reg form 

        echo $url, PHP_EOL;

        //
        $this->assertEquals(1,1, ' check the access log exsits');
        $this->assertEquals(1,1, ' check the content of last line in log file');
        $this->assertEquals(1,1, ' check the signed success log , the last record in table user_sign_up_route ');

    }

    /**
     * landingAction with not exists: wenwen code exists email
     */

    /**
     * landingAction with not exists: fresh email
     * @group user
     */
    public function testLandingActionFresh()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        // JiliApiBundle:WenwenUser' or JiliApiBundle:WenWenUser'
        $user_wenwen = $em->getRepository('JiliApiBundle:WenwenUser')->findOneByEmail($query['email']);

        if( $user_wenwen) {
            $em->remove($user_wenwen);
            $em->flush();
            $em->clear();
        }
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if( $user) {
            $em->remove($user);
            $em->flush();
            $em->clear();
        }

        $secret_token= $this->genSecretToken($query);
        $url = $router->generate('_default_landing', array('secret_token'=>$secret_token));
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after visit landing page'  );
        $form = $crawler->selectButton('1秒注册积粒网')->form();

        $form['nick'] = 'alice323';//str_replace( '@.', '',$query['email']);
        $form['pwd'] = 'dddddd';
        $form['newPwd'] = 'dddddd';

        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        ///  check db status
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        $this->assertEquals(1, count($user));
        echo 'nick:',$user->getNick(),PHP_EOL;
        echo 'email:',$user->getEmail(),PHP_EOL;
        echo 'pwd:','dddddd',PHP_EOL;

//        restore
    }
    /**
     *@param $plain => array( email, uniqkey )
     */
    private function genSecretToken($plain)
    {
        $plain['signature'] = $this->getToken($plain['email']);
        return  strtr(base64_encode(json_encode($plain)), '+/', '-_');
    }
    /**
     * copied from wenwenController.php to gen the signature
     */
    private function getToken($email)
    {
        $seed = "ADF93768CF";
        $hash = sha1($email . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }
    private function buildToken($user , $secret)
    {
        $token = implode('|',$user) .$secret;//.$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }
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
