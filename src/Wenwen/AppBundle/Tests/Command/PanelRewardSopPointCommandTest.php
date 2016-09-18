<?php
namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Command\PanelRewardSopPointCommand;
use Wenwen\FrontendBundle\Entity\CategoryType;

class PanelRewardSopPointCommandTest extends KernelTestCase
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
        $fixture = new PanelRewardSopPointCommandTestFixture();
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
     * @group dev-merge-ui-sop_point
     * @group fix-test-static
     */
    public function testExecuteInvalidAppMid()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $app_mid = $this->sop_respondent[1]->getId();

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
        $data = $commandTester->execute($commandParam);

        $history = $em->getRepository('WenwenAppBundle:SopResearchSurveyParticipationHistory')->findAll();
        $this->assertCount(1, $history);
    }

    /**
     * @group dev-merge-ui-sop_point
     * @group fix-test-static
     */
    public function testUpdateTable()
    {
        $em = $this->em;
        $container = $this->container;
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);
        $app_mid = $this->sop_respondent[1]->getId();

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

        $this->assertEquals('10001', $history_list[0]['partner_app_project_id']);
        $this->assertEquals('10002', $history_list[0]['partner_app_project_quota_id']);
        $this->assertEquals($app_mid, $history_list[0]['app_member_id']);
        $this->assertEquals('30', $history_list[0]['point']);
        $this->assertEquals('11', $history_list[0]['type']);
        $this->assertEquals(date('Y-m-d'), substr($history_list[0]['created_at'], 0, 10));
        $this->assertEquals(date('Y-m-d'), substr($history_list[0]['updated_at'], 0, 10));

        $this->assertEquals('20001', $history_list[1]['partner_app_project_id']);
        $this->assertEquals('20002', $history_list[1]['partner_app_project_quota_id']);
        $this->assertEquals($app_mid, $history_list[1]['app_member_id']);
        $this->assertEquals('100', $history_list[1]['point']);
        $this->assertEquals('61', $history_list[1]['type']);
        $this->assertEquals(date('Y-m-d'), substr($history_list[1]['created_at'], 0, 10));
        $this->assertEquals(date('Y-m-d'), substr($history_list[1]['updated_at'], 0, 10));

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->find($app_mid);
        $user_id = $sop_respondent->getUserId();

        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(30, $task[0]->getPoint());
        $this->assertEquals('r10001 This is a title1', $task[0]->getTaskName());
        $this->assertEquals(CategoryType::SOP_COST, $task[0]->getCategoryType());

        $this->assertEquals(100, $task[1]->getPoint());
        $this->assertEquals('r20001 This is a title2', $task[1]->getTaskName());
        $this->assertEquals(CategoryType::SOP_EXPENSE, $task[1]->getCategoryType());

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(30, $point[0]->getPointChangeNum());
        $this->assertEquals(CategoryType::SOP_COST, $point[0]->getReason());
        $this->assertEquals(100, $point[1]->getPointChangeNum());
        $this->assertEquals(CategoryType::SOP_EXPENSE, $point[1]->getReason());

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $this->assertEquals(330, $user->getPoints());

        // execute again (old: $rec2, new : $rec3)
        $rec3 = array (
            '800001',
            '201502',
            '12',
            $app_mid,
            '30001',
            '30002',
            'This is a title2',
            '11',
            '',
            '1.600',
            'COMPLETE',
            'APPROVED',
            '2015-02-14 06:00:06',
            '{"point":"100","point_type":"61"}'
        );
        $response = new \stdClass();
        $response->body = array (
            $header,
            $rec2,
            $rec3,
            $footer
        );
        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);
        $data = $commandTester->execute($commandParam);

        $stmt = $em->getConnection()->prepare('select * from sop_research_survey_participation_history ');
        $stmt->execute();
        $history_list = $stmt->fetchAll();
        $this->assertCount(3, $history_list);
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;

class PanelRewardSopPointCommandTestFixture implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $r = new SopRespondent();
        $r->setUserId($user->getId());
        $r->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $manager->persist($r);
        $manager->flush();

        $user = new User();
        $user->setNick('cc');
        $user->setEmail('test2@d8aspring.com');
        $user->setPoints(200);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $r = new SopRespondent();
        $r->setUserId($user->getId());
        $r->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $manager->persist($r);
        $manager->flush();
    }
}
