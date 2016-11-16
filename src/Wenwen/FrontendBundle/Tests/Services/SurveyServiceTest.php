<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;

class SurveyServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $surveyService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->surveyService = $container->get('app.survey_service');

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
        $this->em->close();
    }

    public function testGetOrderedHtmlServeyList()
    {
        // a fake user_id for input
        $user_id = '12345';

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';


        // call function for testing
        $this->surveyService->setDummy(true);
        $html_survey_list = $this->surveyService->getOrderedHtmlSurveyList($user_id, $locationInfo);

        // 只要有返回值就OK 返回值的对错不在这里检查
        $this->assertTrue(is_array($html_survey_list));
    }

    public function testGetSurveyResearchArray()
    {
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
        $this->em->flush();

        $partnerId = '9001';
        $surveyId = '1001';

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyPartnerParticipationHistory::STATUS_INIT);
        $surveyPartnerParticipationHistory->setCreatedAt($now);

        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        // call function for testing
        $partnerResearchs = $this->surveyService->getSurveyResearchArray($user, $locationInfo);

        // 能查到一个surveyPartner项目
        $this->assertEquals(1, count($partnerResearchs), 'Should find one surveyPartner.');
    }
}