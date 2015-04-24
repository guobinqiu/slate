<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;


class DuomaiControllerTest extends WebTestCase
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


        $container  = static::$kernel->getContainer();
        $em = $container->get('doctrine')
            ->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->client = static::createClient();
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * 正常请求
     * @group issue_680 
     */
    public function testGetInfoNormal() 
    {
        $client = $this->client;
        $container = $this->container;
        $em = $this->em;
        $query_array = array(
            'ads_id'=>0,
            'ads_name'=>'测试活动',
            'site_id'=>0,
            'link_id'=>0,
            'euid'=>'',
            'order_sn'=>0,
            'order_time'=>'0000-00-00 00:00:00',
            'orders_price'=>0.00,
            'siter_commission'=>0.00,
            'status'=>-1,
            'checksum'=>'bb9a518f17b400380c2a3d22ebd7cdbf' 
        );
        $url = $container->get('router')->generate('_api_duomai_getinfo'  ) ;
        $this->assertEquals('/api/duomai/getInfo', $url);
        $response = $client->request('POST', $url , $query_array);
        $this->assertEquals(1,1);
    }

    /**
     * 验证请求内容
     * @group issue_680 
     */
    public function testGetInfoValidation() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_api_duomai_getinfo'  ) ;
        $this->assertEquals('/api/duomai/getInfo', $url);
        $response = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'BAD request method' );
        $this->assertEquals('-1', $client->getResponse()->getContent(),'request direct');
        
        $response = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'BAD request method' );
        $this->assertEquals('-1', $client->getResponse()->getContent(),'request direct');

        $this->assertEquals(1,1);
    }


    /**
     * 订单初提交 
     * @group issue_680 
     */
    public function testGetInfoInit() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $em = $this->em;
        $query_array = array(
            'ads_id'=>1,
            'ads_name'=>'测试活动init',
            'site_id'=>1,
            'link_id'=>1,
            'euid'=>'1',
            'order_sn'=>'asdfasf',
            'order_time'=>'2015-04-02 00:00:00',
            'orders_price'=>10.00,
            'siter_commission'=>4.00,
            'status'=> 0,
            'checksum'=>'bb9a518f17b400380c2a3d22ebd7cdbf' 
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $this->assertEquals('/api/duomai/getInfo?ads_id=1&ads_name=%E6%B5%8B%E8%AF%95%E6%B4%BB%E5%8A%A8init&site_id=1&link_id=1&euid=1&order_sn=asdfasf&order_time=2015-04-02+00%3A00%3A00&orders_price=10&siter_commission=4&status=0&checksum=bb9a518f17b400380c2a3d22ebd7cdbf', $url);

        $crawler = $client->request('GET', $url ) ;

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $this->assertEquals(1,1);
    }

    /**
     * @group issue_680 
     */
    public function testGetInfoTodo() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_api_duomai_getinfo'  ) ;
        $this->assertEquals('/api/duomai/getInfo', $url);
        $response = $client->request('POST', $url ) ;
        $this->assertEquals(1,1);
    }
}
