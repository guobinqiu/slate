<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\AdminController;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadExchangeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadHandleExchangeWenData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserTaskHistoryData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSendMessageData;
use Jili\ApiBundle\DataFixtures\ORM\LoadAdminSelectTaskPercentCodeData;

class AdminControllerTest extends WebTestCase {

    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $with_fixture = false;

        $tn = $this->getName();
        if ($tn == 'testHandleExchangeWen') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadHandleExchangeWenData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }
        if ($tn == 'testUpdateTaskHistory') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadUserTaskHistoryData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }

        if ($tn == 'testHandleExchange') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadExchangeData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }

        if ($tn == 'testdelSendMs') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadUserSendMessageData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }

        //        if (in_array($tn, array (
        //                'testExchangeOKWen',
        //                'testHandleExchange',
        //                'testHandleExchangeWen'
        //            ))) {
        //            $with_fixture = true;
        //            // load fixtures
        //            $fixture = new LoadExchangeData();
        //            $loader = new Loader();
        //            $loader->addFixture($fixture);
        //        }
        //        elseif (in_array($tn, array (
        //            'testSelectTaskPercent'
        //        ))) {
        //
        //            $with_fixture = true;
        //
        //            $fixture = new LoadAdminSelectTaskPercentCodeData();
        //            $loader = new Loader();
        //            $loader->addFixture($fixture);
        //
        //        }

        if (true === $with_fixture) {
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
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @author mmzhang
     * @group HandleExchangeWen
     */
    public function testHandleExchangeWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $em = $this->em;

        //已经导入过(已发放)
        //用户不存在（account not exists）
        //用户存在，但密码为空(账号没有激活)
        //成功：ExchangeFromWenwen，User，PointHistory，SendMessage

        $users = LoadHandleExchangeWenData :: $USERS;
        $exchange = LoadHandleExchangeWenData :: $EXCHANGE_FROM_WENWEN;

        //测试有关表user,exchange_from_wenwen
        $file[1][0] = $exchange->getWenwenExchangeId();
        $file[1][1] = $users[0]->getEmail();
        $file[1][2] = '30';
        $file[1][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($exchange->getWenwenExchangeId() . '已发放', $return[0]);

        $file = array ();
        $file[2][0] = '91jili-201402-2625-1036110';
        $file[2][1] = $users[0]->getEmail() . 'test';
        $file[2][2] = '30';
        $file[2][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($file[2][0] . '兑换失败', $return[0]);
        $exchangeFromWenwen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($file[2][0]);
        $this->assertEquals('account not exists', $exchangeFromWenwen[0]->getReason());

        $file = array ();
        $file[3][0] = '91jili-201402-2625-1036111';
        $file[3][1] = $users[1]->getEmail();
        $file[3][2] = '30';
        $file[3][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($file[3][0] . '兑换失败', $return[0]);
        $exchangeFromWenwen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($file[3][0]);
        $this->assertEquals('账号没有激活', $exchangeFromWenwen[0]->getReason());

        $file = array ();
        $file[4][0] = '91jili-201402-2625-1036112';
        $file[4][1] = $users[2]->getEmail();
        $file[4][2] = '30';
        $file[4][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $exchangeFromWenwen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($file[4][0]);
        $this->assertEquals(1, $exchangeFromWenwen[0]->getStatus());
        $this->assertEquals($users[2]->getId(), $exchangeFromWenwen[0]->getUserId());
        $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($users[2]->getEmail());
        $this->assertEquals($users[2]->getPoints() + $file[4][3], $userInfo[0]->getPoints());
        $pointHistory = $em->getRepository('JiliApiBundle:PointHistory0' . ($users[2]->getId() % 10))->findByUserId($users[2]->getId());
        $this->assertEquals('+' . $file[4][3], $pointHistory[0]->getPointChangeNum());
        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($users[2]->getId() % 10))->findBySendTo($users[2]->getId());
        $title = $container->getParameter('exchange_finish_wenwen_title');
        $this->assertEquals($title, $sendMessage[0]->getTitle());
    }

    /**
     * @group InsertSendMs
     */
    public function testInsertSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $parms = array (
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

        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;
        $controller->delSendMs($sm->getSendTo(), $sm->getId());
        $em = $this->em;
        $sm = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->find($sm->getId());
        $this->assertEquals(1, $sm->getDeleteFlag());
    }

    /**
     * @group updateSendMs
     */
    public function testupdateSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array (
            'sendid' => 1,
            'userid' => 1057705,
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
        $return = $controller->selectSendMsById($userid, $sendid);
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

        $id = 5;
        $return = $controller->selectSendMs($id);
        $this->assertEquals(3, count($return));
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
        $return = $controller->selectTaskPercent($userid, $orderId);
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

        $task_history = LoadUserTaskHistoryData :: $TASK_HISTORY;

        $params = array (
            'userid' => $task_history->getUserId(),
            'orderId' => $task_history->getOrderId(),
            'taskType' => $task_history->getTaskType() + 1,
            'reward_percent' => $task_history->getRewardPercent(),
            'point' => $task_history->getPoint(),
            'date' => date('Y-m-d H:i:s'),
            'status' => $task_history->getStatus()
        );
        $return = $controller->updateTaskHistory($params);
        $this->assertFalse($return);

        $params = array (
            'userid' => $task_history->getUserId(),
            'orderId' => $task_history->getOrderId() + 1,
            'taskType' => $task_history->getTaskType(),
            'reward_percent' => $task_history->getRewardPercent(),
            'point' => $task_history->getPoint(),
            'date' => date('Y-m-d H:i:s'),
            'status' => $task_history->getStatus()
        );
        $return = $controller->updateTaskHistory($params);
        $this->assertFalse($return);

        $params = array (
            'userid' => $task_history->getUserId(),
            'orderId' => $task_history->getOrderId(),
            'taskType' => $task_history->getTaskType(),
            'reward_percent' => $task_history->getRewardPercent(),
            'point' => $task_history->getPoint() + 100,
            'date' => date('Y-m-d H:i:s'),
            'status' => $task_history->getStatus()
        );
        $return = $controller->updateTaskHistory($params);
        $this->assertTrue($return);
        $em = $this->em;
        $taskHistory = $em->getRepository('JiliApiBundle:TaskHistory0' . ($task_history->getUserId() % 10))->findByUserId($task_history->getUserId());
        $this->assertEquals($task_history->getPoint() + 100, $taskHistory[0]->getPoint());
    }

    /**
     * @group getTaskHistory
     */
    public function testgetTaskHistory() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $params = array (
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
        $controller->getPointHistory($userid, $point, $type);
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
        $return = $controller->exchangeOKWen($email, $points);
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