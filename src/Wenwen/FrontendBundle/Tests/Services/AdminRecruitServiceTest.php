<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\UserTrack;


class AdminRecruitServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $adminRecruitService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->adminRecruitService = $container->get('app.admin_recruit_service');

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

    public function testGetDailyReport_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P30D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test01@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX01');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();


        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P10D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test02@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX01');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();

        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P10D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test03@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX02');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();




        $from = (new \DateTime())->sub(new \DateInterval('P30D'))->setTime(0,0,0); 
        $to = (new \DateTime())->setTime(0,0,0);

        $result = $this->adminRecruitService->getDailyReport($from, $to);

        //var_dump($result);
        $this->assertEquals(3, count($result['titles']), '应该有3个title');
        $this->assertEquals(2, count($result['reports']), '应该有2个report');
    }

    public function testGetMonthlyReport_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P80D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test01@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX01');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();


        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P50D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test02@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX01');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();

        $registerCompleteDate = (new \DateTime())->sub(new \DateInterval('P40D')); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test03@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $userTrack = new UserTrack();
        $userTrack->setSignInCount(1);
        $userTrack->setRegisterRoute('XXX02');
        $userTrack->setCurrentSignInAt($registerCompleteDate);
        $userTrack->setCurrentSignInIp('xxx');
        $userTrack->setUser($user);

        $this->em->persist($user);
        $this->em->persist($userTrack);
        $this->em->flush();




        //$from = (new \DateTime())->sub(new \DateInterval('P30D'))->setTime(0,0,0); 
        //$to = (new \DateTime())->setTime(0,0,0);

        $result = $this->adminRecruitService->getMonthlyReport();

        //var_dump($result);
        $this->assertEquals(3, count($result['titles']), '应该有3个title');
        //$this->assertEquals(3, count($result['reports']), '应该有3个report');
    }



}