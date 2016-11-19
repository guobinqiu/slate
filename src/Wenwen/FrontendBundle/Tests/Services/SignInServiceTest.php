<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\UserSignInSummary;

class SignInServiceTest extends WebTestCase
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

    public function testSignIn()
    {
        $signInService = $this->container->get('app.sign_in_service');

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');

        $d1 = new \DateTime();
        $signInService->signIn($user, $d1);

        $d2 = $d1->modify('+1 day');
        $signInService->signIn($user, $d2);

        $d3 = $d1->modify('+1 day');
        $signInService->signIn($user, $d3);

        $d4 = $d1->modify('+1 day');
        $signInService->signIn($user, $d4);

        $d5 = $d1->modify('+1 day');
        $signInService->signIn($user, $d5);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(5, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(5, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(5, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d1, $userSignInSummary->getStartDate());

        //----------------------------------------------------------------------------

        $d6 = $d1->modify('+1 day');
        $signInService->signIn($user, $d6);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(6, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(6, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(1, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d6, $userSignInSummary->getStartDate());

        //----------------------------------------------------------------------------

        $d7 = $d1->modify('+1 day');
        $signInService->signIn($user, $d7);

        $d8 = $d1->modify('+1 day');
        $signInService->signIn($user, $d8);

        $d9 = $d1->modify('+1 day');
        $signInService->signIn($user, $d9);

        $d10 = $d1->modify('+1 day');
        $signInService->signIn($user, $d10);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(10, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(10, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(5, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d6, $userSignInSummary->getStartDate());

        //----------------------------------------------------------------------------

        $d11 = $d1->modify('+1 day');
        $signInService->signIn($user, $d11);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(11, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(11, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(1, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d11, $userSignInSummary->getStartDate());

        //----------------------------------------------------------------------------

        $d12 = $d1->modify('+1 day');
        $signInService->signIn($user, $d12);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(12, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(12, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(2, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d12, $userSignInSummary->getStartDate());

        //----------------------------------------------------------------------------

        $d1->modify('+1 day');
        $d1->modify('+1 day');
        $d14 = $d1;
        $signInService->signIn($user, $d11);

        $userSignInDetails = $this->em->getRepository('WenwenFrontendBundle:UserSignInDetail')->findAll();
        $this->assertEquals(13, sizeof($userSignInDetails));

        $userSignInSummary = $this->em->getRepository('WenwenFrontendBundle:UserSignInSummary')->findOneByUser(array('user' => $user));
        $this->assertEquals(13, $userSignInSummary->getTotalSignInCount());
        $this->assertEquals(1, $userSignInSummary->getConsecutiveDays());
        $this->assertEquals($d14, $userSignInSummary->getStartDate());
    }
}
