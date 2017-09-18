<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Entity\SurveySop;
use Jili\ApiBundle\Entity\SopRespondent;
use Wenwen\FrontendBundle\Model\OwnerType;

class SopRespondentServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $sopRespondentService;

    private $userService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->sopRespondentService = $container->get('app.sop_respondent_service');
        $this->userService = $container->get('app.user_service');

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->clear();
        $this->em = null;
        $this->sopRespondentService = null;
        $this->userService = null;
    }

    public function testCreateSopRespondent() {

        $dummyOwnerType = OwnerType::INTAGE;

        $user = new User();

        $userTrack = new UserTrack();
        $userTrack->setUser($user);
        $userTrack->setOwnerType($dummyOwnerType);

        $user->setUserTrack($userTrack);

        $this->em->persist($user);
        $this->em->persist($userTrack);

        $this->em->flush();

        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(
            array(
                'userId' => $user->getId(),
            )
        );

        $this->assertNull($sopRespondent);


        $this->sopRespondentService->createSopRespondent($user->getId());

        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(
            array(
                'userId' => $user->getId(),
            )
        );

        $this->assertNotNull($sopRespondent);
        $this->assertEquals($user->getId(), $sopRespondent->getUserId(), 'same user id');
    }

    public function testGetSopRespondentByUserId_Exist(){

        $dummyUserId = 1;
        $dummyAppMid = 'mid1';
        $dummyAppId = 22;

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($dummyUserId);
        $sopRespondent->setAppMid($dummyAppMid);
        $sopRespondent->setAppId($dummyAppId);

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $sr = $this->sopRespondentService->getSopRespondentByUserId($dummyUserId);
        $this->assertNotNull($sr);
        $this->assertEquals($dummyUserId, $sr->getUserId());

    }

    public function testGetSopRespondentByUserId_NotExist(){

        $dummyOwnerType = OwnerType::INTAGE;

        $user = new User();

        $userTrack = new UserTrack();
        $userTrack->setUser($user);
        $userTrack->setOwnerType($dummyOwnerType);

        $user->setUserTrack($userTrack);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();

        $sr = $this->sopRespondentService->getSopRespondentByUserId($user->getId());
        $this->assertNotNull($sr);
        $this->assertEquals($user->getId(), $sr->getUserId());
        $this->assertNotNull($sr->getAppMid());
    }
}