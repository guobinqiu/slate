<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadApiDupEmailCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadApiGetAdwInfoCodeData;

class ApiControllerTest extends WebTestCase
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
        $container  = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $tn = $this->getName();
        if($tn == 'testIsEmailDuplicated' ) {
            // load fixtures
            $fixture = new LoadApiDupEmailCodeData();
            $fixture->setContainer($container);

            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());

        } else if(substr( $tn,0, 15) == 'test_getAdwInfo') {
            $fixture = new LoadApiGetAdwInfoCodeData();

            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

        $this->container = $container;
        $this->em  = $em;
        $this->client= static::createClient();
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
     * todo: testing required update the schema & method to http post
     * @group api
     */
    public function testIsEmailDuplicated()
    {

        $client = $this->client;

        $data = array(
            array('chiangtor@gmail.com', '1'),
            array('zchua9999@sina.cn', '0'),
        );
        $user = LoadApiDupEmailCodeData::$ROWS[0];

        $container = $this->container;
        $url = $container->get('router')->generate('_api_check_email');

        foreach($data as $r) {
            $email = $r[0];
            $expected = $r[1];
            $crawler = $client->request('POST', $url, array('email'=>$email));
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertEquals($expected, $client->getResponse()->getContent(),'expected ' . $expected . ' with email '. $email );
        }
    }

    public function test_getAdwInfo_advertisement()
    {

        $client = $this->client;
        $container = $this->container ;
        $em = $this->em ;

        $user = LoadApiGetAdwInfoCodeData::$USERS[0];

        $advertisement = LoadApiGetAdwInfoCodeData::$ADVERTISEMENTS[0];

        $url = '/api/getAdwInfo?date=20150624&time=151816&type=2&promotionID=514&promotionName=%E4%BA%AC%E4%B8%9C%E5%95%86%E5%9F%8ECPS&extinfo='.$advertisement->getId().'&userinfo='.$user->getId().'&comm=3.0000&totalPrice=202.4000&ocd=9587735585&goodDetails=B1%2F0%25%2F3.0000%2F169%2F1%2F%5B1545861%5D%3AA1%2F0%25%2F0.0000%2F9.9%2F1%2F%5B1417552%5D%3AA1%2F0%25%2F0.0000%2F23.5%2F1%2F%5B1039780%5D&paymentmethod=0&status=0&paid=2&confirm=0';

        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $orders_stm  =  $em->getConnection()->prepare('select * from adw_order');
        $orders_stm->execute();
        $orders = $orders_stm->fetchAll();

        $this->assertNotNull( $orders);
        $this->assertCount(1,  $orders);
        $this->assertEquals('9587735585', $orders[0]['ocd']);
        $this->assertEquals('2', $orders[0]['order_status'], '初始订单的adw_order表中的状态为1');

        $task_hisotries_stm  =   $em->getConnection()->prepare('select * from task_history0'.($user->getId() % 10));
        $task_hisotries_stm->execute();
        $task_hisotries= $task_hisotries_stm->fetchAll();

        $this->assertNotNull( $task_hisotries);
        $this->assertCount(1,  $task_hisotries);
        $this->assertEquals(2,$task_hisotries[0]['status'], '初始订单的task_history 表中的状态为1');
    }

    public function test_getAdwInfo_cps_advertisement()
    {
        $client = $this->client;
        $container = $this->container ;
        $em = $this->em ;

        $user= LoadApiGetAdwInfoCodeData::$USERS[0];
        $cps_advertisement = LoadApiGetAdwInfoCodeData::$CPS_ADVERTISEMENTS[0];

        $url = '/api/getAdwInfo?date=20150624&time=151816&type=2&promotionID=514&promotionName=%E4%BA%AC%E4%B8%9C%E5%95%86%E5%9F%8ECPS&extinfo='.$user->getId().'&userinfo='.$user->getId().'_'.$cps_advertisement->getId().'&comm=3.0000&totalPrice=202.4000&ocd=9587735585&goodDetails=B1%2F0%25%2F3.0000%2F169%2F1%2F%5B1545861%5D%3AA1%2F0%25%2F0.0000%2F9.9%2F1%2F%5B1417552%5D%3AA1%2F0%25%2F0.0000%2F23.5%2F1%2F%5B1039780%5D&paymentmethod=0&status=0&paid=2&confirm=0';

        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $orders_stm  =   $em->getConnection()->prepare('select * from adw_order');
        $orders_stm->execute();
        $orders = $orders_stm->fetchAll();
        $this->assertNotNull( $orders);
        $this->assertCount(2,  $orders);
        $this->assertEquals('9587735585', $orders[1]['ocd']);
        $this->assertEquals('2', $orders[1]['order_status'], '初始订单的adw_order表中的状态为1');

        $task_hisotries_stm  =   $em->getConnection()->prepare('select * from task_history0'.($user->getId() % 10));
        $task_hisotries_stm->execute();
        $task_hisotries= $task_hisotries_stm->fetchAll();

        $this->assertNotNull( $task_hisotries);
        $this->assertCount(2,  $task_hisotries);
        $this->assertEquals(2,$task_hisotries[1]['status'], '初始订单的task_history 表中的状态为1');

// check order status
// check task history status
// check point history status
// check user points

    }

}
