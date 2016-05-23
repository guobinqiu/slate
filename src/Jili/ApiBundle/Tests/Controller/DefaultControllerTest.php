<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserLandingWenwenCodeData;
use Jili\ApiBundle\Utility\WenwenToken;

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

        $container = static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $test_name = $this->getName();

        if( in_array($test_name , array('testAdLoginAction') )) {

            $fixture = new LoadUserLandingWenwenCodeData();
            $fixture->setContainer( $container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

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

        $user = LoadUserLandingWenwenCodeData::$ROWS[0];

        $query = array('email'=> $user->getEmail() );

        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        $this->assertEquals(1, count($user));

        $url = $router->generate('_default_ad_login',array(), true);
        $post =  array( 'email'=>$query['email'] , 'pwd'=> 'aaaaaa') ;

        //echo $url, PHP_EOL;
        $crawler = $client->request('POST', $url, $post) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'ad login in '  );
        $this->assertEquals('ok', $client->getResponse()->getContent());
        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));
        $this->assertEquals($user->getId(), $session->get('uid'));
        //
        //echo $url, PHP_EOL;
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

    }

    private function buildToken($user , $secret)
    {
        $token = implode('|',$user) .$secret;//.$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }
}
