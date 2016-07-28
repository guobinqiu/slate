<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\Entity\User;
use Wenwen\FrontendBundle\Command\ExpirePointCommand;

class ExpirePointCommandTest extends WebTestCase {

    private $em;

    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
    * 测试
    * --baseDate=now
    * 非realMode
    * 不发邮件，不做数据更新
    */
    public function testExpirePointCommandBasedateNow() {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        // 超过180天未获得过积分的用户
        $email1 = 'xiaoyi.chai01@d8aspring.com';
        $nick1  = 'xiaoyi.chai01';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        // 连续173天未获得过积分的用户
        $email2 = 'xiaoyi.chai02@d8aspring.com';
        $nick2  = 'xiaoyi.chai02';
        $point2 = 1032;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P173D'));

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

        $application = new Application(static::$kernel);
        $application->add(new ExpirePointCommand());

        $command = $application->find('point:expire');
        $commandTester = new CommandTester($command);
        // 测试时不指定 --realMode
        // 不会发邮件，不会积分清零
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--baseDate' => 'now',
            )
        );

        $this->assertEquals($point1, $user1->getPoints(), 'User1s point should stay.' );
        $this->assertEquals($point2, $user2->getPoints(), 'User2s point should stay.' );
    }

    /**
    * 测试
    * --baseDate=2016-07-26
    * 非realMode
    * 不发邮件，不做数据更新
    */
    public function testExpirePointCommandBasedateDate() {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        // 超过180天未获得过积分的用户
        $email1 = 'xiaoyi.chai01@d8aspring.com';
        $nick1  = 'xiaoyi.chai01';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        // 连续173天未获得过积分的用户
        $email2 = 'xiaoyi.chai02@d8aspring.com';
        $nick2  = 'xiaoyi.chai02';
        $point2 = 1032;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P173D'));

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

        $application = new Application(static::$kernel);
        $application->add(new ExpirePointCommand());

        $command = $application->find('point:expire');
        $commandTester = new CommandTester($command);
        // 测试时不指定 --realMode
        // 不会发邮件，不会积分清零
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--baseDate' => '2016-07-26',
            )
        );

        $this->assertEquals($point1, $user1->getPoints(), 'User1s point should stay.' );
        $this->assertEquals($point2, $user2->getPoints(), 'User2s point should stay.' );
    }

    /**
    * 测试
    * --baseDate 没设置
    * 非realMode
    * 不发邮件，不做数据更新
    */
    public function testExpirePointCommandBasedateNoDate() {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        // 超过180天未获得过积分的用户
        $email1 = 'xiaoyi.chai01@d8aspring.com';
        $nick1  = 'xiaoyi.chai01';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        // 连续173天未获得过积分的用户
        $email2 = 'xiaoyi.chai02@d8aspring.com';
        $nick2  = 'xiaoyi.chai02';
        $point2 = 1032;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P173D'));

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

        $application = new Application(static::$kernel);
        $application->add(new ExpirePointCommand());

        $command = $application->find('point:expire');
        $commandTester = new CommandTester($command);
        // 测试时不指定 --realMode
        // 不会发邮件，不会积分清零
        $commandTester->execute(
            array(
                'command' => $command->getName(),
            )
        );

        $this->assertEquals($point1, $user1->getPoints(), 'User1s point should stay.' );
        $this->assertEquals($point2, $user2->getPoints(), 'User2s point should stay.' );
    }

    /**
    * 测试
    * --baseDate=20160726
    * 非realMode
    * 不发邮件，不做数据更新
    */
    public function testExpirePointCommandBasedateDateFormatNG() {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        // 超过180天未获得过积分的用户
        $email1 = 'xiaoyi.chai01@d8aspring.com';
        $nick1  = 'xiaoyi.chai01';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        // 连续173天未获得过积分的用户
        $email2 = 'xiaoyi.chai02@d8aspring.com';
        $nick2  = 'xiaoyi.chai02';
        $point2 = 1032;
        $lastGetPointAt2 = clone($baseDate);
        $lastGetPointAt2->sub(new \DateInterval('P173D'));

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

        $application = new Application(static::$kernel);
        $application->add(new ExpirePointCommand());

        $command = $application->find('point:expire');
        $commandTester = new CommandTester($command);
        // 测试时不指定 --realMode
        // 不会发邮件，不会积分清零
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--baseDate' => 'now',
            )
        );

        $this->assertEquals($point1, $user1->getPoints(), 'User1s point should stay.' );
        $this->assertEquals($point2, $user2->getPoints(), 'User2s point should stay.' );
    }

    /**
    * 测试
    * --baseDate=20160726
    * realMode
    * 发邮件，数据更新
    */
    public function testExpirePointCommandBasedateRealMode() {
        $baseDate = date_create();

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();
        
        // 超过180天未获得过积分的用户
        $email1 = 'rpa-sys-china@d8aspring.com';
        $nick1  = 'rpa-sys-china';
        $point1 = 5021;
        $lastGetPointAt1 = clone($baseDate);
        $lastGetPointAt1->sub(new \DateInterval('P181D'));

        $user1 = new User();
        $user1->setNick($nick1);
        $user1->setEmail($email1);
        $user1->setPoints($point1);
        $user1->setIsInfoSet(0);
        $user1->setLastGetPointsAt($lastGetPointAt1);

        $this->em->persist($user1);
        $this->em->flush();

        $application = new Application(static::$kernel);
        $application->add(new ExpirePointCommand());

        $command = $application->find('point:expire');
        $commandTester = new CommandTester($command);
        // 测试时不指定 --realMode
        // 不会发邮件，不会积分清零
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--baseDate' => 'now',
                '--realMode' => true,
            )
        );

        $this->assertEquals(0, $user1->getPoints(), 'User1s point should stay.' );
    }

}