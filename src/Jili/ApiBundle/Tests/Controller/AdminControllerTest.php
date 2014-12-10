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
     * @author mmzhang
     * @group HandleExchangeWen
     * @group issue_492
     * @group issue_535
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
        $user_crosss = LoadHandleExchangeWenData :: $USER_WENWEN_CROSS;
        $exchange = LoadHandleExchangeWenData :: $EXCHANGE_FROM_WENWEN;

        //测试有关表user,exchange_from_wenwen
        $file[1][0] = $exchange->getWenwenExchangeId();
        $file[1][1] = $user_crosss[0]->getId();
        $file[1][2] = '30';
        $file[1][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($exchange->getWenwenExchangeId() . '已发放', $return[0]);

        $file = array ();
        $file[2][0] = '91jili-201402-2625-1036110';
        $file[2][1] = $user_crosss[0]->getId() + 100;
        $file[2][2] = '30';
        $file[2][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($file[2][0] . '兑换失败', $return[0]);
        $exchangeFromWenwen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($file[2][0]);
        $this->assertEquals('account not exists', $exchangeFromWenwen[0]->getReason());

        $file = array ();
        $file[3][0] = '91jili-201402-2625-1036111';
        $file[3][1] = $user_crosss[1]->getId();
        $file[3][2] = '30';
        $file[3][3] = '3000';
        $return = $controller->handleExchangeWen($file);
        $this->assertEquals($file[3][0] . '兑换失败', $return[0]);
        $exchangeFromWenwen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($file[3][0]);
        $this->assertEquals('账号没有激活', $exchangeFromWenwen[0]->getReason());

        $file = array ();
        $file[4][0] = '91jili-201402-2625-1036112';
        $file[4][1] = $user_crosss[2]->getId();
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
     * @group ExchangeOKWen
       @group issue_535
     */
    public function testExchangeOKWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $user = LoadHandleExchangeWenData :: $USERS[0];

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
       @group issue_535
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

    /**
     * @group insertExWenwen
     * @group issue_492
     * @group issue_535
     */
    public function testInsertExWenwen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);
        $em = $this->em;

        $user_cross = LoadHandleExchangeWenData :: $USER_WENWEN_CROSS[0];
        $user = $em->getRepository('JiliApiBundle:User')->getUserByCrossId($user_cross->getId());

        $wenwenExId = '123456789';

        $array = array (
            'wenwenExId' => $wenwenExId,
            'userId' => $user['id'],
            'email' => $user['email'],
            'cross_id' => $user_cross->getId(),
            'points' => 100,
            'status' => 1
        );

        $exchange_0 = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($wenwenExId);
        $controller->insertExWenwen($array);
        $exchange_1 = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($wenwenExId);
        $this->assertEquals(1, count($exchange_1) - count($exchange_0));
    }

    /**
     * @group insertFailExWenwen
     * @group issue_492
     * @group issue_535
     */
    public function testInsertFailExWenwen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $user_cross = LoadHandleExchangeWenData :: $USER_WENWEN_CROSS[0];

        $array = array (
            'wenwenExId' => "987654321",
            'cross_id' => $user_cross->getId(),
            'userId' => null,
            'email' => null,
            'points' => 100,
            'reason' => 'account not exists'
        );
        $return = $controller->insertFailExWenwen($array);
        $this->assertTrue($return);
    }

    /**
     * @group issue_535
     * @group insertSuccessExWenwen
     */
    public function testInsertSuccessExWenwen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $em = $this->em;

        $user_crosss = LoadHandleExchangeWenData :: $USER_WENWEN_CROSS;

        $wenwenExId = "issue535_123456_1";

        $cross_id = $user_crosss[0]->getId();
        $user_id = $user_crosss[0]->getUserId();
        $points = 200;
        $user = $em->getRepository('JiliApiBundle:User')->getUserByCrossId($cross_id);

        $user_0 = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
        $pointHistory_0 = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $sendMessage_0 = $em->getRepository('JiliApiBundle:SendMessage0' . ($user_id % 10))->findOneBySendTo($user_id);

        //测试提交成功
        $return = $controller->insertSuccessExWenwen($wenwenExId, $cross_id, $points);
        $this->assertTrue($return);
        //check 4 张表：  ExchangeFromWenwen，User，PointHistory，SendMessage
        //测试事务，需要数据
        $em->clear();
        $user_1 = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
        $this->assertEquals($points, ($user_1->getPoints() - $user_0->getPoints()));
        $exchange_1 = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($wenwenExId);
        $this->assertEquals(1, count($exchange_1));
        $pointHistory_1 = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(1, count($pointHistory_1) - count($pointHistory_0));
        $sendMessage_1 = $em->getRepository('JiliApiBundle:SendMessage0' . ($user_id % 10))->findBySendTo($user_id);
        $this->assertEquals(1, count($sendMessage_1) - count($sendMessage_0));

        //测试回滚
        $return = $controller->insertSuccessExWenwen($wenwenExId, $cross_id, $points);
        $this->assertFalse($return);
        //check 4 张表：  ExchangeFromWenwen，User，PointHistory，SendMessage
        //测试事务，需要数据
        $user_2 = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
        $this->assertEquals(0, ($user_2->getPoints() - $user_1->getPoints()));
        $exchange_2 = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->findByWenwenExchangeId($wenwenExId);
        $this->assertEquals(0, count($exchange_2) - count($exchange_1));
        $pointHistory_2 = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(0, count($pointHistory_2) - count($pointHistory_1));
        $sendMessage_2 = $em->getRepository('JiliApiBundle:SendMessage0' . ($user_id % 10))->findBySendTo($user_id);
        $this->assertEquals(0, count($sendMessage_2) - count($sendMessage_1));
    }

    /**
     * @group issue_535
     * @group testCheckExchangeWen
     */
    public function testCheckExchangeWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $em = $this->em;

        $user_crosss = LoadHandleExchangeWenData :: $USER_WENWEN_CROSS;

        $wenwenExId = "issue535_123456_1";
        $cross_id = $user_crosss[0]->getId();
        $points = 200;
        $return = $controller->insertSuccessExWenwen($wenwenExId, $cross_id, $points);
        $this->assertTrue($return);

        $wenwenExId = "issue535_123456_1";

        $message = $controller->checkExchangeWen($cross_id, $wenwenExId, $points);
        $this->assertEquals($wenwenExId."已发放", $message);

        $wenwenExId = "issue535_123456_2";
        $cross_id = 100;
        $message = $controller->checkExchangeWen($cross_id, $wenwenExId, $points);

        $this->assertEquals($wenwenExId."兑换失败", $message);

        $wenwenExId = "issue535_123456_3";
        $cross_id = $user_crosss[1]->getId();
        $message = $controller->checkExchangeWen($cross_id, $wenwenExId, $points);
        $this->assertEquals($wenwenExId."兑换失败", $message);
    }

    /**
     * @group issue_535
     * @group testGetContentForExportExchangWen
     */
    public function testGetContentForExportExchangWen() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminController();
        $controller->setContainer($container);

        $exchange = LoadHandleExchangeWenData :: $EXCHANGE_FROM_WENWEN;
        $em = $this->em;

        $start_time = null;
        $end_time = null;
        $exFrWen = $this->em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time, $end_time);
        $content = $controller->getContentForExportExchangWen($exFrWen);

        $export = explode("\n", $content);
        $this->assertEquals('exchange_id,91jili_cross_id,payment_amount,payment_point,status,reason,create_time,91jili_email', $export[0]);
        $item = explode(',', $export[1]);
        $this->assertEquals(8, count($item));
        $this->assertEquals($exchange->getWenwenExchangeId(), $item[0]);
    }

    /**
     * @group issue_560
     * @group debug
     */
    public function testAddPointManageAction(){
        $client = static :: createClient();

        $url = '/admin/pointManage';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}