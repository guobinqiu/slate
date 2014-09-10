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
        $em = $this->em;

        $url =  $container->get('router')->generate('jili_frontend_autocheckinconfig_create');
        // 1. no session uid
        // 1.1. only PUT
        $client->request('PUT', $url );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');

        // 1.2.only Ajax without PUT
        $client->request('POST', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');

        // 1.3.Ajax and PUT;
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');


        $users = LoadUserCodeData::$USERS;
        // 2. session uid of null user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[2]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $expected = '{"code":200,"message":"\u6210\u529f"}';//"成功"
        $this->assertEquals($expected, $client->getResponse()->getContent(),'成功');
        $r = $this->em->getRepository('JiliApiBundle:UserConfigurations')->findBy( array('userId'=> $users[2]->getId() ,'flagName' =>'checkin_flag','flagData'=>1 ) );;
        $this->assertNotNull($r);

        // 3. session uid of false  user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $expected = '{"code":201,"message":"\u5df2\u7ecf\u5b58\u5728"}';//已经存在
        $this->assertEquals($expected, $client->getResponse()->getContent(),'已经存在');
        // checkin current data. 

        // 4. session uid of true user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('PUT', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'已经存在');

        // 4. session uid of true user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();

        $client->request('PUT', $url );
        $response =  $client->getResponse();
        $expected = '{"code":400,"message":"\u8bf7\u6c42\u65b9\u6cd5\u4e0d\u5bf9"}'; //请求方法不对
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');

        // 1.2.only Ajax without PUT
        $client->request('POST', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
    }

    /**
     * @group debug
     */
    public function testDelete()
    {
        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;

        $url =  $container->get('router')->generate('jili_frontend_autocheckinconfig_delete');
        // 1. no session uid
        // 1.1. only DELETE 
        $client->request('DELETE', $url );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        //
        // 1.2. only AJAX 
        $client->request('GET', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        // 1.3. Ajax and DELETE ;
        $client->request('DELETE', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        
        $users = LoadUserCodeData::$USERS;
        // 2. with session uid 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[2]->getId());
        $session->save();
        // 2.1 only DELETE
        $client->request('DELETE', $url );
        $expected = '{"code":400,"message":"\u8bf7\u6c42\u65b9\u6cd5\u4e0d\u5bf9"}'; //请求方法不对
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        
        // 2.2 only Ajax 
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        // 2.3. Ajax and DELETE ;
        
        // 3. with session uid of no user_configurations 
        $client->request('DELETE', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":404,"message":"\u8bb0\u5f55\u4e0d\u5b58\u5728"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'记录不存在');
        
        // 4. with session uid having  user_configurations 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('DELETE', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":200,"data":{"countOfRemoved":1},"message":"\u5b8c\u6210"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'完成');
        // 4.1 checking the remove result!!
        $r= $em->getRepository('JiliApiBundle:UserConfigurations')->findBy(array('flagName'=> 'auto_checkin', 'userId'=> $users[1]->getId(), 'flagData' => 0 ));
        $this->assertNotNull($r);
    }

    /**
     * @group debug
     */
    public function testUpdate()
    {
        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;
        $url =  $container->get('router')->generate('jili_frontend_autocheckinconfig_update');
        // 1. no session uid
        // 1.1. only POST 
        $client->request('POST', $url );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        // 1.2. only AJAX 
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest') );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        // 1.3. Ajax and POST;
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest') );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        
        $users = LoadUserCodeData::$USERS;
        // 2. with session uid 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[2]->getId());
        $session->save();
        // 2.1 only POST 
        $client->request('POST', $url );
        $expected = '{"code":400,"message":"\u8bf7\u6c42\u65b9\u6cd5\u4e0d\u5bf9"}'; //请求方法不对
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        
        // 2.2 only Ajax 
        $client->request('PUT', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        // 2.3. Ajax and  POST;
        //
        // 3. with session uid of no user_configurations 
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":404,"message":"\u8bb0\u5f55\u4e0d\u5b58\u5728"}';

        $this->assertEquals($expected, $client->getResponse()->getContent(),'记录不存在');

        // 4. with session uid having  user_configurations, false
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":200,"data":{"countOfUpdated":1},"message":"\u5b8c\u6210"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'完成');
        $r = $em->getRepository('JiliApiBundle:UserConfigurations')->findBy( array('userId'=>$users[1]->getId(), 'flagName'=> 'auto_checkin', 'flagData'=>1 ));;
        $this->assertNotNull($r);

        // 5. with session uid having  user_configurations, true
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[0]->getId());
        $session->save();
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'完成');
        $r = $em->getRepository('JiliApiBundle:UserConfigurations')->findBy( array('userId'=>$users[0]->getId(), 'flagName'=> 'auto_checkin', 'flagData'=>1 ));;
        $this->assertNotNull($r);
    }

    /**
     * @group debug
     */
    public function testGet()
    {

        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;

        $url =  $container->get('router')->generate('jili_frontend_autocheckinconfig_get');
// jili_frontend_autocheckinconfig_get
        // 1. no session uid
        // 1.1. only GET 
        $client->request('GET', $url );
        $response =  $client->getResponse();
        $expected = '{"code":401,"message":"\u9700\u8981\u767b\u5f55"}'; //需要登录
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        // 1.2. only AJAX 
        $client->request('POST', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        // 1.3. Ajax and GET;
        $client->request('GET', $url , array(), array(),array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expected, $client->getResponse()->getContent(),'需要登录');
        
        $users = LoadUserCodeData::$USERS;
        // 2. with session uid 
        // 
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[2]->getId());
        $session->save();
        // 2.1 only GET 
        $client->request('GET', $url );
        $expected = '{"code":400,"message":"\u8bf7\u6c42\u65b9\u6cd5\u4e0d\u5bf9"}'; //请求方法不对
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        // 2.2 only Ajax 
        $client->request('POST', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $this->assertEquals($expected, $client->getResponse()->getContent(),'请求方法不对');
        // 2.3. Ajax and  GET;
        
        // 3. with session uid of no user_configurations 
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":404,"message":"\u8bb0\u5f55\u4e0d\u5b58\u5728"}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'记录不存在');

        // 4. with session uid having  user_configurations, false
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[1]->getId());
        $session->save();
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":200,"data":{"flag_data":false}}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'完成');

        // 5. with session uid having  user_configurations, true
        $session = static::$kernel->getContainer()->get('session');
        $session->set('uid', $users[0]->getId());
        $session->save();
        $client->request('GET', $url , array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'check request status code ');
        $expected = '{"code":200,"data":{"flag_data":true}}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'完成');
    }

} 

