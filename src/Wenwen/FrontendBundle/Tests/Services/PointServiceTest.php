<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;

class PointServiceTest extends WebTestCase
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
//        $this->em->close();
    }

    public function testAddPoints()
    {
        $pointService = $this->container->get('app.point_service');

        $user1 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $user2 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user2');

        $this->assertEquals(100, $user1->getPoints());
        $this->assertEquals(0, $user2->getPoints());

        $pointService->addPoints($user1, 10, 0, 0, 'test add points');
        $pointService->addPoints($user2, 10, 0, 0, 'test add points');

        $this->assertEquals(110, $user1->getPoints());
        $this->assertEquals(10, $user2->getPoints());
    }

    public function testAddPointsForInviter()
    {
        $pointService = $this->container->get('app.point_service');

        $user1 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $this->assertEquals(100, $user1->getPoints());

        $user2 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user2');
        $pointService->addPointsForInviter($user2, 100, 0, 0, 'test add points for inviter');
        $this->assertEquals(200, $user1->getPoints());

        $user3 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user3');
        $pointService->addPointsForInviter($user3, 100, 0, 0, 'test add points for inviter');
        $this->assertEquals(200, $user1->getPoints());

        $user4 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user4');
        $pointService->addPointsForInviter($user4, 100, 0, 0, 'test add points for inviter');
        $this->assertEquals(300, $user1->getPoints());
    }
}
