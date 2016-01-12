<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSetPasswordCodeData;

class SingupControllerTest extends WebTestCase
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
        $client = static::createClient();
        $container= static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

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


        $this->client= $client;
        $this->container = $container;
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

    public function testRegisterConfirmActionInvalidCode()
    {
        $client= $this->client;
        $container= $this->container;
        $em = $this->em;

        $url = $container->get('router')->generate('_signup_confirm_register', array('register_key'=>'a') );
        $this->assertEquals('https://localhost/confirmRegister/register_key/a',$url, 'register confirm link');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'not exists password code'  );

    }

    /**
     * @group debug
     */
    public function testRegisterConfirmAction()
    {
        $client= $this->client;
        $container= $this->container;
        $em = $this->em;

        $user = LoadUserSetPasswordCodeData::$USER[0];
        $password_code = LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0];

        $url = $container->get('router')->generate('_signup_confirm_register', array('register_key'=>$password_code->getCode()) );
        $crawler = $client->request('GET', $url ) ;

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'request with a valide password code'  );

 
    }

}
