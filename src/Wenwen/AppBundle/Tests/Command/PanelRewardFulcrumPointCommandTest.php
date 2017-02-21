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

        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 10001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 10001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 10001, 'forward');

        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 20001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 20001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 20001, 'forward');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 20001, 'complete');

        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 30001, 'targeted');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 30001, 'init');
        $this->container->get('app.survey_fulcrum_service')->createParticipationByAppMid($app_mid, 30001, 'forward');

        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
        $user_id =  $users[0]->getId();

        // data
        $rec1CompletePoint = 200;
        $rec2CompletePoint = 300;
        $rec3ScreenoutPoint = 30;
        $rec4QuotafullPoint = 20;

        $header = array('response_id', 'yyyymm', 'app_id', 'app_mid', 'survey_id', 'quota_id', 'title',
            'loi', 'ir', 'cpi', 'answer_status', 'approved_at','extra_info',);
        // complete 没已发放积分的历史记录 应该加积分给用户
        $rec1   = array('800001','201502','12',$app_mid,'10001','10002', 'This is a title1',
            '10','','1.500','COMPLETE','2015-02-14 06:00:06','{"point":"' . $rec1CompletePoint . '","point_type":"11"}');
        // complete 有已发放积分的历史记录 不应该加积分给用户
        $rec2   = array('800001','201502','12',$app_mid,'20001','20002', 'This is a title2',
            '11','','1.600','COMPLETE','2015-02-14 06:00:06','{"point":"' . $rec2CompletePoint . '","point_type":"11"}');
        // 实际为screenout 没已发放积分的历史记录 应该加积分给用户
        $rec3   = array('800001','201502','12',$app_mid,'30001','30002', 'This is a title3',
            '10','','1.500','COMPLETE','2015-02-14 06:00:06','{"point":"' . $rec3ScreenoutPoint . '","point_type":"11"}');
        // 实际为quotafull 没已发放积分的历史记录 不应该加积分给用户
        $rec4   = array('800001','201502','12',$app_mid,'40001','40002', 'This is a title4',
            '10','','1.500','COMPLETE','2015-02-14 06:00:06','{"point":"' . $rec4QuotafullPoint . '","point_type":"11"}');
        $footer = array('EOF','Total 4 Records',);
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
//        $history_stmt =   $em->getConnection()->prepare('select * from fulcrum_research_survey_participation_history');
//        $history_stmt->execute();
//        $history = $history_stmt->fetchAll();
//        $this->assertNotEmpty($history,'1 fulcrum_research_survey_participation_history history record');
//        $this->assertCount(2, $history,'1 point history record');
//        $this->assertEquals('10001',  $history[0]['fulcrum_project_id'] );
//        $this->assertEquals('10002', $history[0]['fulcrum_project_quota_id'] );
//        $this->assertEquals($app_mid,  $history[0]['app_member_id'] );
//        $this->assertEquals(30,  $history[0]['point'] );
//        $this->assertEquals(11,  $history[0]['type'] );
//
//        $this->assertEquals('20001',  $history[1]['fulcrum_project_id'] );
//        $this->assertEquals('20002', $history[1]['fulcrum_project_quota_id'] );
//        $this->assertEquals($app_mid,  $history[1]['app_member_id'] );
//        $this->assertEquals(100,  $history[1]['point'] );
//        $this->assertEquals(11,  $history[1]['type'] );

        // task history
        $task_stm =   $em->getConnection()->prepare('select * from task_history0'.( $user_id % 10 ));
        $task_stm->execute();
        $task_history =$task_stm->fetchAll();

        $this->assertCount(3, $task_history,'3 task history record');
        $this->assertEquals(9, $task_history[0]['task_type'],'suvey task9');
        $this->assertEquals(CategoryType::FULCRUM_COST, $task_history[0]['category_type'],'CategoryType::FULCRUM_COST');
        $this->assertEquals('f10001 This is a title1', $task_history[0]['task_name'],'task name');
        $this->assertEquals(9, $task_history[1]['task_type'],'suvey task9');
        $this->assertEquals(CategoryType::FULCRUM_COST, $task_history[1]['category_type'],'CategoryType::FULCRUM_COST');
        $this->assertEquals('f30001 This is a title3', $task_history[1]['task_name'],'task name');
        $this->assertEquals(9, $task_history[2]['task_type'],'suvey task9');
        $this->assertEquals(CategoryType::FULCRUM_COST, $task_history[2]['category_type'],'CategoryType::FULCRUM_COST');
        $this->assertEquals('f40001 This is a title4', $task_history[2]['task_name'],'task name');

        // points history
        $points_stm =   $em->getConnection()->prepare('select * from point_history0'.( $user_id % 10 ));
        $points_stm->execute();
        $points_history =$points_stm->fetchAll();

        $this->assertCount(3, $points_history,'3 point history record');
        $this->assertEquals($rec1CompletePoint, $points_history[0]['point_change_num'], $rec1CompletePoint . ' points');
        $this->assertEquals(CategoryType::FULCRUM_COST, $points_history[0]['reason'],'CategoryType::FULCRUM_COST');

        $this->assertEquals($rec3ScreenoutPoint, $points_history[1]['point_change_num'], $rec3ScreenoutPoint . ' points');
        $this->assertEquals(CategoryType::FULCRUM_COST, $points_history[1]['reason'],'CategoryType::FULCRUM_COST');

        // user points
        //$user_stm =   $em->getConnection()->prepare('select * from user where id =  '. $user_id);
        //$user_stm->execute();
        //$user_updated =$user_stm->fetchAll();

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);

        $this->assertNotEmpty($user,'1 test user');
        $this->assertEquals($rec1CompletePoint + $rec3ScreenoutPoint + $rec4QuotafullPoint, $user->getPoints(), $rec1CompletePoint . ' + ' . $rec3ScreenoutPoint . ' + '. $rec4QuotafullPoint);
        $this->assertEquals(1, $user->getCompleteN(), '应该有1个c');
        $this->assertEquals(2, $user->getScreenoutN(), '应该有2个s');
        $this->assertEquals(0, $user->getQuotafullN(), '应该有0个q');

        $stmt = $em->getConnection()->prepare('select * from survey_fulcrum_participation_history ');
        $stmt->execute();
        $history_list = $stmt->fetchAll();
        // 事先没有forward等初始状态的也不补全，只增加最终状态的历史记录
        $this->assertCount(13, $history_list);
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
        $user->setPoints(0);
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
/*
        //load data for testing .
        $user = new User();
        $user->setNick('bbb');
        $user->setEmail('rpa-sys+aaab@d8aspring.com');
        $user->setPoints(0);
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
        */
    }
}
