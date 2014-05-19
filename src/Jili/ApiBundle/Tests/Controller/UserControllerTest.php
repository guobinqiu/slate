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
        $form['pwd'] = 'cccccc';
        $form['que_pwd'] = 'cccccc';

        // submit the form
        print 'Submit the form'.PHP_EOL;
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
    }
    /**
     * @group debug
     * @group user 
     * @group login 
     */
    public function testLoginRemeberMeAction()
    {

        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');

#        headers_list();
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
        $form['pwd'] = 'cccccc';
        $form['remember_me']->tick();

        $client->submit($form);
        $session = $container->get('session');
#        $cookies  = $client->getCookieJar()->all() ;
#
#        var_dump( $cookies);
#        var_dump( $session->all() );
#        $cn = get_class($client->getResponse());
#        $cm = get_class_methods($cn);
#
#        echo $cn ,PHP_EOL;
#        print_r( $cm);
        $this->assertEquals(301, $client->getResponse()->getStatusCode() );
        $this->assertTrue( $session->has('uid'));


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
}
