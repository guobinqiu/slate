<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\AdminController;

class AdminControllerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group HandleExchangeWen
     */
    public function testHandleExchangeWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        //测试有关表user,exchange_from_wenwen
        $file[1][0] = "91jili-201402-2624-927390";
        $file[1][1] = "zhangmm@voyagegroup.com.cn";
        $file[1][2] = "30";
        $file[1][3] = "3000";

        $file[2][0] = "91jili-201402-2625-1036110";
        $file[2][1] = "zhangmm1@voyagegroup.com.cn";
        $file[2][2] = "30";
        $file[2][3] = "3000";

        $file[3][0] = "91jili-201402-2625-1036111";
        $file[3][1] = "zhangmm2@voyagegroup.com.cn";
        $file[3][2] = "30";
        $file[3][3] = "3000";

        $return = $controller->handleExchangeWen($file);
        $this->assertEquals(3, count($return));
    }

    /**
     * @group InsertSendMs
     */
    public function testInsertSendMs() {
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
    public function testdelSendMs() {
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
    public function testupdateSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array(
                      'sendid'=> 8,
                      'userid' => 1057704,
                      'title' => "test title",
                      'content' => "test content"
                    );
        $controller->updateSendMs($params);
    }

    /**
     * @group selectSendMsById
     */
    public function testselectSendMsById() {
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
    public function testselectSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $userid = 1057704;
        $return = $controller->selectSendMs($userid);
        $this->assertEquals(7, count($return));
    }

    /**
     * @group SelectTaskPercent
     */
    public function testSelectTaskPercent() {
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
    public function testUpdateTaskHistory() {
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
    public function testgetTaskHistory() {
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
    public function testGetPointHistory() {
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
    public function testExchangeOKWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $email = 'zhangmm@voyagegroup.com.cn';
        $points = 120;
        $return = $controller->exchangeOKWen($email,$points);
        $this->assertTrue($return);
    }
}
