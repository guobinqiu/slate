<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\ServiceDependency\Notification\FulcrumDeliveryNotification;
use Wenwen\FrontendBundle\ServiceDependency\Notification\SopDeliveryNotification;

class DeliveryNotificationTest extends WebTestCase {

    private $em;
    private $container;

    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->em = $em;
        $this->container = $container;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testSopDeliveryNotification() {
        $json =
        '{
          "app_id": "27",
          "data": {
            "respondents": [
              {"app_mid":"22681","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":2,"quotafull":1,"complete":400},"date":{"end_at":"2016-11-30 00:00:00","start_at":"2016-11-09 00:00:00"},"content":""},"blocked_devices":[],"loi":"20","title":"\u5173\u4e8e\u7f8e\u5bb9\u65b9\u9762\u7684\u8c03\u67e5","survey_id":"8006","quota_id":"46737"},
              {"app_mid":"1233","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":2,"quotafull":1,"complete":400},"date":{"end_at":"2016-11-30 00:00:00","start_at":"2016-11-09 00:00:00"},"content":""},"blocked_devices":[],"loi":"20","title":"\u5173\u4e8e\u7f8e\u5bb9\u65b9\u9762\u7684\u8c03\u67e5","survey_id":"8006","quota_id":"46737"},
              {"app_mid":"90833","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":2,"quotafull":1,"complete":400},"date":{"end_at":"2016-11-30 00:00:00","start_at":"2016-11-09 00:00:00"},"content":""},"blocked_devices":[],"loi":"20","title":"\u5173\u4e8e\u7f8e\u5bb9\u65b9\u9762\u7684\u8c03\u67e5","survey_id":"8006","quota_id":"46737"}
            ]
          },
          "time": 1479093443
        }';


        $arr = json_decode($json, true);
//        print_r($arr);

        $deliveryNotification = new SopDeliveryNotification($this->em, $this->container->get('app.sop_survey_service'));
        $deliveryNotification->send($arr['data']['respondents']);
        $deliveryNotification->send($arr['data']['respondents']);//测试重复执行

        $sopResearchSurveys = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurvey')->findAll();
//        print_r($sopResearchSurveys[0]);
        $this->assertEquals(1, count($sopResearchSurveys));
        $this->assertEquals(8006, $sopResearchSurveys[0]->getSurveyId());
        $this->assertEquals('关于美容方面的调查', $sopResearchSurveys[0]->getTitle());

        $sopResearchSurveyStatusHistories = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findAll();
//        foreach ($sopResearchSurveyStatusHistories as $sopResearchSurveyStatusHistory) {
//            echo PHP_EOL . 'app_mid=' . $sopResearchSurveyStatusHistory->getAppMid() . ', survey_id=' . $sopResearchSurveyStatusHistory->getSurveyId();
//        }
        $this->assertEquals(3, count($sopResearchSurveyStatusHistories));
    }

    public function testFulcrumDeliveryNotification() {
        $json =
        '{
          "app_id": "27",
          "data": {
            "respondents": [
              {"app_mid":"16419","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":0,"quotafull":0,"complete":400},"date":{"end_at":"","start_at":""},"content":""},"blocked_devices":[],"loi":"17","title":"Fulcrum Survey","survey_id":"7637","quota_id":"5411"},
              {"app_mid":"464968","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":0,"quotafull":0,"complete":400},"date":{"end_at":"","start_at":""},"content":""},"blocked_devices":[],"loi":"17","title":"Fulcrum Survey","survey_id":"7637","quota_id":"5411"},
              {"app_mid":"446694","cpi":"0.00","ir":"0","extra_info":{"point":{"screenout":0,"quotafull":0,"complete":400},"date":{"end_at":"","start_at":""},"content":""},"blocked_devices":[],"loi":"17","title":"Fulcrum Survey","survey_id":"7637","quota_id":"5411"}
            ]
          },
          "time": 1479093443
        }';

        $arr = json_decode($json, true);
//        print_r($arr);

        $deliveryNotification = new FulcrumDeliveryNotification($this->em, $this->container->get('app.fulcrum_survey_service'));
        $deliveryNotification->send($arr['data']['respondents']);
        $deliveryNotification->send($arr['data']['respondents']);//测试重复执行

        $fulcrumResearchSurveys = $this->em->getRepository('WenwenFrontendBundle:fulcrumResearchSurvey')->findAll();
//        print_r($fulcrumResearchSurveys[0]);
        $this->assertEquals(1, count($fulcrumResearchSurveys));
        $this->assertEquals(7637, $fulcrumResearchSurveys[0]->getSurveyId());
        $this->assertEquals('Fulcrum Survey', $fulcrumResearchSurveys[0]->getTitle());

        $fulcrumResearchSurveyStatusHistories = $this->em->getRepository('WenwenFrontendBundle:fulcrumResearchSurveyStatusHistory')->findAll();
//        foreach ($fulcrumResearchSurveyStatusHistories as $fulcrumResearchSurveyStatusHistory) {
//            echo PHP_EOL . 'app_mid=' . $fulcrumResearchSurveyStatusHistory->getAppMid() . ', survey_id=' . $fulcrumResearchSurveyStatusHistory->getSurveyId();
//        }
        $this->assertEquals(3, count($fulcrumResearchSurveyStatusHistories));
    }
}