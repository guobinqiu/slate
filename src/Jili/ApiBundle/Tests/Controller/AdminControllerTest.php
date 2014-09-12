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
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
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
        if ($tn == 'testUpdateTaskHistory' || $tn == 'testgetTaskHistory') {
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

        if ($tn == 'testSelectTaskPercent') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadAdminSelectTaskPercentCodeData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }

        if ($tn == 'testExchangeOKWen') {
            $with_fixture = true;
            // load fixtures
            $fixture = new LoadUserData();
            $loader = new Loader();
            $loader->addFixture($fixture);
        }

        if (in_array($tn, array (
                'testInsertSendMs',
                'testupdateSendMs',
                'testselectSendMs',
                'testselectSendMsById',
                'testdelSendMs'
            ))) {
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
        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;

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
     */
    public function testdelSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;
        $controller->delSendMs($sm->getSendTo(), $sm->getId());
        $em = $this->em;
        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($sm->getSendTo() % 10))->find($sm->getId());
        $this->assertEquals(1, $sendMessage->getDeleteFlag());
    }

    /**
     * @group updateSendMs
     */
    public function testupdateSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;
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
     */
    public function testselectSendMsById() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;
        $user = LoadUserSendMessageData :: $USER;
        $return = $controller->selectSendMsById($sm->getSendTo(), $sm->getId());
        $this->assertEquals($user->getEmail(), $return['email']);
    }

    /**
     * @group selectSendMs
     */
    public function testselectSendMs() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $sm = LoadUserSendMessageData :: $SEND_MESSAGE;
        $user = LoadUserSendMessageData :: $USER;

        $return = $controller->selectSendMs($user->getId());
        $this->assertEquals($sm->getTitle(), $return[0]['title']);
    }

    /**
     * @group SelectTaskPercent
     */
    public function testSelectTaskPercent() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);
        $ta = LoadAdminSelectTaskPercentCodeData :: $TASK_HISTORY;
        $return = $controller->selectTaskPercent($ta->getUserId(), $ta->getOrderId());
        $this->assertEquals($ta->getRewardPercent(), $return['rewardPercent']);
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

        $task_history = LoadUserTaskHistoryData :: $TASK_HISTORY;

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
     * @group ExchangeOKWen
     */
    public function testExchangeOKWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $user = LoadUserData :: $USERS[0];

        $points = 120;

        $em = $this->em;
        $pointHistory1 = $em->getRepository('JiliApiBundle:PointHistory0' . ($user->getId() % 10))->findAll();

        $return = $controller->exchangeOKWen($user->getEmail(), $points);
        $this->assertTrue($return);

        $pointHistory2 = $em->getRepository('JiliApiBundle:PointHistory0' . ($user->getId() % 10))->findAll();
        $this->assertEquals(1, count($pointHistory2) - count($pointHistory1));

        $userInfo = $em->getRepository('JiliApiBundle:User')->findById($user->getId());
        $this->assertEquals($user->getPoints() + 120, $userInfo[0]->getPoints());
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