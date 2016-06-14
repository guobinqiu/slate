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

    public function setUp(){
        static::$kernel = static::createKernel();
        static::$kernel->boot();
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
            '--channel' => 'channel2',
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
            '--channel' => 'channel2',
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
            '--channel' => 'channel3',
        ));
    }
}