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
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
       $this->em->close();
    }

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
            print ' code not found!',PHP_EOL;
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
