<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\ServiceDependency\Notification\SopDeliveryNotification;
use Wenwen\FrontendBundle\ServiceDependency\Notification\SsiDeliveryNotification;

class DeliveryNotificationTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em = $em;

        $loader = new Loader();
        $loader->addFixture(new LoadData());

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
        $this->em = null;
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
              }
            ]
          },
          "time": ""
        }';

        $request_data = json_decode($request_body, true);

        $sopData = $this->em->getRepository('JiliApiBundle:SopRespondent')->findAll();
        $request_data['data']['respondents'][0]['app_mid'] = $sopData[0]->getId();
        $request_data['data']['respondents'][1]['app_mid'] = $sopData[1]->getId();

        $respondents = $request_data['data']['respondents'];

        $notification = new SopDeliveryNotification($this->em);
        print_r($notification->send($respondents));
    }

    public function testSsiDeliveryNotification() {
        $ssiData = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->findAll();
        $notification = new SsiDeliveryNotification($this->em);
        $notification->send(array(
            'wwcn-'.$ssiData[0]->getId(),
            'wwcn-'.$ssiData[1]->getId(),
        ));
    }

    public function testUserEdmUnsubscribe() {
        $userEdmUnsubscribes = $this->em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail('qracle@126.com');
        $this->assertCount(1, $userEdmUnsubscribes);
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadData implements FixtureInterface
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

        $sopRespondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $manager->persist($sopRespondent);
        $manager->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssiRespondent);
        $manager->flush();

        $userEdmUnsubscribe = new \Jili\ApiBundle\Entity\UserEdmUnsubscribe();
        $userEdmUnsubscribe->setUserId($user->getId());
        $userEdmUnsubscribe->setCreatedTime(new \DateTime());
        $manager->persist($userEdmUnsubscribe);
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

        $sopRespondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $manager->persist($sopRespondent);
        $manager->flush();

        $ssiRespondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssiRespondent->setUser($user);
        $ssiRespondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssiRespondent);
        $manager->flush();
    }
}