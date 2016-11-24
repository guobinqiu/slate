<?php
namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\Entity\OfferwowOrder;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Services\OfferwowRequestService;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

class OfferwowRequestServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $offerwowRequestService;

    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = self::$kernel->getContainer();
        $this->offerwowRequestService = static::$kernel->getContainer()->get('app.offerwow_request_service');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testValidateParamsNotExist()
    {
        // memberid 为空
        $memberid = NULL;
        $point = '100'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $websiteid = '97'; // 随意，有值就好
        $immediate = '0'; // 随意，有值就好
        $sign = 'xafsdfads'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);


        // point 为空
        $memberid = '123'; // 随意，有值就好
        $point = NULL;
        $eventid = '1002'; // 随意，有值就好
        $websiteid = '97'; // 随意，有值就好
        $immediate = '0'; // 随意，有值就好
        $sign = 'xafsdfads'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);

        // eventid 为空
        $memberid = '123';
        $point = '100';
        $eventid = NULL;
        $websiteid = '97'; // 随意，有值就好
        $immediate = '0'; // 随意，有值就好
        $sign = 'xafsdfads'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);

        // websiteid 为空
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1004'; // 随意，有值就好
        $websiteid = NULL;
        $immediate = '0'; // 随意，有值就好
        $sign = 'xafsdfads'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);

        // websiteid 为空
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1005'; // 随意，有值就好
        $websiteid = '97'; // 随意，有值就好
        $immediate = NULL; 
        $sign = 'xafsdfads'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);

        // sign 为空
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1006'; // 随意，有值就好
        $websiteid = '97'; // 随意，有值就好
        $immediate = '0'; // 随意，有值就好
        $sign = NULL; 

        $expect_status = 'failure';
        $expect_errno = 'offerwow-01';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);
    }

    public function testValidateParamsWebsiteidNotCorrect()
    {
        // websiteid 跟配置文件中的 websiteid不一致
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1011'; // 随意，有值就好
        $websiteid = '97'; // 随意，跟配置文件中的offerwow_com.websiteid的值不一致就行
        $immediate = '0'; // 随意，有值就好
        $sign = 'ahaha'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'offerwow-02';
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);
    }

    public function testValidateParamsSignNotCorrect()
    {
        // sign 不对
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1021'; // 随意，有值就好
        //$websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $websiteid = '1162'; // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '0'; // 随意，有值就好
        $sign = 'ahaha'; // 随意，有值就好

        $expect_status = 'failure';
        $expect_errno = 'signature error';
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);
    }

    public function testValidateParamsMemberidNotExist()
    {
        // prepare test datas
        // sign 不对
        $memberid = '123'; // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1031'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '0'; // 随意，有值就好
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        // expectations
        $expect_status = 'failure';
        $expect_errno = 'offerwow-03';

        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno']);
    }

    public function testValidateParamsExistImmediate0()
    {
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        // prepare test datas
        // sign 不对
        $memberid = $user->getId(); // 配合测试数据，先生成一条user数据，然后取出user_id
        $point = '100'; // 随意，有值就好
        $eventid = '1041'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '0'; // 0
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($memberid); 
        $offerwowOrder->setEventid($eventid);
        $offerwowOrder->setStatus($immediate); 
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        $this->em->persist($offerwowOrder);
        $this->em->flush();
        $this->em->clear();

        // execute test
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'failure';
        $expect_errno = 'offerwow-05';
        $this->assertEquals($expect_status, $result['status'], 'status is not correct');
        $this->assertEquals($expect_errno, $result['errno'], 'errno is not correct');
    }

    public function testValidateParamsExistImmediate1()
    {
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        // prepare test datas
        // sign 不对
        $memberid = $user->getId(); // 配合测试数据，先生成一条user数据，然后取出user_id
        $point = '100'; // 随意，有值就好
        $eventid = '1051'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '1'; // 1
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($memberid); 
        $offerwowOrder->setEventid($eventid);
        $offerwowOrder->setStatus(OfferwowRequestService::convertStatus($immediate)); 
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        $this->em->persist($offerwowOrder);
        $this->em->flush();
        $this->em->clear();

        // execute test
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'failure';
        $expect_errno = 'offerwow-04';
        $this->assertEquals($expect_status, $result['status'], 'status is not correct');
        $this->assertEquals($expect_errno, $result['errno'], 'errno is not correct');
    }

    public function testValidateParamsExistImmediate2()
    {
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();


        // prepare test datas
        // sign 不对
        $memberid = $user->getId(); // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1061'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '2'; // 2
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($memberid); 
        $offerwowOrder->setEventid($eventid);
        $offerwowOrder->setStatus(OfferwowRequestService::convertStatus('1'));   // 不能是0
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        $this->em->persist($offerwowOrder);
        $this->em->flush();
        $this->em->clear();

        // execute test
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'failure';
        $expect_errno = 'offerwow-04';
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno'], 'errno is not correct');
    }

    public function testValidateParamsExistImmediate3()
    {
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        // prepare test datas
        // sign 不对
        $memberid = $user->getId(); // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '3'; // immediate 3
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($memberid); 
        $offerwowOrder->setEventid($eventid);
        $offerwowOrder->setStatus(OfferwowRequestService::convertStatus('1'));   // 不能是0
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        
        $this->em->persist($offerwowOrder);
        $this->em->flush();
        $this->em->clear();

        // execute test
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'failure';
        $expect_errno = 'offerwow-06';
        $this->assertEquals($expect_status, $result['status']);
        $this->assertEquals($expect_errno, $result['errno'], 'errno is not correct');
    }

    public function testValidateParamsOK()
    {
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        // prepare test datas
        // sign 不对
        $memberid = $user->getId(); // 随意，有值就好
        $point = '100'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $websiteid = $this->container->getParameter('offerwow_com.websiteid'); // 跟配置文件中的offerwow_com.websiteid的值一致
        $immediate = '3'; // immediate 3
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        

        // execute test OK 1 offerwow_order不存在
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'success';
        $this->assertEquals($expect_status, $result['status']);
        // ----------------------------------

        // prepare test datas
        $happen_time = date_create();
        $offerwowOrder = new OfferwowOrder();
        $offerwowOrder->setUserid($memberid); 
        $offerwowOrder->setEventid($eventid);
        $offerwowOrder->setStatus(OfferwowRequestService::convertStatus('0'));   
        $offerwowOrder->setHappenedAt($happen_time);
        $offerwowOrder->setCreatedAt($happen_time);
        $offerwowOrder->setDeleteFlag(0);

        
        $this->em->persist($offerwowOrder);
        $this->em->flush();

        $immediate = '2'; // immediate 2
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        // execute test OK 2 offerwow_order存在,数据回传的immediate是2，已存在offerwow_order的status是0
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'success';
        $this->assertEquals($expect_status, $result['status']);
        // ----------------------------------

        // prepare test datas
        $immediate = '3'; // immediate 3
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->container->getParameter('offerwow_com.key')
            );
        $sign = strtoupper(md5(implode($hash))); // 计算md5的sign

        // execute test OK 3 offerwow_order存在,数据回传的immediate是3，已存在offerwow_order的status是0
        $result = $this->offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);

        // expectations
        $expect_status = 'success';
        $this->assertEquals($expect_status, $result['status']);
    }

    public function testProcessEventImmediate0()
    {
        // 数据回传 immediate = 0
        // 没有已经存在的offerwow_order
        // 新建一个offerwow_order 和 task_history
     
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints(100);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        // prepare test datas
        // sign 不对
        $oldTime = $user->getLastGetPointsAt();
        $memberid = $user->getId(); 
        $point = '100'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $immediate = '0'; // immediate 0
        $programname = '任务TEST';


        // 执行测试对象函数
        $result = $this->offerwowRequestService->processEvent($memberid, $point, $eventid, $immediate, $programname);
        $offerwow_order = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        $taskRepository = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ($memberid % 10));
        $taskHistory = $taskRepository->findOneBy(array( 'orderId'=> $offerwow_order->getId(),'taskType'=> TaskType::CPA) );

        // 检查数据
        $this->assertTrue($result, 'eventid=[' . $eventid . '] is not properly processed.');
        $this->assertTrue(!is_null($offerwow_order),'eventid=[' . $eventid . '] offerwow_order is not existed.');
        $this->assertEquals($memberid, $offerwow_order->getUserId());
        $this->assertEquals($eventid, $offerwow_order->getEventId());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $offerwow_order->getStatus());

        $this->assertTrue(is_null($taskHistory),'eventid=[' . $eventid . '] task_history is not existed.');
        $this->assertTrue(($user->getLastGetPointsAt() == $oldTime), 'lastGetPointsAt should not be changed');
    }

    public function testProcessEventImmediate1()
    {
        // 数据回传 immediate = 1
        // 没有已经存在的offerwow_order
        // 新建一个offerwow_order 和 task_history point_history
        // 更新 user

        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        // 准备user数据
        // prepare test datas
        // 该用户的现有积分数
        $current_point = 100;

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints($current_point);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
        
        // sign 不对
        $oldTime = $user->getLastGetPointsAt();
        $memberid = $user->getId(); // 随意，有值就好
        $point = '152'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $immediate = '1'; // immediate 3
        // programname不存在的时候，task_history.task_name = eventid
        $programname = NULL;

    
        // 执行测试对象函数
        $result = $this->offerwowRequestService->processEvent($memberid, $point, $eventid, $immediate, $programname);
        $offerwow_order = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        $taskRepository = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ($memberid % 10));
        $taskHistory = $taskRepository->findOneBy(array( 'orderId'=> $offerwow_order->getId(),'taskType'=> TaskType::CPA) );
        $pointHistoryRepository = $this->em->getRepository('JiliApiBundle:PointHistory0'. ($memberid % 10));
        $pointHistory = $pointHistoryRepository->findOneByUserId($memberid);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($memberid);

        // 检查数据
        $this->assertTrue($result, 'eventid=[' . $eventid . '] is not properly processed.');
        $this->assertTrue(!is_null($offerwow_order),'eventid=[' . $eventid . '] offerwow_order is not existed.');
        $this->assertEquals($memberid, $offerwow_order->getUserId());
        $this->assertEquals($eventid, $offerwow_order->getEventId());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $offerwow_order->getStatus());

        $this->assertTrue(!is_null($taskHistory),'eventid=[' . $eventid . '] task_history is not existed.');
        $this->assertEquals($memberid, $taskHistory->getUserId());
        $this->assertEquals($eventid, $taskHistory->getTaskName());
        $this->assertEquals($point, $taskHistory->getPoint());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $taskHistory->getStatus());

        $this->assertTrue(!is_null($pointHistory),'eventid=[' . $eventid . '] point_history is not existed.');
        $this->assertEquals($point, $pointHistory->getPointChangeNum());
        $this->assertEquals(CategoryType::OFFERWOW_COST, $pointHistory->getReason());

        $this->assertEquals($current_point+$point, $user->getPoints());
        $this->assertTrue(($user->getLastGetPointsAt() > $oldTime), 'lastGetPointsAt should be updated');
    }

    public function testProcessEventImmediate2()
    {
        // 数据回传 immediate = 2
        // 已经存在对应eventid的offerwow_order
        // 新建一个offerwow_order 和 task_history point_history
        // 更新 user
        
        // prepare test datas
        // 该用户的现有积分数
        $current_point = 100;

        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints($current_point);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        
        // sign 不对
        $oldTime = $user->getLastGetPointsAt();
        $memberid = $user->getId(); // 随意，有值就好
        $point = '152'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $immediate = '2'; // immediate 3
        $programname = '任务TEST';

        

        // 执行测试对象函数
        $result = $this->offerwowRequestService->processEvent($memberid, $point, $eventid, $immediate, $programname);
        $offerwow_order = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        $taskRepository = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ($memberid % 10));
        $taskHistory = $taskRepository->findOneBy(array( 'orderId'=> $offerwow_order->getId(),'taskType'=> TaskType::CPA) );
        $pointHistoryRepository = $this->em->getRepository('JiliApiBundle:PointHistory0'. ($memberid % 10));
        $pointHistory = $pointHistoryRepository->findOneByUserId($memberid);

        // 检查数据
        $this->assertTrue($result, 'eventid=[' . $eventid . '] is not properly processed.');
        $this->assertTrue(!is_null($offerwow_order),'eventid=[' . $eventid . '] offerwow_order is not existed.');
        $this->assertEquals($memberid, $offerwow_order->getUserId());
        $this->assertEquals($eventid, $offerwow_order->getEventId());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $offerwow_order->getStatus());

        $this->assertTrue(!is_null($taskHistory),'eventid=[' . $eventid . '] task_history is not existed.');
        $this->assertEquals($memberid, $taskHistory->getUserId());
        $this->assertEquals($programname, $taskHistory->getTaskName());
        $this->assertEquals($point, $taskHistory->getPoint());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $taskHistory->getStatus());

        $this->assertTrue(!is_null($pointHistory),'eventid=[' . $eventid . '] point_history is not existed.');
        $this->assertEquals($point, $pointHistory->getPointChangeNum());
        $this->assertEquals(CategoryType::OFFERWOW_COST, $pointHistory->getReason());

        $this->assertEquals($current_point+$point, $user->getPoints(), 'user point is not properly updated');
        $this->assertTrue(($user->getLastGetPointsAt() > $oldTime), 'lastGetPointsAt should be updated');
    }

    public function testProcessEventImmediate3()
    {
        // 数据回传 immediate = 3
        // 已经存在对应eventid的offerwow_order
        // 新建一个offerwow_order 和 task_history point_history
        // 更新 user
        
        // prepare test datas
        // 该用户的现有积分数
        $current_point = 100;
        // 清空数据库
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        // 准备user数据
        $user = new User();
        $user->setNick('test');
        $user->setEmail('test@test.com');
        $user->setPoints($current_point);
        $user->setIconPath('test');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        
        // sign 不对
        $oldTime = $user->getLastGetPointsAt();
        $memberid = $user->getId(); // 随意，有值就好
        $point = '152'; // 随意，有值就好
        $eventid = '1001'; // 随意，有值就好
        $immediate = '3'; // immediate 3
        $programname = '任务TEST';

        // 执行测试对象函数
        $result = $this->offerwowRequestService->processEvent($memberid, $point, $eventid, $immediate, $programname);
        $offerwow_order = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        $taskRepository = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ($memberid % 10));
        $taskHistory = $taskRepository->findOneBy(array( 'orderId'=> $offerwow_order->getId(),'taskType'=> TaskType::CPA) );
        $pointHistoryRepository = $this->em->getRepository('JiliApiBundle:PointHistory0'. ($memberid % 10));
        $pointHistory = $pointHistoryRepository->findOneByUserId($memberid);

        // 检查数据
        $this->assertTrue($result, 'eventid=[' . $eventid . '] is not properly processed.');
        $this->assertTrue(!is_null($offerwow_order),'eventid=[' . $eventid . '] offerwow_order is not existed.');
        $this->assertEquals($memberid, $offerwow_order->getUserId());
        $this->assertEquals($eventid, $offerwow_order->getEventId());
        $this->assertEquals(OfferwowRequestService::convertStatus($immediate), $offerwow_order->getStatus());

        $this->assertTrue(is_null($taskHistory),'eventid=[' . $eventid . '] task_history is not existed.');

        $this->assertTrue(is_null($pointHistory),'eventid=[' . $eventid . '] point_history exist.');

        $this->assertEquals($current_point, $user->getPoints(), 'user point is changed');
        $this->assertTrue(($user->getLastGetPointsAt() == $oldTime), 'lastGetPointsAt should not be changed');
    }

}
