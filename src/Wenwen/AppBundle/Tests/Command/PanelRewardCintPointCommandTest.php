<?php

namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Command\PanelRewardCintPointCommand;
use Wenwen\FrontendBundle\Model\CategoryType;

class PanelRewardCintPointCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $sopRespondent;

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
        $fixture = new PanelRewardCintPointCommandTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
        $this->sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->findAll();
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
        $client = Phake::mock('Wenwen\AppBundle\Services\SopHttpfulClient');
        $container->set('sop_api.client', $client);
        $app_mid = $this->sopRespondent[1]->getAppMid();



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

        $footer = array (
            'EOF',
            'Total 2 Records'
        );

        $response = new \stdClass();

        $body = array(
            $header,
            );

        // include memory leak test
        $survey_id=0;
        for($i=1;$i<=100;$i++){
            $survey_id = $i;

            array_push($body, array (
                '800001',
                '201502',
                '12',
                $app_mid,
                $survey_id,
                '40002',
                'This is a title',
                '11',
                '',
                '0',
                'COMPLETE',
                'APPROVED',
                '2015-02-14 06:00:06',
                '{"point":"100","point_type":"11"}'
            ));
            $this->container->get('app.survey_cint_service')->createParticipationByAppMid($app_mid, $survey_id, 'targeted');
            $this->container->get('app.survey_cint_service')->createParticipationByAppMid($app_mid, $survey_id, 'init');
            $this->container->get('app.survey_cint_service')->createParticipationByAppMid($app_mid, $survey_id, 'forward');
        }

        $em->clear();

        array_push($body, $footer);

        $response->body = $body;

        // stub method
        Phake::when($client)->get(Phake::anyParameters())->thenReturn($response);

        $application = new Application(static::$kernel);

        $application->add(new PanelRewardCintPointCommand());
        $command = $application->find('panel:reward-cint-point');
        $command->setContainer($container);
        $this->assertInstanceOf('Wenwen\AppBundle\Command\PanelRewardCintPointCommand', $command);
        $commandTester = new CommandTester($command);
        $commandParam = array (
            'command' => $command->getName(),
            'date' => '2016-01-26',
            '--definitive' => true
        );

        // execute
        $data = $commandTester->execute($commandParam);


        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneByAppMid($app_mid);
        $user_id = $sopRespondent->getUserId();

        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(100, $task[0]->getPoint());
        $this->assertEquals('c1 This is a title', $task[0]->getTaskName());
        $this->assertEquals(CategoryType::CINT_COST, $task[0]->getCategoryType());

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findByUserId($user_id);
        $this->assertEquals(100, $point[0]->getPointChangeNum());
        $this->assertEquals(CategoryType::CINT_COST, $point[0]->getReason());

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $this->assertEquals(200 + $survey_id * 100, $user->getPoints());
        $this->assertEquals($survey_id, $user->getCompleteN());
        $this->assertEquals(0, $user->getScreenoutN());
        $this->assertEquals(0, $user->getQuotafullN());

        $stmt = $em->getConnection()->prepare('select * from survey_cint_participation_history ');
        $stmt->execute();
        $history_list = $stmt->fetchAll();
        $this->assertCount(4*$survey_id, $history_list);
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;

class PanelRewardCintPointCommandTestFixture implements FixtureInterface
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
        $r->setAppId(27);
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
        $r->setAppId(92);
        $manager->persist($r);
        $manager->flush();
    }
}
