<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\AdminController;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadExchangeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadAdminSelectTaskPercentCodeData;
class AdminControllerTest extends WebTestCase
{
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $container=static::$kernel->getContainer();


            $with_fixture = false;
        $tn = $this->getName();
        if (in_array( $tn, array('testExchangeOKWen','testHandleExchange','testHandleExchangeWen') ) ) {
$with_fixture = true;
            // load fixtures
            $fixture = new LoadExchangeData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        } else if( in_array( $tn , array('testSelectTaskPercent') ) ){

$with_fixture = true;

            $fixture = new LoadAdminSelectTaskPercentCodeData();
            $loader = new Loader();
            $loader->addFixture($fixture);

        }

        if( true === $with_fixture ) {
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();
            $executor->execute($loader->getFixtures());
        }

        $this->container = $container;

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group HandleExchangeWen
     */
    public function testHandleExchangeWen()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        //测试有关表user,exchange_from_wenwen
        $file[1][0] = '91jili-201402-2624-927390';
        $file[1][1] = 'zhangmm@voyagegroup.com.cn';
        $file[1][2] = '30';
        $file[1][3] = '3000';

        $file[2][0] = '91jili-201402-2625-1036110';
        $file[2][1] = 'zhangmm1@voyagegroup.com.cn';
        $file[2][2] = '30';
        $file[2][3] = '3000';

        $file[3][0] = '91jili-201402-2625-1036111';
        $file[3][1] = 'zhangmm2@voyagegroup.com.cn';
        $file[3][2] = '30';
        $file[3][3] = '3000';

        $return = $controller->handleExchangeWen($file);
        var_dump($return);
        $this->assertEquals(3, count($return));
    }

    /**
     * @group InsertSendMs
     */
    public function testInsertSendMs()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $parms = array(
                  'userid' => 1057704,
                  'title' => "test",
                  'content' => "function test"
                );
        $controller->insertSendMs($parms);
    }

    /**
     * @group delSendMs
     */
    public function testdelSendMs()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $userid = 1057704;
        $sendid = 8;
        $controller->delSendMs($userid,$sendid);
    }

    /**
     * @group updateSendMs
     */
    public function testupdateSendMs()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array(
                      'sendid'=> 1,
                      'userid' => 1057705,
                      'title' => "test title",
                      'content' => "test content"
                    );
        $controller->updateSendMs($params);
    }

    /**
     * @group selectSendMsById
     */
    public function testselectSendMsById()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $userid = 1057704;
        $sendid = 7;
        $return = $controller->selectSendMsById($userid,$sendid);
        $this->assertEquals('zhangmm@voyagegroup.com.cn', $return['email']);
    }

    /**
     * @group selectSendMs
     */
    public function testselectSendMs()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $id = 5;
        $return = $controller->selectSendMs($id);
        $this->assertEquals(3, count($return));
    }

    /**
     * @group SelectTaskPercent
     */
    public function testSelectTaskPercent()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $userid = 1057704;
        $orderId = 1;
        $return = $controller->selectTaskPercent($userid,$orderId);
        $this->assertEquals(1, count($return));
    }

    /**
     * @group UpdateTaskHistory
     */
    public function testUpdateTaskHistory()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array(
              'userid' => 1057704,
              'orderId' => 1,
              'taskType' => 1,
              'reward_percent' => '40',
              'point' => 100,
              'date' => date('Y-m-d H:i:s'),
              'status' => 4
            );
        $return = $controller->updateTaskHistory($params);
        $this->assertTrue($return);
    }

    /**
     * @group getTaskHistory
     */
    public function testgetTaskHistory()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array(
          'orderId' => 0,
          'userid' => 1057704,
          'task_type' => 4,
          'categoryId' => 14,
          'taskName' => '名片入力',
          'reward_percent' => 0,
          'point' => 100,
          'date' => date('Y-m-d H:i:s'),
          'status' => 1
        );
        $controller->getTaskHistory($params);
    }

    /**
     * @group GetPointHistory
     */
    public function testGetPointHistory()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $userid = 1057704;
        $point = 120;
        $type = 4;
        $controller->getPointHistory($userid,$point,$type);
    }

    /**
     * @group ExchangeOKWen
     */
    public function testExchangeOKWen()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $email = 'zhangmm@voyagegroup.com.cn';
        $points = 120;
        $return = $controller->exchangeOKWen($email,$points);
        $this->assertTrue($return);
    }

    /**
     * @group HandleExchange
     */
    public function testHandleExchange() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $client = static :: createClient();
        $em = $this->em;


        for ($i = 0; $i < 3; $i++) {
            $exchange_id = LoadExchangeData :: $POINTS_EXCHANGES[$i]->getId();

            $file[$i +1][0] = $exchange_id;
            $file[$i +1][1] = "zhangmm@voyagegroup.com.cn";
            $file[$i +1][2] = "13761756201";
            $file[$i +1][3] = date('Y/m/d');
            $file[$i +1][4] = "2010";
            $file[$i +1][5] = "20";
            $file[$i +1][6] = "mobile";

            switch ($i) {
                case 0 :
                    $file[$i +1][7] = " OK ";
                    break;
                case 1 :
                    $file[$i +1][7] = " nG ";
                    break;
                case 2 :
                    $file[$i +1][7] = " OKe ";
                    break;
            }
            $file[$i +1][8] = date('Y/m/d');
            $file[$i +1][9] = "";
            $file[$i +1][10] = "";
            $file[$i +1][11] = "";
            $file[$i +1][12] = "";
            $type = 4;
            $controller->handleExchange($file, $type);

            $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);

            switch ($i) {
                case 0 :
                    $this->assertEquals(1, $exchange->getStatus());
                    break;
                case 1 :
                    $this->assertEquals(2, $exchange->getStatus());
                    break;
                case 2 :
                    $this->assertEquals(null, $exchange->getStatus());
                    break;
            }
        }
    }

}
