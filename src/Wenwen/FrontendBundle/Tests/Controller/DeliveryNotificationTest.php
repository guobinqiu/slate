<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Services\SopDeliveryNotification;

class DeliveryNotificationTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testSopDeliveryNotification() {
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
                "title":      "Example",
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
                "title":      "Example",
                "extra_info": {
                    "point": {
                        "complete": "10"
                     }
                }
              }
            ]
          },
          "time": ""
        }';

        $request_data = json_decode($request_body, true);
        $respondents = $request_data['data']['respondents'];

        $notification = new SopDeliveryNotification($this->em);
        print_r($notification->send($respondents));
    }

    //{"requestHeader":{"contactMethodId":1,"projectId":2,"mailBatchId":3},"startUrlHead":"http:\/\/www.d8aspring.com\/?test=","respondentList":[{"respondentId":"wwcn-1","startUrlId":"sur1"},{"respondentId":"wwcn-9998","startUrlId":""},{"respondentId":"wwcn-9999","startUrlId":"sur3"}]}
}

class LoadSopData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        self::$USER = $user;
        self::$SSI_RESPONDENT = $ssi_respondent;
    }
}