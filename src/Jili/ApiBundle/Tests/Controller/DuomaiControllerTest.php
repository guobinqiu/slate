<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader; 
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadInitData;
use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadConfirmedData;

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

        $tn = $this->getName();
        if (in_array($tn, array('testGetInfoConfirmed','testGetInfoInvalid'))) {
            $fixture = new LoadInitData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        
         } else if (in_array($tn, array('testGetInfoBalanced'))) {
            $fixture = new LoadConfirmedData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
         }

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
            'checksum'=>'bb9a518f17b400380c2a3d22ebd7cdbf' ,
            'id'=> 11111,
        );
        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $this->assertEquals('/api/duomai/getInfo?ads_id=1&ads_name=%E6%B5%8B%E8%AF%95%E6%B4%BB%E5%8A%A8init&site_id=1&link_id=1&euid=1&order_sn=asdfasf&order_time=2015-04-02+00%3A00%3A00&orders_price=10&siter_commission=4&status=0&checksum=bb9a518f17b400380c2a3d22ebd7cdbf&id=11111', $url);

        $query_array = array(
            'ads_id' => '61',
            'ads_name' => '京东商城CPS推广',
            'euid' => '105',
            'link_id' => '0',
            'order_sn' => '9152050154',
            'order_time' => '2015-04-27 10:28:59',
            'orders_price' => '799.00',
            'site_id' => '152244',
            'siter_commission' => '5.40',
            'status' => '0',
            'checksum' => '8819d8bbd92f6dfc5a4eb9dd84fead27',
            'id' => '71440050',
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;
        $this->assertEquals('/api/duomai/getInfo?ads_id=61&ads_name=%E4%BA%AC%E4%B8%9C%E5%95%86%E5%9F%8ECPS%E6%8E%A8%E5%B9%BF&euid=105&link_id=0&order_sn=9152050154&order_time=2015-04-27+10%3A28%3A59&orders_price=799.00&site_id=152244&siter_commission=5.40&status=0&checksum=8819d8bbd92f6dfc5a4eb9dd84fead27&id=71440050', $url);

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(1, $client->getResponse()->getContent());

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);
        $this->assertEquals('1', $duomai_order_records[0]['status']);

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertCount(1,$task_history_records);
        $this->assertEquals(1,$task_history_records[0]['status']);
        
        # qeury for duomai order
        # qeury for task_history 
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(0, $client->getResponse()->getContent(), 'duplicated callback return 0 ');
    }

    /**
     * 订单确认提交 
     * @group issue_680 
     * @group debug 
     */
    public function testGetInfoConfirmed() 
    {

        $client = $this->client;
        $container = $client->getContainer();
        $em = $this->em;

        $query_array = array(
            'ads_id' => '61',
            'ads_name' => '京东商城CPS推广',
            'euid' => '105',
            'link_id' => '0',
            'order_sn' => '9152050154',
            'order_time' => '2015-04-27 10:28:59',
            'orders_price' => '799.00',
            'site_id' => '152244',
            'siter_commission' => '5.40',
            'status' => '1',
            'checksum' => '8819d8bbd92f6dfc5a4eb9dd84fead27',
            'id' => '71440050',
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(1, $client->getResponse()->getContent());

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

    }

    /**
     * @group issue_680
     * @group debug 
     */
    public function testGetInfoBalanced() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $em = $this->em;

        $query_array = array(
            'ads_id' => '61',
            'ads_name' => '京东商城CPS推广',
            'euid' => '105',
            'link_id' => '0',
            'order_sn' => '9152050154',
            'order_time' => '2015-04-27 10:28:59',
            'orders_price' => '799.00',
            'site_id' => '152244',
            'siter_commission' => '5.40',
            'status' => '2',
            'checksum' => '8819d8bbd92f6dfc5a4eb9dd84fead27',
            'id' => '71440050',
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(1, $client->getResponse()->getContent());

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertNotNull( $duomai_order_records);
        $this->assertCount(1,  $duomai_order_records);
        $this->assertEquals(3,  $duomai_order_records[0]['status']);

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertNotNull( $task_history_records);
        $this->assertCount(1,  $task_history_records);
        $this->assertEquals(3,  $task_history_records[0]['status']);
        $point_history_stm  =   $em->getConnection()->prepare('select * from point_history05');
        $point_history_stm->execute(); 
        $point_history_records =$point_history_stm->fetchAll();


        $this->assertNotNull( $point_history_records);
        $this->assertCount(1,  $point_history_records);
        $this->assertEquals(378,  $point_history_records[0]['point_change_num']);

        $this->assertEquals(\Jili\ApiBundle\Entity\AdCategory::ID_DUOMAI,  $point_history_records[0]['reason']);



        $users_stm  =   $em->getConnection()->prepare('select * from user');
        $users_stm->execute(); 
        $users =$users_stm->fetchAll();

        $this->assertNotNull( $users);
        $this->assertCount(1,  $users);
        $this->assertEquals(105,  $users[0]['id']);
        $this->assertEquals(98614,  $users[0]['points'], 'point added by 378');

    }

    /**
     * @group issue_680
     */
    public function testGetInfoInvalid() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $em = $this->em;

        $query_array = array(
            'ads_id' => '61',
            'ads_name' => '京东商城CPS推广',
            'euid' => '105',
            'link_id' => '0',
            'order_sn' => '9152050154',
            'order_time' => '2015-04-27 10:28:59',
            'orders_price' => '799.00',
            'site_id' => '152244',
            'siter_commission' => '5.40',
            'status' => '-1',
            'checksum' => '8819d8bbd92f6dfc5a4eb9dd84fead27',
            'id' => '71440050',
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(1, $client->getResponse()->getContent());

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertNotNull(  $duomai_order_records);
        $this->assertCount(1,  $duomai_order_records);
        $this->assertEquals(4,  $duomai_order_records[0]['status']);

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertNotNull( $task_history_records);
        $this->assertCount(1,  $task_history_records);
        $this->assertEquals(4,  $task_history_records[0]['status']);
        $users_stm  =   $em->getConnection()->prepare('select * from user');
        $users_stm->execute(); 
        $users =$users_stm->fetchAll();

        $this->assertNotNull( $users);
        $this->assertCount(1,  $users);
        $this->assertEquals(105,  $users[0]['id']);
        $this->assertEquals(98236,  $users[0]['points'], 'point not changed');

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
