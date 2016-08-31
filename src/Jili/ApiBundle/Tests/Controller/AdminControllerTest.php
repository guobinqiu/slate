<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\AdminController;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadHandleExchangeWenData;

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

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadHandleExchangeWenData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

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
     * @group InsertSendMs
       @group issue_535
     */
    public function testInsertSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);
        $sm = LoadHandleExchangeWenData :: $SEND_MESSAGE[0];

        $parms = array (
            'userid' => $sm->getSendTo(),
            'title' => "test",
            'content' => "function test"
        );

        $em = $this->em;
        $sendMessage1 = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->findBySendTo($sm->getSendTo());
        $controller->insertSendMs($parms);
        $sendMessage2 = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->findBySendTo($sm->getSendTo());
        $this->assertEquals(1, count($sendMessage2) - count($sendMessage1));
    }

    /**
     * @group delSendMs
       @group issue_535
     */
    public function testdelSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadHandleExchangeWenData :: $SEND_MESSAGE[0];
        $controller->delSendMs($sm->getSendTo(), $sm->getId());
        $em = $this->em;
        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->find($sm->getId());
        $this->assertEquals(1, $sendMessage->getDeleteFlag());
    }

    /**
     * @group updateSendMs
       @group issue_535
     */
    public function testupdateSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadHandleExchangeWenData :: $SEND_MESSAGE[0];
        $return = $controller->selectSendMsById($sm->getSendTo(), $sm->getId());

        $params = array (
            'sendid' => $sm->getId(),
            'userid' => $sm->getSendTo(),
            'title' => "test title",
            'content' => "test content"
        );
        $controller->updateSendMs($params);

        $em = $this->em;
        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->find($sm->getId());
        $this->assertEquals($params['title'], $sendMessage->getTitle());
    }

    /**
     * @group selectSendMsById
       @group issue_535
     */
    public function testselectSendMsById() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadHandleExchangeWenData :: $SEND_MESSAGE[0];
        $user = LoadHandleExchangeWenData :: $USERS[0];
        $return = $controller->selectSendMsById($sm->getSendTo(), $sm->getId());
        $this->assertEquals($user->getEmail(), $return['email']);
    }

    /**
     * @group selectSendMs
       @group issue_535
     */
    public function testselectSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadHandleExchangeWenData :: $SEND_MESSAGE[0];
        $user = LoadHandleExchangeWenData :: $USERS[0];
        $user_id = $user->getId();

        $id = $user_id % 10;
        $return = $controller->selectSendMs($id);
        $this->assertEquals($sm->getTitle(), $return[0]['title'], '$id 是后缀数字');
    }

    /**
     * @group SelectTaskPercent
       @group issue_535
     */
    public function testSelectTaskPercent() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);
        $ta = LoadHandleExchangeWenData :: $TASK_HISTORY[1];
        $return = $controller->selectTaskPercent($ta->getUserId(), $ta->getOrderId());
        $this->assertEquals($ta->getRewardPercent(), $return['rewardPercent']);
    }

    /**
     * @group UpdateTaskHistory
       @group issue_535
     */
    public function testUpdateTaskHistory() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);
        $em = $this->em;

        $task_history = LoadHandleExchangeWenData :: $TASK_HISTORY[0];

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
        $taskHistory = $em->getRepository('JiliApiBundle:TaskHistory0' . ($task_history->getUserId() % 10))->findByUserId($task_history->getUserId());
        $this->assertEquals($task_history->getPoint() + 100, $taskHistory[0]->getPoint());
    }

    /**
     * @group getTaskHistory
       @group issue_535
     */
    public function testgetTaskHistory() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $task_history = LoadHandleExchangeWenData :: $TASK_HISTORY[0];

        $params = array (
            'userid' => $task_history->getUserId(),
            'orderId' => $task_history->getOrderId() + 1,
            'task_type' => $task_history->getTaskType() + 1,
            'categoryId' => 1,
            'reward_percent' => $task_history->getRewardPercent(),
            'point' => $task_history->getPoint(),
            'taskName' => '名片入力',
            'date' => date('Y-m-d H:i:s'),
            'status' => $task_history->getStatus()
        );

        $em = $this->em;
        $taskHistory1 = $em->getRepository('JiliApiBundle:TaskHistory0' . ($task_history->getUserId() % 10))->findByUserId($task_history->getUserId());
        $controller->getTaskHistory($params);
        $taskHistory2 = $em->getRepository('JiliApiBundle:TaskHistory0' . ($task_history->getUserId() % 10))->findByUserId($task_history->getUserId());
        $this->assertEquals(1, count($taskHistory2) - count($taskHistory1));
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
        $em = $this->em;
        $pointHistory1 = $em->getRepository('JiliApiBundle:PointHistory0' . ($userid % 10))->findAll();
        $controller->getPointHistory($userid, $point, $type);
        $pointHistory2 = $em->getRepository('JiliApiBundle:PointHistory0' . ($userid % 10))->findAll();
        $this->assertEquals(1, count($pointHistory2) - count($pointHistory1));

    }

    /**
     * @group HandleExchange
     * @group issue_535
     */
    public function testHandleExchange() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $client = static :: createClient();
        $em = $this->em;
        $user = LoadHandleExchangeWenData :: $USERS[0];

        for ($i = 0; $i < 3; $i++) {
            $exchange_id = LoadHandleExchangeWenData :: $POINTS_EXCHANGES[$i]->getId();

            $file[$i +1][0] = $exchange_id;
            $file[$i +1][1] = $user->getEmail();
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
