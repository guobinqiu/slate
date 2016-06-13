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
            '--subject' => 'signup confirmation',
            '--email' => 'qracle@126.com',
            '--name' => 'Guobin',
            '--register_key' => '1234567890',
        ));

        echo $commandTester->getDisplay();
    }

    public function testSignupSuccessMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SignupSuccessMailCommand());

        $command = $application->find('mail:signup_success');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            '--subject' => 'signup success',
            '--email' => 'qracle@126.com',
            '--name' => 'Guobin',
        ));

        echo $commandTester->getDisplay();
    }

    public function testSopDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SopDeliveryNotificationMailCommand());

        $command = $application->find('mail:sop_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'sop',
            '--survey_point' => 10,
            '--survey_length' => 10,
            '--subject' => 'sop delivery notification',
            '--channel' => 'channel2',
        ));

        echo $commandTester->getDisplay();
    }

    public function testFulcrumDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new FulcrumDeliveryNotificationMailCommand());

        $command = $application->find('mail:fulcrum_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'fulcrum',
            '--survey_point' => 10,
            '--subject' => 'fulcrum delivery notification',
            '--channel' => 'channel2',
        ));

        echo $commandTester->getDisplay();
    }

    public function testSsiDeliveryNotificationMailCommand() {
        $application = new Application(static::$kernel);
        $application->add(new SsiDeliveryNotificationMailCommand());

        $command = $application->find('mail:ssi_delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            '--name1' => 'Guobin',
            '--email' => 'qracle@126.com',
            '--survey_title' => 'ssi',
            '--survey_point' => 10,
            '--subject' => 'ssi delivery notification',
            '--channel' => 'channel2',
        ));

        echo $commandTester->getDisplay();
    }

//    public function testSOPDeliveryNotification() {
//        $request_body = '{
//              "app_id": "",
//              "data": {
//                "respondents": [
//                  {
//                    "app_mid":    "1",
//                    "survey_id":  "123",
//                    "quota_id":   "1234",
//                    "loi":        "10",
//                    "ir":         "50",
//                    "cpi":        "1.50",
//                    "title":      "Example survey title",
//                    "extra_info": {
//                        "point": {
//                            "complete": "10"
//                         }
//                    }
//                  },
//                  {
//                    "app_mid":    "2",
//                    "survey_id":  "123",
//                    "quota_id":   "1234",
//                    "loi":        "10",
//                    "ir":         "50",
//                    "cpi":        "1.50",
//                    "title":      "Example survey title",
//                    "extra_info": {
//                        "point": {
//                            "complete": "10"
//                         }
//                    }
//                  }
//                ]
//              },
//              "time": ""
//        }';
//        $request_data = json_decode($request_body, true);
//        $respondents = $request_data['data']['respondents'];
//
//        $application = new Application(static::$kernel);
//        $application->add(new DeliveryNotificationCommand());
//
//        $command = $application->find('mail:delivery_notification');
//        $commandTester = new CommandTester($command);
//        $commandTester->execute(array(
//            'respondents' => serialize($respondents),
//            '--type' => DeliveryNotificationCommand::SOP
//        ));
//
//        $this->assertEquals(array('errors' => 0, 'total' => 2, 'success' => '100%'), $commandTester->getDisplay());
//    }
}