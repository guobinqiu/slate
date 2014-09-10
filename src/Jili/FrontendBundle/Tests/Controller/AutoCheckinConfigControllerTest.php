<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;


use Jili\FrontendBundle\DataFixtures\ORM\AutoCheckinConfig\LoadUserCodeData;
use Jili\FrontendBundle\DataFixtures\ORM\AutoCheckinConfig\LoadUserConfigurationsCodeData;

class AutoCheckinConfigControllerTest extends WebTestCase
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
        $directory = $container->get('kernel')->getBundle('JiliFrontendBundle')->getPath(); 
        $directory .= '/DataFixtures/ORM/AutoCheckInConfig';
        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures());

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

    /**
     * @group debug
     */
    public function testCreate()
    {
        $client = static::createClient();
        $container = $this->container;
 

        $url =  $container->get('router')->generate('_autoCheckin_create');

        // 1. no session uid
        // 1.1. only PUT
        $client->request('PUT', $url );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'bad request method');

        // 1.2.only Ajax without PUT
        $client->request('POST', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // check response 
        $this->assertEquals($expected, $client->getResponse()->getContent(),'bad request method');

        // 1.3.Ajax and PUT;
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        echo  $client->getResponse()->getContent(),PHP_EOL;

        $users = LoadUserCodeData::$USERS;
        // 2. session uid of null user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[2]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $expected = '{"code":200,"message":"\u6210\u529f"}';//"成功"
        $this->assertEquals($expected, $client->getResponse()->getContent(),'bad request method');
        $r = $em->getRepository('JiliApiBundle:UserConfigurations')->findBy( array('user'=> $users[2],'flag_name' =>'checkin_flag','flag_data'=>1 ) );;
        $this->assertNotNull($r);

        // 3. session uid of false  user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $expected = '{"code":201,"message":"已经存在"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'bad request method');
        // checkin current data. 

        // 4. session uid of true user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $expected = '{"code":201,"message":"已经存在"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'bad request method');

    }

    /**
     * @group debug
     */
    public function testDelete()
    {

// _autoCheckin_delete
        $this->assertEquals('1', 1);
    }

    /**
     * @group debug
     */
    public function testUpdate()
    {


// _autoCheckin_update
        $this->assertEquals('1', 1);
    }
    /**
     * @group debug
     */
    public function testGet()
    {

// _autoCheckin_get
        $this->assertEquals('1', 1);
    }


} 

