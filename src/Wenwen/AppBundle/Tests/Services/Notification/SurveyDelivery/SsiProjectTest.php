<?php

namespace Wenwen\AppBundle\Tests\Services\Notification\SurveyDelivery;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SsiProjectTest extends KernelTestCase
{
    protected static $kernel;

    public function setUp()
    {
        static::$kernel = static::createKernel(array(
            'environment' => 'test',
            'debug' => false,
        ));
        static::$kernel->boot();

        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SsiProjectTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testCreateInstance()
    {
        $respondentIds = array('wwcn-'.SsiProjectTestFixture::$SSI_RESPONDENT->getId(), 'wwcn-999');
        $notification = new \Wenwen\AppBundle\Services\Notification\SurveyDelivery\SsiProject(
            $respondentIds,
            $this->em,
            $this->container
          );

        $this->assertEquals($respondentIds, $notification->getRespondentIds());
    }

    public function testSetupRespondentsToMail()
    {
        $respondentIds = array('wwcn-'.SsiProjectTestFixture::$SSI_RESPONDENT->getId(), 'wwcn-999');
        $notification = new \Wenwen\AppBundle\Services\Notification\SurveyDelivery\SsiProject(
            $respondentIds,
            $this->em,
            $this->container
        );

        $recipients = $notification->retrieveRecipientsToMail();

        $this->assertEquals(array(
          array(
            'email' => 'test@d8aspring.com',
            'name1' => __NAMESPACE__.'\SsiProjectTestFixture',
            'title' => '先生',
          ),
        ), $recipients);
    }

    public function testGetMailTemplateParams()
    {
        $respondentIds = array('wwcn-'.SsiProjectTestFixture::$SSI_RESPONDENT->getId(), 'wwcn-999');
        $notification = new \Wenwen\AppBundle\Services\Notification\SurveyDelivery\SsiProject(
            $respondentIds,
            $this->em,
            $this->container
        );
        $params = $notification->getMailTemplateParams('1234', array(
            'email' => 'test@d8aspring.com',
            'name1' => __NAMESPACE__.'\SsiProjectTestFixture',
            'title' => '先生',
          ));
        $this->assertEquals(array(
          'email' => 'test@d8aspring.com',
          'name1' => __NAMESPACE__.'\SsiProjectTestFixture',
          'title' => '先生',
          'survey_title' => 'SSI海外调查',
          'survey_id' => '1234',
          'survey_point' => 180,
        ), $params);
    }

    public function testSendMail()
    {
        $respondentIds = array('wwcn-'.SsiProjectTestFixture::$SSI_RESPONDENT->getId(), 'wwcn-999');
        $notification = new \Wenwen\AppBundle\Services\Notification\SurveyDelivery\SsiProject(
            $respondentIds,
            $this->em,
            $this->container
        );

        $recipients = $notification->retrieveRecipientsToMail();

        $jobs = $notification->sendMailing(100, $recipients);
        $this->assertCount(1, $jobs);

        $this->assertInstanceOf('JMS\JobQueueBundle\Entity\Job', $jobs[0]);
        $this->assertSame('research_survey:delivery_notification', $jobs[0]->getCommand());
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiProjectTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $SSI_RESPONDENT;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setIsEmailConfirmed(1);
        $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_WENWEN);
        $manager->persist($user);
        $manager->flush();

        $user_wenwen_login = new \Jili\ApiBundle\Entity\UserWenwenLogin();
        $user_wenwen_login->setUser($user);
        $user_wenwen_login->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');
        $user_wenwen_login->setLoginPasswordCryptType('blowfish');
        $user_wenwen_login->setLoginPassword('9rNV0b+0hnA=');
        $manager->persist($user_wenwen_login);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        self::$SSI_RESPONDENT = $ssi_respondent;
    }
}
