<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Entity\User;

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
        $this->em->close();
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
        $this->assertEquals(100, $user1->getPoints(), 'Original point is 100');

        $user2 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user2');
        $pointService->addPointsForInviter($user2, 100, 0, 0, 'points from user2');
        $this->assertEquals(100 + 100, $user1->getPoints(), 'Plus 100 point from user2');

        $user3 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user3');
        $pointService->addPointsForInviter($user3, 100, 0, 0, 'points from user3');
        $this->assertEquals(100 + 100, $user1->getPoints(), 'No point from user3 because user3 did not complete register');

        $user4 = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user4');
        $pointService->addPointsForInviter($user4, 100, 0, 0, 'first point from user4');
        $this->assertEquals(100 + 100 + 100, $user1->getPoints(), 'Plus 100 more point from user4');
        $user4->updateCSQ(SurveyStatus::STATUS_COMPLETE);
        $pointService->addPointsForInviter($user4, 100, 0, 0, 'bonus for first complete of user4');
        $this->assertEquals(100 + 100 + 100 + User::POINT_INVITE_SIGNUP + 100, $user1->getPoints(), 'There should be bonus');

        $user4->updateCSQ(SurveyStatus::STATUS_COMPLETE);
        $pointService->addPointsForInviter($user4, 100, 0, 0, 'No invite bonus for next time of user4');
        $this->assertEquals(100 + 100 + 100 + User::POINT_INVITE_SIGNUP + 100 + 100, $user1->getPoints(), 'There should be no more bonus');
    }

}
