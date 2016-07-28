<?php
namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Services\ExpirePointService;
use Jili\ApiBundle\Entity\User;

class ExpirePointServiceTest extends WebTestCase
{

    private $em;

    private $application;

    private $expirePointService;

    private $container;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->application = new \Symfony\Bundle\FrameworkBundle\Console\Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->container = self::$kernel->getContainer();
        $this->expirePointService = static::$kernel->getContainer()->get('app.expire_point_service');
        $this->expirePointService->skipEmail(); // 因为是测试，所以不发邮件
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    /**
    * 测试发送系统通知邮件的功能
    * 会实际发邮件
    * 用来保证系统通知邮件的twig正确
    * 需要去看一下邮件
    */
    public function testSystemResultNotify(){
        $subject = '这是一个测试哦 [OK] ExpirePointCommand finished';

        $result30Days = array(
            'status' => 'succeeded',
            'errmsg' => '测试信息30Days',
            'targetUserCount' => 11,      // 总共应该通知的用户数目
            'notifyFailedUsers' => array( // 通知失败的用户信息
                                    array(
                                       'id'     => 1001,
                                       'email'  => 'test1001@email.com',
                                       'nick'   => 'test1001',
                                       'points' => 1001,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    array(
                                       'id'     => 1002,
                                       'email'  => 'test1002@email.com',
                                       'nick'   => 'test1002',
                                       'points' => 1002,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    )        
            );
        $result7Days = array(
            'status' => 'succeeded',
            'errmsg' => '测试信息7Days',
            'targetUserCount' => 21,      // 总共应该通知的用户数目
            'notifyFailedUsers' => array( // 通知失败的用户信息
                                    array(
                                       'id'     => 2001,
                                       'email'  => 'test2001@email.com',
                                       'nick'   => 'test2001',
                                       'points' => 2001,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    array(
                                       'id'     => 2002,
                                       'email'  => 'test2002@email.com',
                                       'nick'   => 'test2002',
                                       'points' => 2002,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    )        
            );
        $resultExpired = array(
            'status' => 'succeeded',
            'errmsg' => '测试信息Expired',
            'targetUserCount' => 31,      // 总共应该通知的用户数目
            'notifyFailedUsers' => array( // 通知失败的用户信息
                                    array(
                                       'id'     => 3001,
                                       'email'  => 'test3001@email.com',
                                       'nick'   => 'test3001',
                                       'points' => 3001,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    array(
                                       'id'     => 3002,
                                       'email'  => 'test3002@email.com',
                                       'nick'   => 'test3002',
                                       'points' => 3002,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    ),
            'totalExpiredPoints' => 6003, 
            'expireFailedUsers' => array( // 通知失败的用户信息
                                    array(
                                       'id'     => 3001,
                                       'email'  => 'test3001@email.com',
                                       'nick'   => 'test3001',
                                       'points' => 3001,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    array(
                                       'id'     => 3002,
                                       'email'  => 'test3002@email.com',
                                       'nick'   => 'test3002',
                                       'points' => 3002,
                                       'errmsg' => '该用户失败的原因'
                                        ),
                                    )    
            );

        $params = array();
        $params['result30Days'] = $result30Days;
        $params['result7Days'] = $result7Days;
        $params['resultExpired'] = $resultExpired;

        $this->expirePointService->sendEmail();
        $result = $this->expirePointService->systemResultNotify($subject, $params);

        $this->assertEquals(true, $result,'systemResultNotify should be true.');
    }


    /**
    *  
    * 测试notifyUserExpiringIn30Days 成功的分支
    * 不会发邮件以及真正积分清零
    */
    public function testNotifyUserExpiringIn30DaysSucceeded()
    {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $email1 = 'xiaoyi.chai@d8aspring.com';
        $nick1  = 'xiaoyi.chai';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P151D'));

        $email2 = 'xiaoyi_chai1@d8aspring.com';
        $nick2  = 'xiaoyi_chai1';
        $point2 = 5021;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P151D'));

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);
        $user1->setLastGetPointsAt($lastGetPointAt1);

        $user2 = new User();
        $user2->setNick($nick2);
        $user2->setEmail($email2);
        $user2->setPoints($point2);
        $user2->setIsInfoSet(0);
        $user2->setLastGetPointsAt($lastGetPointAt2);

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->flush();

        $result = $this->expirePointService->notifyUserExpiringIn30Days($baseDate);

        $this->assertEquals('succeeded', $result['status'], "The status of the result of notifyUserExpiringIn30Days should be succeeded.");
        $this->assertEquals(2, $result['targetUserCount'], "The targetUserCount of the result of notifyUserExpiringIn30Days should be 1.");
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers should be 0.(Sometimes will not be 0 because here we used email provider's system to send email)");
    }

    /**
    *  
    * 测试notifyUserExpiringIn30Days 失败的分支
    */
    public function testNotifyUserExpiringIn30DaysFailed()
    {
        $baseDate = date_create();

        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $result = $this->expirePointService->notifyUserExpiringIn30Days($baseDate);

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");

        $this->assertEquals('failed', $result['status'], "The status of the result of notifyUserExpiringIn30Days should be succeeded.");
        $this->assertTrue(isset($result['errmsg']), "The errmsg of the result of notifyUserExpiringIn30Days should not be null.");
    }

    /**
    *  
    * 测试notifyUserExpiringIn7Days 成功的分支
    */
    public function testNotifyUserExpiringIn7DaysSucceeded()
    {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $email1 = 'xiaoyi.chai@d8aspring.com';
        $nick1  = 'xiaoyi.chai';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P174D'));

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);
        $user1->setLastGetPointsAt($lastGetPointAt1);

        $this->em->persist($user1);
        $this->em->flush();

        $result = $this->expirePointService->notifyUserExpiringIn7Days($baseDate);

        $this->assertEquals('succeeded', $result['status'], "The status of the result of notifyUserExpiringIn7Days should be succeeded.");
        $this->assertEquals(1, $result['targetUserCount'], "The targetUserCount of the result of notifyUserExpiringIn7Days should be 1.");
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers should be 0.(Sometimes will not be 0 because here we used email provider's system to send email)");
    }

    /**
    *  
    * 测试notifyUserExpiringIn7Days 失败的分支
    */
    public function testNotifyUserExpiringIn7DaysFailed()
    {
        $baseDate = date_create();

        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $result = $this->expirePointService->notifyUserExpiringIn7Days($baseDate);

        $this->assertEquals('failed', $result['status'], "The status of the result of notifyUserExpiringIn7Days should be succeeded.");
        $this->assertTrue(isset($result['errmsg']), "The errmsg of the result of notifyUserExpiringIn7Days should not be null.");
        
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
    *  
    * 测试 notifyAndExpireUserExpired 成功分支
    */
    public function testNotifyAndExpireUserExpiredSucceeded()
    {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $email1 = 'xiaoyi.chai01@d8aspring.com';
        $nick1  = 'xiaoyi.chai01';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        $email2 = 'xiaoyi.chai02@d8aspring.com';
        $nick2  = 'xiaoyi.chai02';
        $point2 = 1032;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P182D'));

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);
        $user1->setLastGetPointsAt($lastGetPointAt1);

        $user2 = new User();
        $user2->setNick($nick2);
        $user2->setEmail($email2);
        $user2->setPoints($point2);
        $user2->setIsInfoSet(0);
        $user2->setLastGetPointsAt($lastGetPointAt2);

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->flush();

        $this->expirePointService->doExpiringPoint();
        $result = $this->expirePointService->notifyAndExpireUserExpired($baseDate);

        $this->assertEquals('succeeded', $result['status'], "The status of the result of notifyUserExpiringIn7Days should be succeeded.");
        $this->assertEquals(2, $result['targetUserCount'], "The targetUserCount of the result of notifyUserExpiringIn7Days should be 2.");
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers should be 0.(Sometimes will not be 0 because here we used email provider's system to send email)");
        $this->assertEquals($point1 + $point2, $result['totalExpiredPoints'], "The totalExpiredPoints should be ". $point1 + $point2 . ".");
    }

    /**
    *  
    * 测试 notifyAndExpireUserExpired 失败的分支
    */
    public function testNotifyAndExpireUserExpiredFailed()
    {
        $baseDate = date_create();

        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $result = $this->expirePointService->notifyUserExpiringIn7Days($baseDate);

        $this->assertEquals('failed', $result['status'], "The status of the result of notifyAndExpireUserExpired should be succeeded.");
        $this->assertTrue(isset($result['errmsg']), "The errmsg of the result of notifyAndExpireUserExpired should not be null.");
        
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
    * 检索过程出现异常 
    *（测试方法：检索对象的user表不存在）
    */
    public function testFindExpiringUsersFailed()
    {
        $baseDate = date_create();

        $lastPointGetAt = clone($baseDate);
        $lastPointGetAt->sub(new \DateInterval('P181D'));

        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $result = $this->expirePointService->findExpiringUsers($lastPointGetAt, $lastPointGetAt);

        $this->assertEquals('failed', $result['status'], "The status of the result of findExpireingUsers should be failed.");

        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

    /**
    * 指定时间范围内的用户成功被检索到
    */
    public function testFindExpiringUsersSucceeded()
    {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        $email1 = 'email1@d8aspring.com';
        $nick1  = 'email1';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        $email2 = 'email2@d8aspring.com';
        $nick2  = 'email2';
        $point2 = 5022;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P181D'));

        $email3 = 'email3@d8aspring.com';
        $nick3  = 'email3';
        $point3 = 5023;
        $lastGetPointAt3 = clone($baseDate);
        $lastGetPointAt3->sub(new \DateInterval('P181D'));

        $email4 = 'email4@d8aspring.com';
        $nick4  = 'email4';
        $point4 = 5023;
        $lastGetPointAt4 = clone($baseDate);
        $lastGetPointAt4->sub(new \DateInterval('P179D')); // 这个用户不该被检索到

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);
        $user1->setLastGetPointsAt($lastGetPointAt1);

        $user2 = new User();
        $user2->setNick($nick2);
        $user2->setEmail($email2);
        $user2->setPoints($point2);
        $user2->setIsInfoSet(0);
        $user2->setLastGetPointsAt($lastGetPointAt2);

        $user3 = new User();
        $user3->setNick($nick3);
        $user3->setEmail($email3);
        $user3->setPoints($point3);
        $user3->setIsInfoSet(0);
        $user3->setLastGetPointsAt($lastGetPointAt3);

        $user4 = new User();
        $user4->setNick($nick4);
        $user4->setEmail($email4);
        $user4->setPoints($point4);
        $user4->setIsInfoSet(0);
        $user4->setLastGetPointsAt($lastGetPointAt4);

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->persist($user3);
        $this->em->persist($user4);
        $this->em->flush();

        $from = clone($baseDate);
        $from->sub(new \DateInterval('P190D'));
        $to = clone($baseDate);
        $to->sub(new \DateInterval('P180D'));

        $result = $this->expirePointService->findExpiringUsers($from, $to);

        $this->assertEquals('suceeded', $result['status'], "The status of the result of findExpireingUsers should be failed.");
        $this->assertEquals(3, sizeof($result['expiringUsers']), 'The count of expiringUsers should be 0.');
    }

    /**
    * 通知用户成功
    * 注意： 这里会发三封邮件
    */
    public function testNotifyExpiringUsersSucceeded()
    {
        $expiringUsers = array(
            array(
                'id' => 1001,
                'email' => 'rpa-sys-china@d8aspring.com',
                'nick' => '[测试] rpa-sys-china',
                'points' => 5023,
                )
            );

        $this->expirePointService->sendEmail(); // 这里要测到发邮件的模板是否正确，所以开放实际发邮件的功能

        $result = $this->expirePointService->notifyExpiringUsers($expiringUsers, ExpirePointService::TITLE_FORMAT_EXPIRING_30D, ExpirePointService::TEMPLATE_EXPIRING_30D);
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers of 30D should be 0.");

        $result = $this->expirePointService->notifyExpiringUsers($expiringUsers, ExpirePointService::TITLE_FORMAT_EXPIRING_07D, ExpirePointService::TEMPLATE_EXPIRING_07D);
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers of 07D should be 0.");

        $result = $this->expirePointService->notifyExpiringUsers($expiringUsers, ExpirePointService::TITLE_FORMAT_EXPIRED, ExpirePointService::TEMPLATE_EXPIRED);
        $this->assertEquals(0, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers of EXPIRED should be 0.");
    }

    /**
    * 邮件格式不对导致通知（邮件）用户失败
    */
    public function testNotifyExpiringUsersFailed()
    {
        $expiringUsers = array(
            array(
                'id' => 1002,
                'email' => 'xiaoyi.chai_d8aspring.com', // 邮件格式不对
                'nick' => 'xiaoyi.chai',
                'points' => 5024,
                )
            );

        $this->expirePointService->sendEmail(); // 这里要测到发邮件失败的逻辑，所以开放实际发邮件的功能
        $result = $this->expirePointService->notifyExpiringUsers($expiringUsers, ExpirePointService::TITLE_FORMAT_EXPIRING_30D, ExpirePointService::TEMPLATE_EXPIRING_30D);

        $this->assertEquals(1, sizeof($result['notifyFailedUsers']), "The size of notifyFailedUsers should be 1.");
    }

    /**
    * 成功将指定用户的积分清零
    */
    public function testExpireExpiringUsersSucceeded()
    {
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $email1 = 'email1@d8aspring.com';
        $nick1  = 'email1';
        $point1 = 5021;

        $email2 = 'email2@d8aspring.com';
        $nick2  = 'email2';
        $point2 = 5022;

        $email3 = 'email3@d8aspring.com';
        $nick3  = 'email3';
        $point3 = 5023;

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);

        $user2 = new User();
        $user2->setNick($nick2);
        $user2->setEmail($email2);
        $user2->setPoints($point2);
        $user2->setIsInfoSet(0);

        $user3 = new User();
        $user3->setNick($nick3);
        $user3->setEmail($email3);
        $user3->setPoints($point3);
        $user3->setIsInfoSet(0);

        $this->em->persist($user1);
        $this->em->persist($user2);
        $this->em->persist($user3);
        $this->em->flush();

        $expiringUsers = array(
            array(
                'id' => $user1->getId(),
                'email' => $user1->getEmail(),
                'nick' => $user1->getNick(),
                'points' => $user1->getPoints(),
                ),
            array(
                'id' => $user2->getId(),
                'email' => $user2->getEmail(),
                'nick' => $user2->getNick(),
                'points' => $user2->getPoints(),
                ),
            array(
                'id' => $user3->getId(),
                'email' => $user3->getEmail(),
                'nick' => $user3->getNick(),
                'points' => $user3->getPoints(),
                )
            );

        $this->expirePointService->doExpiringPoint(); // 这里要测试数据库相关操作，所以开放积分清零相关操作
        $result = $this->expirePointService->expireExpiringUsers($expiringUsers);

        $expectedExpiredPoints = $point1 + $point2 + $point3;
        $this->assertEquals($expectedExpiredPoints, $result['totalExpiredPoints'], "The size of totalExpiredPoints should be ". $expectedExpiredPoints);
        $this->assertEquals(0, sizeof($result['expireFailedUsers']), "The size of expireFailedUsers should be 0.");
        $this->assertEquals(0, $user1->getPoints(), 'The points of user1 should be 0 after expireExpiringUsers.');
        $this->assertEquals(0, $user2->getPoints(), 'The points of user2 should be 0 after expireExpiringUsers.');
        $this->assertEquals(0, $user3->getPoints(), 'The points of user3 should be 0 after expireExpiringUsers.');
    }

    /**
    * user to update is not exist
    */
    public function testExpireExpiringUsersFailed1()
    {
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $email1 = 'email1@d8aspring.com';
        $nick1  = 'email1';
        $point1 = 5021;

        $email2 = 'email2@d8aspring.com';
        $nick2  = 'email2';
        $point2 = 5022;

        $email3 = 'email3@d8aspring.com';
        $nick3  = 'email3';
        $point3 = 5023;

        $expiringUsers = array(
            array(
                'id' => 1,
                'email' => $email1,
                'nick' => $nick1,
                'points' => $point1,
                ),
            array(
                'id' => 2,
                'email' => $email2,
                'nick' => $nick2,
                'points' => $point2,
                ),
            array(
                'id' => 3,
                'email' => $email3,
                'nick' => $nick3,
                'points' => $point3,
                )
            );

        $this->expirePointService->doExpiringPoint(); // 这里要测试数据库相关操作，所以开放积分清零相关操作
        $result = $this->expirePointService->expireExpiringUsers($expiringUsers);

        $this->assertEquals(0, $result['totalExpiredPoints'], "The size of totalExpiredPoints should be 0.");
        $this->assertEquals(3, sizeof($result['expireFailedUsers']), "The size of expireFailedUsers should be 3.");
    }

    /**
    * user table is not exist
    */
    public function testExpireExpiringUsersFailed2()
    {
        // 删掉所有表
        $this->runConsole("doctrine:schema:drop", array("--force" => true));

        $email1 = 'email1@d8aspring.com';
        $nick1  = 'email1';
        $point1 = 5021;

        $email2 = 'email2@d8aspring.com';
        $nick2  = 'email2';
        $point2 = 5022;

        $email3 = 'email3@d8aspring.com';
        $nick3  = 'email3';
        $point3 = 5023;

        $expiringUsers = array(
            array(
                'id' => 1,
                'email' => $email1,
                'nick' => $nick1,
                'points' => $point1,
                ),
            array(
                'id' => 2,
                'email' => $email2,
                'nick' => $nick2,
                'points' => $point2,
                ),
            array(
                'id' => 3,
                'email' => $email3,
                'nick' => $nick3,
                'points' => $point3,
                )
            );

        $this->expirePointService->doExpiringPoint(); // 这里要测试数据库相关操作，所以开放积分清零相关操作
        $result = $this->expirePointService->expireExpiringUsers($expiringUsers);

        $this->assertEquals(0, $result['totalExpiredPoints'], "The size of totalExpiredPoints should be 0.");
        $this->assertEquals(3, sizeof($result['expireFailedUsers']), "The size of expireFailedUsers should be 3.");
        // 测试结束，恢复所有表
        // 建立所有表
        $this->runConsole("doctrine:schema:create");
    }

}
