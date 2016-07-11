<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\FulcrumDeliveryNotificationMailCommand;
use Wenwen\FrontendBundle\Command\SignupConfirmationMailCommand;
use Wenwen\FrontendBundle\Command\SignupSuccessMailCommand;
use Wenwen\FrontendBundle\Command\SopDeliveryNotificationMailCommand;
use Wenwen\FrontendBundle\Command\SsiDeliveryNotificationMailCommand;

class MailCommandTest extends WebTestCase {

    private $container;

    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
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
            '--register_key' => '1234567890',
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
            //'--channel' => 'channel2',//sendcloud
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

    public function testQQ()
    {
        $subject = 'testQQ';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('account@91jili.com' => '91问问调查网'))
            ->setTo($this->recipients())
            ->setBody('你好你好你好', 'text/html');
        $mailer = $this->container->get('swiftmailer.mailer.qq');
        $count = $mailer->send($message);
        $this->assertEquals(5, $count);
    }

    public function testWebpowerSys() {
        $subject = 'testWebpowerSys';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('support@91wenwen.com' => '91问问调查网'))
            ->setTo($this->recipients())
            ->setBody('你好你好你好', 'text/html');
        $mailer = $this->container->get('swiftmailer.mailer.webpower_signup_mailer');
        $count = $mailer->send($message);
        $this->assertEquals(5, $count);
    }

    public function testWebpowerMkt() {
        $subject = 'testWebpowerMkt';
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('support@91wenwen.com' => '91问问调查网'))
            ->setTo($this->recipients())
            ->setBody('', 'text/html');
        $mailer = $this->container->get('swiftmailer.mailer.webpower_mailer');
        $count = $mailer->send($message);
        $this->assertEquals(5, $count);
    }

    //添加收件人here
    private function recipients() {
        return array(
            'support@91wenwen.com',
            'qracle@126.com',
            'xiaoyi.chai@d8aspring.com',
            '9615841@qq.com',
            'mercurylovesea@163.com',
        );
    }
}