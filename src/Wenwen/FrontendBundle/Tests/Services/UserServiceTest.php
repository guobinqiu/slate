<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

class UserServiceTest extends WebTestCase
{
    private $container;
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testSerializer()
    {
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');

        $serializer = $this->container->get('jms_serializer');
        $str = $serializer->serialize($user, 'json');
        echo $str;

        $user = $serializer->deserialize($str, 'Wenwen\FrontendBundle\Entity\User', 'json');
        $this->assertEquals('user1', $user->getNick());
    }

    public function testAddPoints()
    {
        $userService = $this->container->get('app.user_service');

        $user1 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $user2 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user2');

        $this->assertEquals(100, $user1->getPoints());
        $this->assertEquals(0, $user2->getPoints());

        $userService->addPoints($user1, 10, 0, 0, 'test add points');
        $userService->addPoints($user2, 10, 0, 0, 'test add points');

        $this->assertEquals(110, $user1->getPoints());
        $this->assertEquals(10, $user2->getPoints());
    }

    public function testAddPointsForInviter()
    {
        $userService = $this->container->get('app.user_service');

        $user1 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $this->assertEquals(100, $user1->getPoints());

        $user2 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user2');
        $userService->addPointsForInviter($user2, 100, 0, 0, 'test add points for inviter');

        $this->assertEquals(200, $user1->getPoints());
    }

    public function testInsertLatestNews()
    {
        $userService = $this->container->get('app.user_service');
        for($i=0; $i<100; $i++) {
            $userService->insertLatestNews('最新动态'.$i);
        }
        print_r($userService->getLatestNews());

        $userService->insertLatestNews('最新动态100');
        print_r($userService->getLatestNews());

        $userService->insertLatestNews('最新动态101');
        print_r($userService->getLatestNews());
    }

    public function testBuildNews()
    {
        $userService = $this->container->get('app.user_service');
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::SSI_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::CINT_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::FULCRUM_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::SIGNUP, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::QUICK_POLL, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::EVENT_INVITE_SIGNUP, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::EVENT_INVITE_SURVEY, TaskType::RENTENTION);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::OFFERWOW_COST, TaskType::CPA);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::OFFER99_COST, TaskType::CPA);
        echo PHP_EOL . $userService->buildNews($user, 100, null, TaskType::CPS);
        echo PHP_EOL . $userService->buildNews($user, 100, CategoryType::SOP_COST, TaskType::SURVEY);
    }
}
