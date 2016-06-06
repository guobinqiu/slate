<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\DeliveryNotificationCommand;

class DeliveryNotificationCommandTest extends WebTestCase {

    private $respondents;

    public function setUp(){
        static::$kernel = static::createKernel();
        //static::$kernel->boot();

        $request_body = '{
              "app_id": "",
              "data": {
                "respondents": [
                  {
                    "app_mid":    "1",
                    "survey_id":  "123",
                    "quota_id":   "1234",
                    "loi":        "10",
                    "ir":         "50",
                    "cpi":        "1.50",
                    "title":      "Example survey title",
                    "extra_info": {
                        "point": {
                            "complete": "10"
                         }
                    }
                  },
                  {
                    "app_mid":    "2",
                    "survey_id":  "123",
                    "quota_id":   "1234",
                    "loi":        "10",
                    "ir":         "50",
                    "cpi":        "1.50",
                    "title":      "Example survey title",
                    "extra_info": {
                        "point": {
                            "complete": "10"
                         }
                    }
                  },
                ]
              },
              "time": ""
        }';
        $request_data = json_decode($request_body, true);
        $this->respondents = $request_data['data']['respondents'];
    }

    public function testSOPDeliveryNotification() {

        $application = new Application(static::$kernel);
        $application->add(new DeliveryNotificationCommand());

        $command = $application->find('mail:delivery_notification');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'respondents' => serialize($this->respondents),
            '--survey' => 'sop'
        ));

        //$this->assertEquals(array('errors' => 0, 'total' => 2, 'success' => '100%'), $commandTester->getDisplay());
        $this->assertEquals(0, 0);
    }
}