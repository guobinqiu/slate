<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

class LatestNewsServiceTest extends WebTestCase
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

    public function testInsertLatestNews()
    {
        $latestNewsService = $this->container->get('app.latest_news_service');
        for($i=0; $i<100; $i++) {
            $latestNewsService->insertLatestNews('最新动态'.$i);
        }
        print_r($latestNewsService->getLatestNews());

        $latestNewsService->insertLatestNews('最新动态100');
        print_r($latestNewsService->getLatestNews());

        $latestNewsService->insertLatestNews('最新动态101');
        print_r($latestNewsService->getLatestNews());
    }

    public function testBuildNews()
    {
        $latestNewsService = $this->container->get('app.latest_news_service');
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::SSI_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::CINT_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::FULCRUM_EXPENSE, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::SIGNUP, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::QUICK_POLL, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::EVENT_INVITE_SIGNUP, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::EVENT_INVITE_SURVEY, TaskType::RENTENTION);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::OFFERWOW_COST, TaskType::CPA);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::OFFER99_COST, TaskType::CPA);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, null, TaskType::CPS);
        echo PHP_EOL . $latestNewsService->buildNews($user, 100, CategoryType::SOP_COST, TaskType::SURVEY);
    }
}
