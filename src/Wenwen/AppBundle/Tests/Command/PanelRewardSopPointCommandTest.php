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
use Wenwen\AppBundle\Command\PanelRewardSopPointCommand;

class PanelRewardSopPointCommandTest extends KernelTestCase
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
     * @group dev-merge-ui-sop_point
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
            'response_id',
            'yyyymm',
            'app_id',
            'app_mid',
            'survey_id',
            'quota_id',
            'title',
            'loi',
            'ir',
            'cpi',
            'answer_status',
            'approval_status',
            'approved_at',
            'extra_info'
        );

        $rec1 = array (
            '15',
            '201502',
            '2',
            $app_mid,
            '30001',
            '30002',
            'This is a title1',
            '10',
            '',
            '1.500',
            'SCREENOUT',
            'APPROVED',
            '2015-02-14 06:00:06',
            '{"point":"30","point_type":"11"}'
        );
        $rec2 = array (
            '16',
            '201502',
            '3',
            'Invalid-app-mid',
            '2',
            '3',
            'This is a title 2',
            '11',
            '',
            '1.600',
            'COMPLETE',
            'APPROVED',
            '2015-02-14 06:00:06',
            '{"point":"100","point_type":"61"}'
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

        $application->add(new PanelRewardSopPointCommand());
        $command = $application->find('panel:reward-sop-point');
        $command->setContainer($container);

        $this->assertInstanceOf('Wenwen\AppBundle\Command\PanelRewardSopPointCommand', $command);

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

        $stmt = $em->getConnection()->prepare('select * from sop_research_survey_participation_history where app_member_id = 1');
        $stmt->execute();
        $history = $stmt->fetchAll();
        $this->assertEmpty($history);
    }

    /**
     * @group dev-merge-ui-sop_point
     */
    public function testUpdateTable()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);
        $sop_respondents = LoadPanelRewardSopPointCommandData::$SOP_RESPONEDNT;
        $app_mid = $this->sop_responednt[1]->getId();

        // data
        $header = array (
            'response_id',
            'yyyymm',
            'app_id',
            'app_mid',
            'survey_id',
            'quota_id',
            'title',
            'loi',
            'ir',
            'cpi',
            'answer_status',
            'approval_status',
            'approved_at',
            'extra_info'
        );
        $rec1 = array (
            '800001',
            '201502',
            '12',
            $app_mid,
            '10001',
            '10002',
            'This is a title1',
            '10',
            '',
            '1.500',
            'SCREENOUT',
            'APPROVED',
            '2015-02-14 06:00:06',
            '{"point":"30","point_type":"11"}'
        );
        $rec2 = array (
            '800001',
            '201502',
            '12',
            $app_mid,
            '20001',
            '20002',
            'This is a title2',
            '11',
            '',
            '1.600',
            'COMPLETE',
            'APPROVED',
            '2015-02-14 06:00:06',
            '{"point":"100","point_type":"61"}'
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
        $application->add(new PanelRewardSopPointCommand());
        $command = $application->find('panel:reward-sop-point');
        $command->setContainer($container);
        $this->assertInstanceOf('Wenwen\AppBundle\Command\PanelRewardSopPointCommand', $command);
        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-01-26',
            '--definitive' => true
        );
        // execute
        $data = $commandTester->execute($commandParam);

        $stmt = $em->getConnection()->prepare('select * from sop_research_survey_participation_history ');
        $stmt->execute();
        $history_list = $stmt->fetchAll();

        $this->assertNotEmpty($history_list);
        $this->assertCount(2, $history_list);

        $this->assertEquals($history_list[0]['partner_app_project_id'], '10001');
        $this->assertEquals($history_list[0]['partner_app_project_quota_id'], '10002');
        $this->assertEquals($history_list[0]['app_member_id'], $app_mid);
        $this->assertEquals($history_list[0]['point'], '30');
        $this->assertEquals($history_list[0]['type'], '92');

        $this->assertEquals($history_list[1]['partner_app_project_id'], '20001');
        $this->assertEquals($history_list[1]['partner_app_project_quota_id'], '20002');
        $this->assertEquals($history_list[1]['app_member_id'], $app_mid);
        $this->assertEquals($history_list[1]['point'], '100');
        $this->assertEquals($history_list[1]['type'], '93');

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->find($app_mid);
        $user_id = $sop_respondent->getUserId();

        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals($task[0]->getPoint(), 30);
        $this->assertEquals($task[0]->getTaskName(), 'r10001 This is a title1');
        $this->assertEquals($task[0]->getCategoryType(), '92');

        $this->assertEquals($task[1]->getPoint(), 100);
        $this->assertEquals($task[1]->getTaskName(), 'r20001 This is a title2');
        $this->assertEquals($task[1]->getCategoryType(), '93');

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals($point[0]->getPointChangeNum(), 30);
        $this->assertEquals($point[0]->getReason(), 92);
        $this->assertEquals($point[1]->getPointChangeNum(), 100);
        $this->assertEquals($point[1]->getReason(), 93);

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals($user->getPoints(), 330);
    }
}
