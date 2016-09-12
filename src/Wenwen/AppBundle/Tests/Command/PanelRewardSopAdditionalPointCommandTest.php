<?php
namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Command\PanelRewardSopAdditionalPointCommand;

class PanelRewardSopAdditionalPointCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $sop_respondent;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));

        static::$kernel->boot();
        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new PanelRewardSopAdditionalPointCommandTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
        $this->sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        Phake::resetStaticInfo();
    }

    /**
     * @group dev-merge-ui-sop_additional_point
     * @group fix-test-static
     */
    public function testExecuteInvalidAppMid()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $app_mid = $this->sop_respondent[0]->getId();

        // data
        $header = array (
            "app_id",
            "app_mid",
            "survey_id",
            "quota_id",
            "title",
            "incentive_amount",
            "hash",
            "created_at",
            "extra_info"
        );
        $rec1 = array (
            "2",
            $app_mid,
            "1",
            "2",
            "zh_CH title",
            "100.00",
            "uniq6",
            "2014-12-01 10:00:00",
            '{"point_type":"11"}'
        );
        $rec2 = array (
            "2",
            "Invalid-app-mid",
            "1",
            "2",
            "zh_CH title",
            "100.00",
            "uniq7",
            "2014-12-01 10:00:00",
            '{"point_type":"61"}'
        );
        $footer = array (
            'EOF',
            'Total 2 Records'
        );

        $response = new \stdClass();
        $response->body = array (
            $header,
            $rec1,
            $rec2,
            $footer
        );

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $application = new Application(static::$kernel);

        $application->add(new PanelRewardSopAdditionalPointCommand());
        $command = $application->find('panel:reward-sop-additional-point');
        $command->setContainer($container);

        $this->assertInstanceOf('Wenwen\AppBundle\Command\PanelRewardSopAdditionalPointCommand', $command);

        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-01-26',
            '--definitive' => true
        );
        // execute
        $data = $commandTester->execute($commandParam);

        // assert
        $history_list = $em->getRepository('WenwenAppBundle:SopResearchSurveyAdditionalIncentiveHistory')->findByAppMemberId($app_mid);
        $this->assertCount(1, $history_list);
    }

    /**
     * @group dev-merge-ui-sop_additional_point
     * @group fix-test-static
     */
    public function testUpdateTable()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);
        $app_mid = $this->sop_respondent[0]->getId();

        $header = array (
            "app_id",
            "app_mid",
            "survey_id",
            "quota_id",
            "title",
            "incentive_amount",
            "hash",
            "created_at",
            "extra_info"
        );
        $rec1 = array (
            "2",
            $app_mid,
            "1",
            "2",
            "zh_CN title",
            "100.00",
            "uniq6",
            "2014-12-01 10:00:00",
            '{"point_type":"11"}'
        );
        $rec2 = array (
            "2",
            $app_mid,
            "1",
            "2",
            "zh_CN title",
            "100.00",
            "uniq7",
            "2014-12-01 10:00:00",
            '{"point_type":"61"}'
        );
        $footer = array (
            'EOF',
            'Total 2 Records'
        );
        $response = new \stdClass();
        $response->body = array (
            $header,
            $rec1,
            $rec2,
            $footer
        );

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $application = new Application(static::$kernel);
        $application->add(new PanelRewardSopAdditionalPointCommand());
        $command = $application->find('panel:reward-sop-additional-point');
        $command->setContainer($container);
        $this->assertInstanceOf('Wenwen\AppBundle\Command\PanelRewardSopAdditionalPointCommand', $command);
        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-01-26',
            '--definitive' => true
        );
        // execute
        $data = $commandTester->execute($commandParam);

        $history_list = $em->getRepository('WenwenAppBundle:SopResearchSurveyAdditionalIncentiveHistory')->findByAppMemberId($app_mid);
        $this->assertNotEmpty($history_list);
        $this->assertCount(2, $history_list);

        $this->assertEquals('1', $history_list[0]->getSurveyId());
        $this->assertEquals('2', $history_list[0]->getQuotaId());
        $this->assertEquals($app_mid, $history_list[0]->getAppMemberId());
        $this->assertEquals('100', $history_list[0]->getPoint());
        $this->assertEquals('92', $history_list[0]->getType());
        $this->assertEquals('uniq6', $history_list[0]->getHash());
        $this->assertEquals(date('Y-m-d'), $history_list[0]->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals(date('Y-m-d'), $history_list[0]->getUpdatedAt()->format('Y-m-d'));

        $this->assertEquals('1', $history_list[1]->getSurveyId());
        $this->assertEquals('2', $history_list[1]->getQuotaId());
        $this->assertEquals($app_mid, $history_list[1]->getAppMemberId());
        $this->assertEquals('100', $history_list[1]->getPoint());
        $this->assertEquals('93', $history_list[1]->getType());
        $this->assertEquals('uniq7', $history_list[1]->getHash());
        $this->assertEquals(date('Y-m-d'), $history_list[1]->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals(date('Y-m-d'), $history_list[1]->getUpdatedAt()->format('Y-m-d'));

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->find($app_mid);
        $user_id = $sop_respondent->getUserId();

        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals('100', $task[0]->getPoint());
        $this->assertEquals('r1 zh_CN title', $task[0]->getTaskName());
        $this->assertEquals('92', $task[0]->getCategoryType());

        $this->assertEquals('100', $task[1]->getPoint());
        $this->assertEquals('r1 zh_CN title', $task[1]->getTaskName());
        $this->assertEquals('93', $task[1]->getCategoryType());

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals('100', $point[0]->getPointChangeNum());
        $this->assertEquals('92', $point[0]->getReason());
        $this->assertEquals('100', $point[1]->getPointChangeNum());
        $this->assertEquals('93', $point[1]->getReason());

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $this->assertEquals('400', $user->getPoints());
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PanelRewardSopAdditionalPointCommandTestFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new \Wenwen\FrontendBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(200);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $r = new \Jili\ApiBundle\Entity\SopRespondent();
        $r->setUserId($user->getId());
        $r->setStatusFlag(\Jili\ApiBundle\Entity\SopRespondent::STATUS_ACTIVE);
        $manager->persist($r);
        $manager->flush();
    }
}
