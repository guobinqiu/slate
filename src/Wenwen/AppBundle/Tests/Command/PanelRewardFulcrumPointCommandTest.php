<?php

namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Command\PanelRewardFulcrumPointCommand;
use Wenwen\FrontendBundle\Model\CategoryType;

class PanelRewardFulcrumPointCommandTest extends KernelTestCase
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
        $fixture = new PanelRewardFulcrumPointCommandTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
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


    public function testInvalidAppMID()
    {
        $em = $this->em;
        $container = $this->container;

        $application = new Application(static::$kernel);
        $application->add(new PanelRewardFulcrumPointCommand());
        $command = $application->find('panel:reward-fulcrum-point');
        $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumPointCommand', $command, 'wrong class type');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $respondents = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
        $sop_respondent = $respondents[0];
        $app_mid = $sop_respondent->getId();

        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 30001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 30001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 30001, 'forward');

        // data
        $header = array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id', 'title',
            'loi', 'ir', 'cpi', 'answer_status', 'approval_status','extra_info',);
        $rec1   = array('15','201502','2',$app_mid,'30001','30002', 'This is a title1',
            '10','','1.500','COMPLETE','APPROVED','{"point":"30","point_type":"11"}');
        $rec2   = array('16','201502','3','Invalid-app-mid','2','3', 'This is a title 2',
            '11','','1.600','COMPLETE','APPROVED','{"point":"100","point_type":"11"}');
        $footer = array('EOF','Total 2 Records',);
        $response        = new \stdClass();
        $response->body  = array($header,$rec1,$rec2,$footer);

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-03-02',
            '--definitive' => true
        );

        // execute
        $data = $commandTester->execute($commandParam);

        // assert
        $history = $em->getRepository('WenwenAppBundle:FulcrumResearchSurveyParticipationHistory')->findAll();
        $this->assertCount(1, $history);

    }


    public function testUpdateTable()
    {
        $em = $this->em;
        $container = $this->container;

        $application = new Application(static::$kernel);
        $application->add(new PanelRewardFulcrumPointCommand());
        $command = $application->find('panel:reward-fulcrum-point');
        $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumPointCommand', $command, 'wrong class type');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $respondents = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
        $app_mid =  $respondents[0]->getId();

        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 10001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 10001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 10001, 'forward');

        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 20001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 20001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createStatusHistory($app_mid, 20001, 'forward');

        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
        $user_id =  $users[0]->getId();

        // data
        $header = array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id', 'title',
            'loi', 'ir', 'cpi', 'answer_status', 'approved_at','extra_info',);
        $rec1   = array('800001','201502','12',$app_mid,'10001','10002', 'This is a title1',
            '10','','1.500','COMPLETE','2015-02-14 06:00:06','{"point":"30","point_type":"11"}');
        $rec2   = array('800001','201502','12',$app_mid,'20001','20002', 'This is a title2',
            '11','','1.600','COMPLETE','2015-02-14 06:00:06','{"point":"100","point_type":"11"}');
        $rec3   = array('800001','201502','12',$app_mid,'30001','30002', 'This is a title3',
            '10','','1.500','SCREENOUT','2015-02-14 06:00:06','{"point":"0","point_type":"11"}');
        $rec4   = array('800001','201502','12',$app_mid,'40001','40002', 'This is a title4',
            '10','','1.500','QUOTAFULL','2015-02-14 06:00:06','{"point":"0","point_type":"11"}');
        $footer = array('EOF','Total 2 Records',);
        $response        = new \stdClass();
        $response->body  = array($header,$rec1,$rec2,$rec3,$rec4,$footer);

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-03-02',
            '--definitive' => true

        );
        // execute
        $data = $commandTester->execute($commandParam);

        // checking participation history
        $history_stmt =   $em->getConnection()->prepare('select * from fulcrum_research_survey_participation_history');
        $history_stmt->execute();
        $history = $history_stmt->fetchAll();
        $this->assertNotEmpty($history,'1 fulcrum_research_survey_participation_history history record');
        $this->assertCount(2, $history,'1 point history record');
        $this->assertEquals('10001',  $history[0]['fulcrum_project_id'] );
        $this->assertEquals('10002', $history[0]['fulcrum_project_quota_id'] );
        $this->assertEquals($app_mid,  $history[0]['app_member_id'] );
        $this->assertEquals(30,  $history[0]['point'] );
        $this->assertEquals(11,  $history[0]['type'] );

        $this->assertEquals('20001',  $history[1]['fulcrum_project_id'] );
        $this->assertEquals('20002', $history[1]['fulcrum_project_quota_id'] );
        $this->assertEquals($app_mid,  $history[1]['app_member_id'] );
        $this->assertEquals(100,  $history[1]['point'] );
        $this->assertEquals(11,  $history[1]['type'] );

        // task history
        $task_stm =   $em->getConnection()->prepare('select * from task_history0'.( $user_id % 10 ));
        $task_stm->execute();
        $task_history =$task_stm->fetchAll();
        $this->assertNotEmpty($task_history,'2 task history record');
        $this->assertCount(2, $task_history,'2 task history record');
        $this->assertEquals(9, $task_history[0]['task_type'],'suvey task9');
        $this->assertEquals(CategoryType::FULCRUM_COST, $task_history[0]['category_type'],'CategoryType::FULCRUM_COST');
        $this->assertEquals('f10001 This is a title1', $task_history[0]['task_name'],'task name');
        $this->assertEquals(9, $task_history[1]['task_type'],'suvey task9');
        $this->assertEquals(CategoryType::FULCRUM_COST, $task_history[1]['category_type'],'CategoryType::FULCRUM_COST');
        $this->assertEquals('f20001 This is a title2', $task_history[1]['task_name'],'task name');

        // points history
        $points_stm =   $em->getConnection()->prepare('select * from point_history0'.( $user_id % 10 ));
        $points_stm->execute();
        $points_history =$points_stm->fetchAll();
        $this->assertNotEmpty($points_history,'2 point history record');
        $this->assertCount(2, $points_history,'2 point history record');
        $this->assertEquals(30, $points_history[0]['point_change_num'],'7 points');
        $this->assertEquals(CategoryType::FULCRUM_COST, $points_history[0]['reason'],'CategoryType::FULCRUM_COST');

        $this->assertEquals(100, $points_history[1]['point_change_num'],'7 points');
        $this->assertEquals(CategoryType::FULCRUM_COST, $points_history[1]['reason'],'CategoryType::FULCRUM_COST');

        // user points
        $user_stm =   $em->getConnection()->prepare('select * from user where id =  '. $user_id);
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();

        $this->assertNotEmpty($user_updated,'1 test user');
        $this->assertCount(1, $user_updated,'1 test user');
        $this->assertEquals(141, $user_updated[0]['points'], '100 + 30 + 11');

    }

}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;

class PanelRewardFulcrumPointCommandTestFixture implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('aaa');
        $user->setEmail('rpa-sys+aaa@d8aspring.com');
        $user->setPoints(11);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();

        //load data for testing .
        $user = new User();
        $user->setNick('bbb');
        $user->setEmail('rpa-sys+aaab@d8aspring.com');
        $user->setPoints(23);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        //inactive
        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
    }
}
