<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSopData;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\FulcrumDeliveryNotificationMailCommand;
use Wenwen\FrontendBundle\Command\SignupConfirmationMailCommand;
use Wenwen\FrontendBundle\Command\SignupSuccessMailCommand;
use Wenwen\FrontendBundle\Command\SopDeliveryNotificationBatchMailCommand;
use Wenwen\FrontendBundle\Command\SopDeliveryNotificationMailCommand;
use Wenwen\FrontendBundle\Command\SsiDeliveryNotificationMailCommand;

class MailCommandTest extends WebTestCase {

    private $container;

    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
        $em = $this->container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadUserSopData());
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testSignupConfirmationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SignupConfirmationMailCommand());

        $command = $application->find('mail:signup_confirmation');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--subject' => 'signup confirmation',
            '--email' => 'qracle@126.com',
            '--name' => 'Guobin',
            '--confirmation_token' => '1234567890',
        ));
    }

    public function testSignupSuccessMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SignupSuccessMailCommand());

        $command = $application->find('mail:signup_success');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--subject' => 'signup success',
            '--email' => 'qracle@126.com',
            '--name' => 'Guobin',
        ));
    }

    public function testSopDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SopDeliveryNotificationMailCommand());

        $command = $application->find('mail:sop_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'sop',
            '--survey_point' => 10,
            '--survey_length' => 10,
            '--subject' => 'sop delivery notification',
            '--survey_id' => 1,
            //'--channel' => 'channel2',//sendcloud
        ));

        $commandTester->execute(array(
            'command' => $command->getName(),
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'sop2',
            '--survey_point' => 10,
            '--survey_length' => 10,
            '--subject' => 'sop delivery notification',
            '--survey_id' => 7548,
        ));
    }

    public function testFulcrumDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new FulcrumDeliveryNotificationMailCommand());

        $command = $application->find('mail:fulcrum_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'fulcrum',
            '--survey_point' => 10,
            '--subject' => 'fulcrum delivery notification',
            //'--channel' => 'channel2',//sendcloud
        ));
    }

    public function testSsiDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SsiDeliveryNotificationMailCommand());

        $command = $application->find('mail:ssi_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'ssi',
            '--survey_point' => 10,
            '--subject' => 'ssi delivery notification',
            //'--channel' => 'channel3',//sendcloud
        ));
    }

//    public function testQQ()
//    {
//        $subject = 'testQQ';
//        $message = \Swift_Message::newInstance()
//            ->setSubject($subject)
//            ->setFrom(array('account@91jili.com' => '91问问调查网'))
//            ->setTo($this->recipients())
//            ->setBody('testQQ testQQ testQQ', 'text/html');
//        $mailer = $this->container->get('swiftmailer.mailer.qq');
//        $count = $mailer->send($message);
//        $this->assertEquals(count($this->recipients()), $count);
//    }

    public function testWebpowerSys() {
        $subject = 'testWebpowerSys';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'))
            ->setSender($this->container->getParameter('webpower_signup_sender'))
            ->setTo($this->recipients())
            ->setBody('testWebpowerSys testWebpowerSys testWebpowerSys', 'text/html');
        $mailer = $this->container->get('swiftmailer.mailer.webpower_signup_mailer');
        $count = $mailer->send($message);
        $this->assertEquals(count($this->recipients()), $count);
    }

    public function testWebpowerMkt() {
        $subject = 'testWebpowerMkt';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->container->getParameter('webpower_from') => '91问问调查网'))
            ->setSender($this->container->getParameter('webpower_sender'))
            ->setTo($this->recipients())
            ->setBody('testWebpowerMkt testWebpowerMkt testWebpowerMkt', 'text/html');
        $mailer = $this->container->get('swiftmailer.mailer.webpower_mailer');
        $count = $mailer->send($message);
        $this->assertEquals(count($this->recipients()), $count);
    }

    //添加收件人here
    private function recipients() {
        return array(
            'qracle@126.com',
            'xiaoyi.chai@d8aspring.com',
            '9615841@qq.com',
            'mercurylovesea@163.com',
            'cs@91wenwen.net',
        );
    }

    public function testSopDeliveryNotificationBatchMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SopDeliveryNotificationBatchMailCommand());

        $command = $application->find('mail:sop_delivery_notification_batch');
        $commandTester = new CommandTester($command);

        // test a totally incorrect data
        $respondents = "fdsafadsfdasfsafadfa";
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertEquals('The format of the argument passed in is incorrect', $commandTester->getDisplay());
        $this->assertEquals(1, $exitCode);

        // test without correct data structure
        $respondents = [
            [
                "app_mid"        => LoadUserSopData::$SOP_RESPONDENT_WITH_EMAIL_AND_SUBSCRIBED->getId(),
                "survey_id"      => "123",
                "quota_id"       => "1234",
                "loi"            => "10",
                "ir"             => "50",
                "cpi"            => "1.50",
                "title"          => "Example survey title",
            ]
        ];
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertContains('Notice', $commandTester->getDisplay());
        $this->assertEquals(1, $exitCode);

        // test with the not existing app_mid
        $respondents = [
            [
                "app_mid"        => 11111111,
                "survey_id"      => "123",
                "quota_id"       => "1234",
                "loi"            => "10",
                "ir"             => "50",
                "cpi"            => "1.50",
                "title"          => "Example survey title",
                "extra_info"     => [
                    "point" => [
                        "complete" => 100,
                    ]
                ]
            ]
        ];
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertContains('No user found', $commandTester->getDisplay());
        $this->assertEquals(1, $exitCode);

        // test without email
        $respondents = [
            [
                "app_mid"        => LoadUserSopData::$SOP_RESPONDENT_WITHOUT_EMAIL->getId(),
                "survey_id"      => "123",
                "quota_id"       => "1234",
                "loi"            => "10",
                "ir"             => "50",
                "cpi"            => "1.50",
                "title"          => "Example survey title",
                "extra_info"     => [
                    "point" => [
                        "complete" => 100,
                    ]
                ]
            ]
        ];
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertContains('does not have an email', $commandTester->getDisplay());
        $this->assertEquals(0, $exitCode);

        // test unsubscribed
        $respondents = [
            [
                "app_mid"        => LoadUserSopData::$SOP_RESPONDENT_UNSUBSCRIBED->getId(),
                "survey_id"      => "123",
                "quota_id"       => "1234",
                "loi"            => "10",
                "ir"             => "50",
                "cpi"            => "1.50",
                "title"          => "Example survey title",
                "extra_info"     => [
                    "point" => [
                        "complete" => 100,
                    ]
                ]
            ]
        ];
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertContains('does not want to receive email', $commandTester->getDisplay());
        $this->assertEquals(0, $exitCode);

        // test with email and subscribe
        $respondents = [
            [
                "app_mid"        => LoadUserSopData::$SOP_RESPONDENT_WITH_EMAIL_AND_SUBSCRIBED->getId(),
                "survey_id"      => "123",
                "quota_id"       => "1234",
                "loi"            => "10",
                "ir"             => "50",
                "cpi"            => "1.50",
                "title"          => "Example survey title",
                "extra_info"     => [
                    "point" => [
                        "complete" => 100,
                    ]
                ]
            ],
        ];
        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            '--respondents' => json_encode($respondents),
        ));
        $this->assertEquals(0, $exitCode);
    }
}