<?php

namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Command\PanelRewardFulcrumAgreementCommand;
use Wenwen\FrontendBundle\Model\CategoryType;

class PanelRewardFulcrumAgreementCommandTest extends KernelTestCase
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
        $fixture = new PanelRewardFulcrumAgreementCommandTestFixture();
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
        $application->add(new PanelRewardFulcrumAgreementCommand());
        $command = $application->find('panel:reward-fulcrum-agreement');
        $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumAgreementCommand', $command, 'wrong class type');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $respondents = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
        $sop_respondent = $respondents[0];
        $app_mid = $sop_respondent->getId();

        // data
        $header = array('app_id', 'app_mid', 'agreement_status', 'answered_at');
        $rec1   = array('1',$app_mid,'ACTIVE','2015-09-20 00:00:00');
        $rec2   = array('1','Invalid-app-mid','ACTIVE','2015-09-20 00:00:00');
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

        // run
        $data = $commandTester->execute($commandParam);

        // assert
        $history = $em->getRepository('WenwenAppBundle:FulcrumUserAgreementParticipationHistory')->findAll();
        $this->assertCount(1, $history);
    }

    public function testUpdateTable()
    {

        $em = $this->em;
        $container = $this->container;

        $application = new Application(static::$kernel);
        $application->add(new PanelRewardFulcrumAgreementCommand());
        $command = $application->find('panel:reward-fulcrum-agreement');
        $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumAgreementCommand', $command, 'wrong class type');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);

        $respondents = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
        $app0_mid = $respondents[0]->getId();
        $app1_mid = $respondents[1]->getId();

        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
        $user0_id = $users[0]->getId();
        $user1_id = $users[1]->getId();

        // data
        $header = array('app_id', 'app_mid', 'agreement_status', 'answered_at');
        $rec1   = array('1',$app0_mid,'ACTIVE','2015-09-20 00:00:00');
        $rec2   = array('1',$app1_mid,'INACTIVE','2015-09-20 00:00:00');
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

        // checking participation history
        $history_stmt =   $em->getConnection()->prepare('select * from fulcrum_user_agreement_participation_history');
        $history_stmt->execute();
        $history = $history_stmt->fetchAll();
        $this->assertNotEmpty($history,'1 fulcrum_research_survey_participation_history history record');
        $this->assertCount(2, $history,'1 point history record');
        $this->assertEquals($app0_mid,  $history[0]['app_member_id'] );
        $this->assertEquals('1', $history[0]['agreement_status'] );
        $this->assertEquals($app1_mid,  $history[1]['app_member_id'] );
        $this->assertEquals('0', $history[1]['agreement_status'] );


        // task history
        $task_stm =   $em->getConnection()->prepare('select * from task_history0'.( $user0_id % 10 ));
        $task_stm->execute();
        $task_history =$task_stm->fetchAll();
        $this->assertNotEmpty($task_history,'1 task history record');
        $this->assertCount(1, $task_history,'1 task history record');

        $this->assertEquals(4, $task_history[0]['task_type'],'suvey task4');
        $this->assertEquals(CategoryType::FULCRUM_EXPENSE, $task_history[0]['category_type'],'user0 task_history.category_type should be CategoryType::FULCRUM_EXPENSE');
        $this->assertEquals('同意Fulcrum问卷调查', $task_history[0]['task_name'],'task name');

        $task_stm =   $em->getConnection()->prepare('select * from task_history0'.( $user1_id % 10 ));      
         $task_stm->execute();     
         $task_history =$task_stm->fetchAll();     
         $this->assertNotEmpty($task_history,'1 task history record');     
         $this->assertCount(1, $task_history,'1 task history record');     
         $this->assertEquals(4, $task_history[0]['task_type'],'suvey task4');      
         $this->assertEquals(CategoryType::FULCRUM_EXPENSE, $task_history[0]['category_type'],'user1 task_history.category_type shoulde be CategoryType::FULCRUM_EXPENSE');      
         $this->assertEquals('同意Fulcrum问卷调查', $task_history[0]['task_name'],'task name');
        // points history
        $points_stm =   $em->getConnection()->prepare('select * from point_history0'.( $user0_id % 10 ));
        $points_stm->execute();
        $points_history =$points_stm->fetchAll();
        $this->assertNotEmpty($points_history,'1 point history record');
        $this->assertCount(1, $points_history,'1 point history record');
        $this->assertEquals(10, $points_history[0]['point_change_num'],'10 points');
        $this->assertEquals(CategoryType::FULCRUM_EXPENSE, $points_history[0]['reason'],'user0 point_history.reason shoulde be CategoryType::FULCRUM_EXPENSE');


        $points_stm =   $em->getConnection()->prepare('select * from point_history0'.( $user1_id % 10 ));
        $points_stm->execute();
        $points_history =$points_stm->fetchAll();
        $this->assertNotEmpty($points_history,'1 point history record');
        $this->assertCount(1, $points_history,'1 point history record');
        $this->assertEquals(10, $points_history[0]['point_change_num'],'10 points');
        $this->assertEquals(CategoryType::FULCRUM_EXPENSE, $points_history[0]['reason'],'user0 point_history.reason shoulde be CategoryType::FULCRUM_EXPENSE');
        // user points
        $user_stm =   $em->getConnection()->prepare('select * from user ');
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();

        $this->assertNotEmpty($user_updated,'1 test user');
        $this->assertCount(2, $user_updated,'1 test user');
        $this->assertEquals(12, $user_updated[0]['points'], '1+ 11');
        $this->assertEquals(24, $user_updated[1]['points'], '0 + 23, 拒绝了也会加1分的');
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;

class PanelRewardFulcrumAgreementCommandTestFixture implements FixtureInterface
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
