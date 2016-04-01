<?php
namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\DataFixtures\ORM\LoadPanelRewardSopPointCommandData;
use Wenwen\AppBundle\Command\PanelRewardSopAdditionalPointCommand;

class PanelRewardSopAdditionalPointCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $sop_responednt;

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
        $fixture = new LoadPanelRewardSopPointCommandData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
        $this->sop_responednt = LoadPanelRewardSopPointCommandData::$SOP_RESPONEDNT;
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
     */
    public function testExecuteInvalidAppMid()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $app_mid = $this->sop_responednt[1]->getId();

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
            "invalid-app-mid",
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
        try {

            $data = $commandTester->execute($commandParam);
        } catch (\Exception $e) {
            $this->assertEquals('No SopRespondent for: Invalid-app-mid', $e->getMessage());
        }

        $history_list = $em->getRepository('WenwenAppBundle:SopResearchSurveyAdditionalIncentiveHistory')->findByAppMemberId($app_mid);
        $this->assertEmpty($history_list);
    }

    /**
     * @group dev-merge-ui-sop_additional_point
     */
    public function testUpdateTable()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);
        $sop_respondents = LoadPanelRewardSopPointCommandData::$SOP_RESPONEDNT;
        $app_mid = $this->sop_responednt[1]->getId();

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

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals('400', $user->getPoints());
    }
}
