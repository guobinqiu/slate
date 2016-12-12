<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;
use Wenwen\FrontendBundle\Model\SurveyStatus;


class AdminSurveyPartnerServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $adminSurveyPartnerService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->adminSurveyPartnerService = $container->get('app.admin_survey_partner_service');

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

    public function testGetSurveyPartnerList_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $page = 1;
        $limit = 3;

        $pagination = $this->adminSurveyPartnerService->getSurveyPartnerList($page, $limit);

        $this->assertEquals(1, count($pagination), 'Should found one result.');
    }

    public function testOpenSurveyPartner_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $this->adminSurveyPartnerService->openSurveyPartner($surveyPartner->getId());

        $resultSurveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartner->getId(),
                        ));

        $this->assertEquals(SurveyPartner::STATUS_OPEN, $resultSurveyPartner->getStatus(), 'Status of SurveyPartner should be open.');
    }

    public function testCloseSurveyPartner_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_EXPENSE);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $this->adminSurveyPartnerService->closeSurveyPartner($surveyPartner->getId());

        $resultSurveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartner->getId(),
                        ));

        $this->assertEquals(SurveyPartner::STATUS_CLOSE, $resultSurveyPartner->getStatus(), 'Status of SurveyPartner should be open.');
    }

    public function testCreateUpdateSurveyPartner_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());

        $this->adminSurveyPartnerService->createUpdateSurveyPartner($surveyPartner);

        $resultSurveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneBy(
                    array('id' => $surveyPartner->getId(),
                        ));

        $this->assertEquals(SurveyPartner::STATUS_OPEN, $resultSurveyPartner->getStatus(), 'Status of SurveyPartner should be open.');
        $this->assertEquals(SurveyPartner::GENDER_BOTH, $resultSurveyPartner->getGender(), 'Status of SurveyPartner should be open.');
    }

    public function testFindSurveyPartner_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $resultSurveyPartner = $this->adminSurveyPartnerService->findSurveyPartner($surveyPartner->getId());

        $this->assertEquals(SurveyPartner::STATUS_OPEN, $resultSurveyPartner->getStatus(), 'Status of SurveyPartner should be open.');
        $this->assertEquals(SurveyPartner::GENDER_BOTH, $resultSurveyPartner->getGender(), 'Status of SurveyPartner should be open.');
    }

    public function testGetSurveyPartnerParticipationSummary_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);
        $this->em->persist($user);

        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user2->setRegisterCompleteDate(new \DateTime());
        $user2->setPoints(100);
        $user2->setRewardMultiple(1);
        $user2->setUserProfile($userProfile);
        $this->em->persist($user2);

        $this->em->flush();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());
        $this->em->persist($surveyPartner);
        
        $surveyPartnerParticipationHistory11 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory11->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory11->setUser($user);
        $surveyPartnerParticipationHistory11->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory11->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory11);

        $surveyPartnerParticipationHistory12 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory12->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory12->setUser($user);
        $surveyPartnerParticipationHistory12->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory12->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory12);

        $surveyPartnerParticipationHistory21 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory21->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory21->setUser($user2);
        $surveyPartnerParticipationHistory21->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory21->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory21);

        $surveyPartnerParticipationHistory22 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory22->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory22->setUser($user2);
        $surveyPartnerParticipationHistory22->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory22->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory22);

        $surveyPartnerParticipationHistory23 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory23->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory23->setUser($user2);
        $surveyPartnerParticipationHistory23->setStatus(SurveyStatus::STATUS_COMPLETE);
        $surveyPartnerParticipationHistory23->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory23);


        $this->em->flush();

        $summary = $this->adminSurveyPartnerService->getSurveyPartnerParticipationSummary($surveyPartner);
        $this->assertEquals(2, $summary['initCount'], 'initCount should be 2.');
        $this->assertEquals(2, $summary['forwardCount'], 'forwardCount should be 2.');
        $this->assertEquals(1, $summary['completeCount'], 'completeCount should be 1.');

    }

    public function testGetSurveyPartnerParticipationDetail_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);
        $this->em->persist($user);

        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user2->setRegisterCompleteDate(new \DateTime());
        $user2->setPoints(100);
        $user2->setRewardMultiple(1);
        $user2->setUserProfile($userProfile);
        $this->em->persist($user2);

        $this->em->flush();

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());
        $this->em->persist($surveyPartner);
        
        $surveyPartnerParticipationHistory11 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory11->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory11->setUser($user);
        $surveyPartnerParticipationHistory11->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory11->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory11);

        $surveyPartnerParticipationHistory12 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory12->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory12->setUser($user);
        $surveyPartnerParticipationHistory12->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory12->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory12);

        $surveyPartnerParticipationHistory21 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory21->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory21->setUser($user2);
        $surveyPartnerParticipationHistory21->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory21->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory21);

        $surveyPartnerParticipationHistory22 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory22->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory22->setUser($user2);
        $surveyPartnerParticipationHistory22->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory22->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory22);

        $surveyPartnerParticipationHistory23 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory23->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory23->setUser($user2);
        $surveyPartnerParticipationHistory23->setStatus(SurveyStatus::STATUS_COMPLETE);
        $surveyPartnerParticipationHistory23->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory23);


        $this->em->flush();

        $pagination = $this->adminSurveyPartnerService->getSurveyPartnerParticipationDetail($surveyPartner, 1);
        $this->assertEquals(5, count($pagination), 'total result count should be 5.');
        
    }

    public function testGetParticipationDailyReport_ok(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);
        $this->em->persist($user);

        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user2->setRegisterCompleteDate(new \DateTime());
        $user2->setPoints(100);
        $user2->setRewardMultiple(1);
        $user2->setUserProfile($userProfile);
        $this->em->persist($user2);

        $surveyPartner1 = new SurveyPartner();
        $surveyPartner1->setPartnerName('triples');
        $surveyPartner1->setType(SurveyPartner::TYPE_COST);
        $surveyPartner1->setSurveyId('1001');
        $surveyPartner1->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner1->setTitle('测试用问卷标题1002');
        $surveyPartner1->setReentry(false);
        $surveyPartner1->setLoi(10);
        $surveyPartner1->setIr(50);
        $surveyPartner1->setCompletePoint(298);
        $surveyPartner1->setScreenoutPoint(10);
        $surveyPartner1->setQuotafullPoint(2);
        $surveyPartner1->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner1->setNewUserOnly(true);
        $surveyPartner1->setMinAge(10);
        $surveyPartner1->setMaxAge(100);
        $surveyPartner1->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner1->setProvince('江西，贵州');
        $surveyPartner1->setCity('上海，合肥');
        $surveyPartner1->setCreatedAt(new \DateTime());
        $this->em->persist($surveyPartner1);

        $surveyPartner2 = new SurveyPartner();
        $surveyPartner2->setPartnerName('triples');
        $surveyPartner2->setType(SurveyPartner::TYPE_COST);
        $surveyPartner2->setSurveyId('1002');
        $surveyPartner2->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner2->setTitle('测试用问卷标题1002');
        $surveyPartner2->setReentry(false);
        $surveyPartner2->setLoi(10);
        $surveyPartner2->setIr(50);
        $surveyPartner2->setCompletePoint(298);
        $surveyPartner2->setScreenoutPoint(10);
        $surveyPartner2->setQuotafullPoint(2);
        $surveyPartner2->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner2->setNewUserOnly(true);
        $surveyPartner2->setMinAge(10);
        $surveyPartner2->setMaxAge(100);
        $surveyPartner2->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner2->setProvince('江西，贵州');
        $surveyPartner2->setCity('上海，合肥');
        $surveyPartner2->setCreatedAt(new \DateTime());
        $this->em->persist($surveyPartner2);

        $surveyPartnerParticipationHistory11 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory11->setSurveyPartner($surveyPartner1);
        $surveyPartnerParticipationHistory11->setUser($user);
        $surveyPartnerParticipationHistory11->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory11->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory11);

        $surveyPartnerParticipationHistory12 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory12->setSurveyPartner($surveyPartner1);
        $surveyPartnerParticipationHistory12->setUser($user);
        $surveyPartnerParticipationHistory12->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory12->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory12);

        $surveyPartnerParticipationHistory21 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory21->setSurveyPartner($surveyPartner2);
        $surveyPartnerParticipationHistory21->setUser($user2);
        $surveyPartnerParticipationHistory21->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory21->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory21);

        $createTime22 = clone $now;
        $createTime22->sub(new \DateInterval('P03D'));

        $surveyPartnerParticipationHistory22 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory22->setSurveyPartner($surveyPartner2);
        $surveyPartnerParticipationHistory22->setUser($user2);
        $surveyPartnerParticipationHistory22->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory22->setCreatedAt($createTime22);
        $this->em->persist($surveyPartnerParticipationHistory22);

        $surveyPartnerParticipationHistory23 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory23->setSurveyPartner($surveyPartner2);
        $surveyPartnerParticipationHistory23->setUser($user2);
        $surveyPartnerParticipationHistory23->setStatus(SurveyStatus::STATUS_COMPLETE);
        $surveyPartnerParticipationHistory23->setCreatedAt($createTime22);
        $this->em->persist($surveyPartnerParticipationHistory23);

        $this->em->flush();


        $rtn = $this->adminSurveyPartnerService->getParticipationDailyReport();
/*
        foreach($rtn['dailyReports'] as $key => $value){
            echo $key . PHP_EOL;
            echo $value['initCount'] .PHP_EOL;
            echo $value['forwardCount'] .PHP_EOL;
            echo $value['completeCount'] .PHP_EOL;
            echo $value['screenoutCount'] .PHP_EOL;
            echo $value['quotafullCount'] .PHP_EOL;
            echo $value['errorCount'] .PHP_EOL;
        }
*/
        $this->assertEquals(31, count($rtn['dailyReports']));
        $now = (new \DateTime())->format('Y-m-d');
        $this->assertEquals(2 ,$rtn['dailyReports'][$now]['initCount']);
        $this->assertEquals(1 ,$rtn['dailyReports'][$now]['forwardCount']);
        $this->assertEquals('50%' ,$rtn['dailyReports'][$now]['cvrInitToForward']);

    }


}