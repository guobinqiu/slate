<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
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
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em = $em;

        $loader = new Loader();
        $loader->addFixture(new LoadSopData());

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
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
}


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSopData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('Guobin');
        $user->setEmail('qracle@126.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $sop_respondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setId(1);
        $manager->persist($sop_respondent);
        $manager->flush();

        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('Guobin');
        $user->setEmail('guobin.qiu@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('password');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $sop_respondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setId(2);
        $manager->persist($sop_respondent);
        $manager->flush();
    }
}