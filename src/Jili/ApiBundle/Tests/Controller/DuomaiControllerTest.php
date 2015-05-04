<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader; 
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadInitData;
use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadConfirmedData;

/**
 * @abstract duomai 回调接口测试
 */
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
            'order_sn'=>'0',
            'order_time'=>'0000-00-00 00:00:00',
            'orders_price'=>'0.00',
            'siter_commission'=>'0.00',
            'status'=>'-1',
            'checksum'=>'bb9a518f17b400380c2a3d22ebd7cdbf' 
        );
        $url = $container->get('router')->generate('_api_duomai_getinfo', $query_array  ) ;

        $this->assertEquals('/api/duomai/getInfo?ads_id=0&ads_name=%E6%B5%8B%E8%AF%95%E6%B4%BB%E5%8A%A8&site_id=0&link_id=0&euid=&order_sn=0&order_time=0000-00-00+00%3A00%3A00&orders_price=0.00&siter_commission=0.00&status=-1&checksum=bb9a518f17b400380c2a3d22ebd7cdbf', $url, 'duomai回调的完成url');

        $crawler = $client->request('GET', $url);

        $this->assertEquals( '1', $client->getResponse()->getContent(),'Return 1 when  callback api verify params' );
    }

    /**
     * 验证请求内容
     * @group issue_680 
     */
    public function testGetInfoValidation() 
    {
        $client = $this->client;
        $container = $client->getContainer();
        $configs = $container->getParameter('duomai_com');

        $this->assertEquals('f2cc3391af820e539fac5d3fbcb89c2c', $configs['site_hash'], 'site hash');


        $this->assertEquals(0, $configs['status']['UNCERTAIN'],'订单状态  0 未确认 ');
        $this->assertEquals(1, $configs['status']['CONFIRMED'],'订单状态  1 确认');
        $this->assertEquals(2, $configs['status']['BALANCED'],'订单状态  2 结算');
        $this->assertEquals(-1, $configs['status']['INVALID'], '订单状态  -1 无效 ');

        $this->assertEquals(1, $configs['response']['SUCCESS'], '表示此次推送成功 并且订单已成功入');
        $this->assertEquals(0, $configs['response']['SUCCESS_DUPLICATED'], '表示推送成功 但订单已存在。');
        $this->assertEquals(-1, $configs['response']['FAILED'], '表示推送失败');

        $url = $container->get('router')->generate('_api_duomai_getinfo'  ) ;
        $this->assertEquals('/api/duomai/getInfo', $url);
        $response = $client->request('POST', $url ) ;
        $this->assertEquals(405, $client->getResponse()->getStatusCode(),'BAD request method' );

        $response = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'lack parameter' );
        $this->assertEquals(-1, $client->getResponse()->getContent());


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

        $required_keys = array('ads_id', 'ads_name','site_id', 'link_id','euid','order_sn','orders_price', 'siter_commission','status');
        foreach ($required_keys as $key ) {
            $params = $query_array;
            unset($params[$key] );
            $url = $container->get('router')->generate('_api_duomai_getinfo' , $params ) ;
            $client->request('GET', $url);

            $this->assertEquals(200, $client->getResponse()->getStatusCode(),'lack parameter '. $key);
            $this->assertEquals(-1, $client->getResponse()->getContent(),'缺少参数'. $key);
        }

        $params = $query_array;
        $params['checksum'] = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $url = $container->get('router')->generate('_api_duomai_getinfo' , $params) ;
        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'无效checksum');
        $this->assertEquals(-1, $client->getResponse()->getContent(),'无效checksum');
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
            'orders_price'=>'10.00',
            'siter_commission'=>'4.00',
            'status'=> 0,
            'checksum'=>'bb9a518f17b400380c2a3d22ebd7cdbf' ,
            'id'=> 11111,
        );
        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $this->assertEquals('/api/duomai/getInfo?ads_id=1&ads_name=%E6%B5%8B%E8%AF%95%E6%B4%BB%E5%8A%A8init&site_id=1&link_id=1&euid=1&order_sn=asdfasf&order_time=2015-04-02+00%3A00%3A00&orders_price=10.00&siter_commission=4.00&status=0&checksum=bb9a518f17b400380c2a3d22ebd7cdbf&id=11111', $url,'回调url');

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

        # qeury for duomai order
        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);
        $this->assertEquals('1', $duomai_order_records[0]['status'], '初始订单的duomai_order表中的状态为1');

        # qeury for task_history 
        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertCount(1,$task_history_records);
        $this->assertEquals(1,$task_history_records[0]['status'], '初始订单的task_history 表中的状态为1');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(0, $client->getResponse()->getContent(), 'duplicated callback return 0 ');
    }

    /**
     * 订单确认提交 
     * @group issue_680 
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
            'checksum' => '3b11f021b560e3eb5a26282d02f361c8',
            'id' => '71440050',
        );

        $url = $container->get('router')->generate('_api_duomai_getinfo' , $query_array ) ;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(1, $client->getResponse()->getContent());

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();
        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);
        $this->assertEquals('2', $duomai_order_records[0]['status'], '初始订单的duomai_order表中的状态为2');

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertCount(1,$task_history_records);
        $this->assertEquals(2,$task_history_records[0]['status'], '初始订单的task_history 表中的状态为2');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(0, $client->getResponse()->getContent(), 'duplicated callback return 0 ');
    }

    /**
     * @group issue_680
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
            'checksum' => 'b1514c3865a6f43a282d8e3edbc3cfc5',
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
        $this->assertEquals(3,  $duomai_order_records[0]['status'], '结算后duomai_order.status为3');

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertNotNull( $task_history_records);
        $this->assertCount(1,  $task_history_records);
        $this->assertEquals(3,  $task_history_records[0]['status'],'订单结算后task_history status 为3');

        $point_history_stm  =   $em->getConnection()->prepare('select * from point_history05');
        $point_history_stm->execute(); 
        $point_history_records =$point_history_stm->fetchAll();


        $this->assertNotNull( $point_history_records);
        $this->assertCount(1,  $point_history_records);
        $this->assertEquals(378,  $point_history_records[0]['point_change_num'],'积分变化为378' );

        $this->assertEquals(\Jili\ApiBundle\Entity\AdCategory::ID_DUOMAI,  $point_history_records[0]['reason'], '修改原因为23');



        $users_stm  =   $em->getConnection()->prepare('select * from user');
        $users_stm->execute(); 
        $users =$users_stm->fetchAll();

        $this->assertNotNull( $users);
        $this->assertCount(1,  $users);
        $this->assertEquals(105,  $users[0]['id']);
        $this->assertEquals(98614,  $users[0]['points'], 'point added by 378');


        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(0, $client->getResponse()->getContent(), 'duplicated callback return 0 ');
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
            'checksum' => '5f2604ab4d3e3147d678803e1fe6f0e4',
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
        $this->assertEquals(4,  $duomai_order_records[0]['status'], '无效状态的duomai_order 为4');

        $task_history_stm  =   $em->getConnection()->prepare('select * from task_history05');
        $task_history_stm->execute(); 
        $task_history_records =$task_history_stm->fetchAll();

        $this->assertNotNull( $task_history_records);
        $this->assertCount(1,  $task_history_records);
        $this->assertEquals(4,  $task_history_records[0]['status'],'无效状态task_history.status = 4');
        $users_stm  =   $em->getConnection()->prepare('select * from user');
        $users_stm->execute(); 
        $users =$users_stm->fetchAll();

        $this->assertNotNull( $users);
        $this->assertCount(1,  $users);
        $this->assertEquals(105,  $users[0]['id']);
        $this->assertEquals(98236,  $users[0]['points'], 'point not changed');


        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals(0, $client->getResponse()->getContent(), 'duplicated callback return 0 ');
    }

}
