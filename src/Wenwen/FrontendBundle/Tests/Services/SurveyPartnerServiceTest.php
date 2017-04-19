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

class SurveyPartnerServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $surveyPartnerService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->surveyPartnerService = $container->get('app.survey_partner_service');

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

    public function testIsValidSurveyPartnerForUser_registerAtEqualFrom(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(24);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate((new \DateTime())->add(new \DateInterval('P01D')));
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('success', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_registerAtUnderFrom(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(24);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('Only allow registered over '.$surveyPartner->getRegisteredAtFrom() . ' hours', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_registerAtEqualTo(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(24);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate((new \DateTime())->add(new \DateInterval('P03D')));
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('success', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_registerAtOverTo(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(24);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate((new \DateTime())->add(new \DateInterval('P04D'))); // 4天96小时前注册的
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('Only allow registered under '.$surveyPartner->getRegisteredAtTo() . ' hours', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_registerAfterOneDay(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(24);
        $surveyPartner->setRegisteredAtTo(2400000);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate((new \DateTime())->add(new \DateInterval('P01D')));
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('success', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_genderBoth(){

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setPartnerName('triples');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);

        $this->assertEquals('success', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_genderNG(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_FEMALE);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('genderCheckFailed', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_minAgeNG(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_MALE);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('2010-01-01'); // 6 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('ageCheckFailed', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_maxAgeNG(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_MALE);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1910-01-01'); // 106 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('ageCheckFailed', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_locationNG(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_MALE);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('locationCheckFailed . city=' .$locationInfo['city']. ' province=' . $locationInfo['province'], $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_locationOK(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_MALE);
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('success', $rtn['result'] );
    }

    public function testIsValidSurveyPartnerForUser_locationNotFound(){

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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setProvince('江西，贵州');
        $surveyPartner->setCity('上海，合肥');
        $surveyPartner->setGender(SurveyPartner::GENDER_MALE);
        $surveyPartner->setCreatedAt(new \DateTime());


        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $locationInfo = array();
        $locationInfo['status'] = false;
        $locationInfo['province'] = null;
        $locationInfo['city'] = null;

        $rtn = $this->surveyPartnerService->isValidSurveyPartnerForUser($surveyPartner, $user, $locationInfo);
        $this->assertEquals('success', $rtn['result'] );
    }

    public function testGetSurveyPartnerListForUser_forTestUser()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('ds-Product-china@d8aspring.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        // 项目处于close状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1001');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1001');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(90);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1002');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1002');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_FEMALE);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1003');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1003');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1004');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1004');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_CLOSE);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(2, count($rtn), '测试用户，显示INIT状态的项目');

    }

    public function testGetSurveyPartnerListForUser_noOpenProjects()
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

        // 项目处于close状态
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
        $surveyPartner->setStatus(SurveyPartner::STATUS_CLOSE);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('1002');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(0, count($rtn), '不存在处于OPEN状态的项目');

    }

    public function testGetSurveyPartnerListForUser_userRegisteredBefore3Days()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $now = new \DateTime(); // current time
        $registerCompleteDate = $now->sub(new \DateInterval('P04D')); // 用户注册于4天前

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($registerCompleteDate);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        // 项目处于open状态
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(0, count($rtn), '用户注册于3天前');
    }



    public function testGetSurveyPartnerListForUser_noParticipationHistory()
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

        // 项目处于open状态
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(1, count($rtn), '没有参与记录');

    }


    public function testGetSurveyPartnerListForUser_initHistory()
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
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
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
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory->setCreatedAt($now);

        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('456');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(2, count($rtn), '参与记录状态为init');
    }

    public function testGetSurveyPartnerListForUser_reentryHistory()
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

        $surveyId = '1001';

        // 项目处于open状态 并且是可以中途退出的
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory->setCreatedAt($now);

        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(1, count($rtn), '参与记录状态为init');
    }

    public function testGetSurveyPartnerListForUser_forwardHistory()
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

        $surveyId = '1001';

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
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
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory);

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn = $this->surveyPartnerService->getSurveyPartnerListForUser($user, $locationInfo);

        $this->assertEquals(0, count($rtn), '参与记录状态为forward');
    }

    public function testProcessInformation()
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


        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 1 项目处于open状态，没有该用户的参与记录 ->显示
        $surveyId1 = '1001';
        $title1 = '测试用问卷标题1';
        $content1 = '测试用问卷说明1';
        $loi1 = 10;
        $ir1 = 10;
        $completePoint1 = 298;
        $screenoutPoint1 = 10;
        $quotafullPoint1 = 2;

        $surveyPartner1 = new SurveyPartner();
        $surveyPartner1->setPartnerName('triples');
        $surveyPartner1->setType(SurveyPartner::TYPE_COST);
        $surveyPartner1->setSurveyId($surveyId1);
        $surveyPartner1->setUrl($url);
        $surveyPartner1->setTitle($title1);
        $surveyPartner1->setContent($content1);
        $surveyPartner1->setReentry(false);
        $surveyPartner1->setLoi($loi1);
        $surveyPartner1->setIr($ir1);
        $surveyPartner1->setCompletePoint($completePoint1);
        $surveyPartner1->setScreenoutPoint($screenoutPoint1);
        $surveyPartner1->setQuotafullPoint($quotafullPoint1);
        $surveyPartner1->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner1->setNewUserOnly(true);
        $surveyPartner1->setRegisteredAtFrom(0);
        $surveyPartner1->setRegisteredAtTo(72);
        $surveyPartner1->setMinAge(10);
        $surveyPartner1->setMaxAge(100);
        $surveyPartner1->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner1->setCreatedAt($now);

        $this->em->persist($surveyPartner1);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn1 = $this->surveyPartnerService->processInformation($user, $surveyPartner1->getId(), $locationInfo);
        $this->assertEquals('success', $rtn1['status'], 'Should be success.');
        $this->assertEquals($surveyPartner1->getId() . ' ' . $title1, $rtn1['title'], 'Should be success.');
        $this->assertEquals($content1, $rtn1['content'], 'Should be success.');
        $this->assertEquals($loi1, $rtn1['loi'], 'Should be success.');
        $this->assertEquals($completePoint1, $rtn1['completePoint'], 'Should be success.');
        $this->assertEquals($url, $rtn1['url'], 'Should be success.');
        $this->assertEquals('简单', $rtn1['difficulty'], 'Should be success.');
        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner1,
                        ));
        $this->assertEquals(SurveyStatus::STATUS_INIT, $surveyPartnerParticipationHistory->getStatus(), 'Should be init.');

        // 2 项目处于open状态，该用户有参与记录，且参与状态为init ->显示
        $surveyId2 = '1002';
        $title2 = '测试用问卷标题2';
        $content2 = '测试用问卷说明2';
        $loi2 = 10;
        $ir2 = 10;
        $completePoint2 = 298;
        $screenoutPoint2 = 10;
        $quotafullPoint2 = 2;

        $surveyPartner2 = new SurveyPartner();
        $surveyPartner2->setPartnerName('triples');
        $surveyPartner2->setType(SurveyPartner::TYPE_COST);
        $surveyPartner2->setSurveyId($surveyId2);
        $surveyPartner2->setUrl($url);
        $surveyPartner2->setTitle($title2);
        $surveyPartner2->setContent($content2);
        $surveyPartner2->setReentry(false);
        $surveyPartner2->setLoi($loi2);
        $surveyPartner2->setIr($ir2);
        $surveyPartner2->setCompletePoint($completePoint2);
        $surveyPartner2->setScreenoutPoint($screenoutPoint2);
        $surveyPartner2->setQuotafullPoint($quotafullPoint2);
        $surveyPartner2->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner2->setNewUserOnly(true);
        $surveyPartner2->setRegisteredAtFrom(0);
        $surveyPartner2->setRegisteredAtTo(72);
        $surveyPartner2->setMinAge(10);
        $surveyPartner2->setMaxAge(100);
        $surveyPartner2->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner2->setCreatedAt($now);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner2);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory2->setCreatedAt($now);

        $this->em->persist($surveyPartner2);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn2 = $this->surveyPartnerService->processInformation($user, $surveyPartner2->getId(), $locationInfo);
        $this->assertEquals('success', $rtn2['status'], 'Should be success.');
        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner2,
                        ));
        $this->assertEquals(SurveyStatus::STATUS_INIT, $surveyPartnerParticipationHistory->getStatus(), 'Should be init.');

        // 3 项目处于open状态，该用户有参与记录，且参与状态为reentry ->显示
        $surveyId3 ='1003';

        $surveyPartner3 = new SurveyPartner();
        $surveyPartner3->setPartnerName('triples');
        $surveyPartner3->setType(SurveyPartner::TYPE_COST);
        $surveyPartner3->setSurveyId($surveyId3);
        $surveyPartner3->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner3->setTitle('测试用问卷标题3');
        $surveyPartner3->setReentry(true);
        $surveyPartner3->setLoi(10);
        $surveyPartner3->setIr(50);
        $surveyPartner3->setCompletePoint(298);
        $surveyPartner3->setScreenoutPoint(10);
        $surveyPartner3->setQuotafullPoint(2);
        $surveyPartner3->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner3->setNewUserOnly(true);
        $surveyPartner3->setRegisteredAtFrom(0);
        $surveyPartner3->setRegisteredAtTo(72);
        $surveyPartner3->setMinAge(10);
        $surveyPartner3->setMaxAge(100);
        $surveyPartner3->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner3->setCreatedAt($now);

        $surveyPartnerParticipationHistory3 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory3->setSurveyPartner($surveyPartner3);
        $surveyPartnerParticipationHistory3->setUser($user);
        $surveyPartnerParticipationHistory3->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory3->setCreatedAt($now);

        $this->em->persist($surveyPartner3);
        $this->em->persist($surveyPartnerParticipationHistory3);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn3 = $this->surveyPartnerService->processInformation($user, $surveyPartner3->getId(), $locationInfo);
        $this->assertEquals('success', $rtn3['status'], 'Should be success.');
        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner3,
                        ));
        $this->assertEquals(SurveyStatus::STATUS_FORWARD, $surveyPartnerParticipationHistory->getStatus());

        // 4 项目处于init状态，没有该用户的参与记录 ->不显示
        $surveyId4 = '1004';

        $surveyPartner4 = new SurveyPartner();
        $surveyPartner4->setPartnerName('triples');
        $surveyPartner4->setType(SurveyPartner::TYPE_COST);
        $surveyPartner4->setSurveyId($surveyId4);
        $surveyPartner4->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner4->setTitle('测试用问卷标题4');
        $surveyPartner4->setReentry(false);
        $surveyPartner4->setLoi(10);
        $surveyPartner4->setIr(50);
        $surveyPartner4->setCompletePoint(298);
        $surveyPartner4->setScreenoutPoint(10);
        $surveyPartner4->setQuotafullPoint(2);
        $surveyPartner4->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner4->setNewUserOnly(true);
        $surveyPartner4->setRegisteredAtFrom(0);
        $surveyPartner4->setRegisteredAtTo(72);
        $surveyPartner4->setMinAge(10);
        $surveyPartner4->setMaxAge(100);
        $surveyPartner4->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner4->setCreatedAt($now);

        $this->em->persist($surveyPartner4);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn4 = $this->surveyPartnerService->processInformation($user, $surveyPartner4->getId(), $locationInfo);
        $this->assertEquals('failure', $rtn4['status'], 'Should be failure because this project is not open.');

        // 5 项目处于close状态，没有该用户的参与记录 ->不显示
        $surveyId5 = '1005';

        $surveyPartner5 = new SurveyPartner();
        $surveyPartner5->setPartnerName('triples');
        $surveyPartner5->setType(SurveyPartner::TYPE_COST);
        $surveyPartner5->setSurveyId($surveyId5);
        $surveyPartner5->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner5->setTitle('测试用问卷标题5');
        $surveyPartner5->setReentry(false);
        $surveyPartner5->setLoi(10);
        $surveyPartner5->setIr(50);
        $surveyPartner5->setCompletePoint(298);
        $surveyPartner5->setScreenoutPoint(10);
        $surveyPartner5->setQuotafullPoint(2);
        $surveyPartner5->setStatus(SurveyPartner::STATUS_CLOSE);
        $surveyPartner5->setNewUserOnly(true);
        $surveyPartner5->setRegisteredAtFrom(0);
        $surveyPartner5->setRegisteredAtTo(72);
        $surveyPartner5->setMinAge(10);
        $surveyPartner5->setMaxAge(100);
        $surveyPartner5->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner5->setCreatedAt($now);

        $this->em->persist($surveyPartner5);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn5 = $this->surveyPartnerService->processInformation($user, $surveyPartner5->getId(), $locationInfo);
        $this->assertEquals('failure', $rtn5['status'], 'Should be failure because this project is not open.');

        // 6 项目处于open状态，该用户有参与记录，且参与状态为forward ->不显示
        $surveyId6 = '1006';

        $surveyPartner6 = new SurveyPartner();
        $surveyPartner6->setPartnerName('triples');
        $surveyPartner6->setType(SurveyPartner::TYPE_COST);
        $surveyPartner6->setSurveyId($surveyId6);
        $surveyPartner6->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner6->setTitle('测试用问卷标题6');
        $surveyPartner6->setReentry(false);
        $surveyPartner6->setLoi(10);
        $surveyPartner6->setIr(50);
        $surveyPartner6->setCompletePoint(298);
        $surveyPartner6->setScreenoutPoint(10);
        $surveyPartner6->setQuotafullPoint(2);
        $surveyPartner6->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner6->setNewUserOnly(true);
        $surveyPartner6->setRegisteredAtFrom(0);
        $surveyPartner6->setRegisteredAtTo(72);
        $surveyPartner6->setMinAge(10);
        $surveyPartner6->setMaxAge(100);
        $surveyPartner6->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner6->setCreatedAt($now);

        $surveyPartnerParticipationHistory6 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory6->setSurveyPartner($surveyPartner6);
        $surveyPartnerParticipationHistory6->setUser($user);
        $surveyPartnerParticipationHistory6->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory6->setCreatedAt($now);

        $surveyPartnerParticipationHistory7 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory7->setSurveyPartner($surveyPartner6);
        $surveyPartnerParticipationHistory7->setUser($user);
        $surveyPartnerParticipationHistory7->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory7->setCreatedAt($now);

        $this->em->persist($surveyPartner6);
        $this->em->persist($surveyPartnerParticipationHistory6);
        $this->em->persist($surveyPartnerParticipationHistory7);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn6 = $this->surveyPartnerService->processInformation($user, $surveyPartner6->getId(), $locationInfo);
        $this->assertEquals('participated', $rtn6['status'], 'Should be participated.');
    }

    public function testProcessInformation_forTestUser()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();


        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 项目处于init状态，没有该用户的参与记录 ->显示
        $surveyId1 = '1001';
        $title1 = '测试用问卷标题1';
        $content1 = '测试用问卷说明1';
        $loi1 = 10;
        $ir1 = 10;
        $completePoint1 = 298;
        $screenoutPoint1 = 10;
        $quotafullPoint1 = 2;

        $surveyPartner1 = new SurveyPartner();
        $surveyPartner1->setPartnerName('triples');
        $surveyPartner1->setType(SurveyPartner::TYPE_COST);
        $surveyPartner1->setSurveyId($surveyId1);
        $surveyPartner1->setUrl($url);
        $surveyPartner1->setTitle($title1);
        $surveyPartner1->setContent($content1);
        $surveyPartner1->setReentry(false);
        $surveyPartner1->setLoi($loi1);
        $surveyPartner1->setIr($ir1);
        $surveyPartner1->setCompletePoint($completePoint1);
        $surveyPartner1->setScreenoutPoint($screenoutPoint1);
        $surveyPartner1->setQuotafullPoint($quotafullPoint1);
        $surveyPartner1->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner1->setNewUserOnly(true);
        $surveyPartner1->setRegisteredAtFrom(0);
        $surveyPartner1->setRegisteredAtTo(72);
        $surveyPartner1->setMinAge(10);
        $surveyPartner1->setMaxAge(100);
        $surveyPartner1->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner1->setCreatedAt($now);

        $this->em->persist($surveyPartner1);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '天津市';
        $locationInfo['clientIp'] = '139.111.111.111';

        $rtn1 = $this->surveyPartnerService->processInformation($user, $surveyPartner1->getId(), $locationInfo);
        $this->assertEquals('success', $rtn1['status'], 'Should be success.');
        $this->assertEquals($surveyPartner1->getId() . ' ' . $title1, $rtn1['title'], 'Should be success.');
        $this->assertEquals($content1, $rtn1['content'], 'Should be success.');
        $this->assertEquals($loi1, $rtn1['loi'], 'Should be success.');
        $this->assertEquals($completePoint1, $rtn1['completePoint'], 'Should be success.');
        $this->assertEquals($url, $rtn1['url'], 'Should be success.');
        $this->assertEquals('简单', $rtn1['difficulty'], 'Should be success.');
        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner1,
                        ));
        $this->assertEquals(SurveyStatus::STATUS_INIT, $surveyPartnerParticipationHistory->getStatus(), 'Should be init.');
    }

    public function testRedirectToSurvey_forTestUser()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $userProfile = new UserProfile();
        $userProfile->setBirthday('1980-01-01'); // 36 岁
        $userProfile->setSex(1); // 男性 

        $user = new User();
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $partnername = 'triples';
        $url = 'http://www.d8aspring.com/?uid=__UID__';

        //  项目处于open状态，但是项目要求60岁到100岁，该用户是36岁，所以不允许参加
        $surveyId1 = '1001';
        $title1 = '测试用问卷标题1';
        $content1 = '测试用问卷说明1';
        $loi1 = 10;
        $ir1 = 10;
        $completePoint1 = 298;
        $screenoutPoint1 = 10;
        $quotafullPoint1 = 2;

        $surveyPartner1 = new SurveyPartner();
        $surveyPartner1->setPartnerName($partnername);
        $surveyPartner1->setType(SurveyPartner::TYPE_COST);
        $surveyPartner1->setSurveyId($surveyId1);
        $surveyPartner1->setUrl($url);
        $surveyPartner1->setTitle($title1);
        $surveyPartner1->setContent($content1);
        $surveyPartner1->setReentry(false);
        $surveyPartner1->setLoi($loi1);
        $surveyPartner1->setIr($ir1);
        $surveyPartner1->setCompletePoint($completePoint1);
        $surveyPartner1->setScreenoutPoint($screenoutPoint1);
        $surveyPartner1->setQuotafullPoint($quotafullPoint1);
        $surveyPartner1->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner1->setNewUserOnly(true);
        $surveyPartner1->setRegisteredAtFrom(0);
        $surveyPartner1->setRegisteredAtTo(72);
        $surveyPartner1->setMinAge(60);
        $surveyPartner1->setMaxAge(100);
        $surveyPartner1->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner1->setCreatedAt($now);

        $this->em->persist($surveyPartner1);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn1 = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner1->getId(), $locationInfo);
        $this->assertEquals('success', $rtn1['status'], 'Should be success.');
    }

    public function testRedirectToSurvey_NotAllowed()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $partnername = 'triples';
        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 1 项目处于open状态，但是项目要求60岁到100岁，该用户是36岁，所以不允许参加
        $surveyId1 = '1001';
        $title1 = '测试用问卷标题1';
        $content1 = '测试用问卷说明1';
        $loi1 = 10;
        $ir1 = 10;
        $completePoint1 = 298;
        $screenoutPoint1 = 10;
        $quotafullPoint1 = 2;

        $surveyPartner1 = new SurveyPartner();
        $surveyPartner1->setPartnerName($partnername);
        $surveyPartner1->setType(SurveyPartner::TYPE_COST);
        $surveyPartner1->setSurveyId($surveyId1);
        $surveyPartner1->setUrl($url);
        $surveyPartner1->setTitle($title1);
        $surveyPartner1->setContent($content1);
        $surveyPartner1->setReentry(false);
        $surveyPartner1->setLoi($loi1);
        $surveyPartner1->setIr($ir1);
        $surveyPartner1->setCompletePoint($completePoint1);
        $surveyPartner1->setScreenoutPoint($screenoutPoint1);
        $surveyPartner1->setQuotafullPoint($quotafullPoint1);
        $surveyPartner1->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner1->setNewUserOnly(true);
        $surveyPartner1->setRegisteredAtFrom(0);
        $surveyPartner1->setRegisteredAtTo(72);
        $surveyPartner1->setMinAge(60);
        $surveyPartner1->setMaxAge(100);
        $surveyPartner1->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner1->setCreatedAt($now);

        $this->em->persist($surveyPartner1);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn1 = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner1->getId(), $locationInfo);
        $this->assertEquals('notallowed', $rtn1['status'], 'Not allowed because of age.');
        $this->assertEquals('ageCheckFailed', $rtn1['errMsg'], 'Not allowed because of age.');
    }

    public function testRedirectToSurvey_success_open_and_no_history()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 1 项目处于open状态，没有该用户的参与记录 ->显示
        $surveyId = '1001';
        $title = '测试用问卷标题1';
        $content = '测试用问卷说明1';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setContent($content);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('success', $rtn['status'], 'Should be success. 项目处于open状态，没有该用户的参与记录 ->显示');
        $this->assertEquals('http://www.d8aspring.com/?uid=' . $user->getId(), $rtn['surveyUrl'], 'Should be success. url被正常替换');
        $count = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->countByUserAndSurveyPartner($user, $surveyPartner);
        $this->assertEquals(2, $count, 'Participation history should increased to 2.');
    }

    public function testRedirectToSurvey_success_only_init_history()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 2 项目处于open状态，该用户有参与记录，且参与状态为init ->显示
        $surveyId = '1002';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setContent($content);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('success', $rtn['status'], 'Should be success.');
        $count = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->countByUserAndSurveyPartner($user, $surveyPartner);
        $this->assertEquals(2, $count, 'Participation history should increased to 2.');
    }

    public function testRedirectToSurvey_success_init_and_forward_history()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 3 项目处于open状态，该用户有参与记录，且参与状态为reentry ->显示
        $surveyId ='1003';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle('测试用问卷标题3');
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory1);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now);
        $this->em->persist($surveyPartnerParticipationHistory2);

        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn3 = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('success', $rtn3['status'], 'Should be success.');
        $count = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->countByUserAndSurveyPartner($user, $surveyPartner);
        $this->assertEquals(2, $count, 'Participation history not changed. still 2.');
    }

    public function testRedirectToSurvey_failure_with_init_survey()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 4 项目处于init状态，没有该用户的参与记录 ->不显示
        $surveyId = '1004';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题4');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('failure', $rtn['status'], 'Should be failure because this project is not open.');
    }

    public function testRedirectToSurvey_failure_with_close_survey()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 5 项目处于close状态，没有该用户的参与记录 ->不显示
        $surveyId = '1005';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle('测试用问卷标题5');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_CLOSE);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('failure', $rtn['status'], 'Should be failure because this project is not open.');
    }
    
    public function testRedirectToSurvey_failure_with_forward_history()
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
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setUserProfile($userProfile);

        $this->em->persist($user);
        $this->em->flush();

        $url = 'http://www.d8aspring.com/?uid=__UID__';

        // 6 项目处于open状态，该用户有参与记录，且参与状态为forward ->不显示
        $surveyId = '1006';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('triples');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题6');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint(10);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->persist($surveyPartnerParticipationHistory1);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();

        $locationInfo = array();
        $locationInfo['status'] = true;
        $locationInfo['province'] = '广东省';
        $locationInfo['city'] = '合肥市';
        $locationInfo['clientIp'] = '191.111.111.111';

        $rtn = $this->surveyPartnerService->redirectToSurvey($user, $surveyPartner->getId(), $locationInfo);
        $this->assertEquals('participated', $rtn['status'], 'Should be participated.');
    }

    public function testProcessTriplesEndlink_forTestUser()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $user = new User();
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于close状态 ->不处理endlink
        $surveyId = '1005';
        $partnerName = 'triples';
        $answerStatus = 'complete';
        $key = 'surveyPartner商业问卷';
        $title = '测试用问卷标题5';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 50;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;
        $minAge = 10;
        $maxAge = 100;
        $clientIp = '12334343';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge($minAge);
        $surveyPartner->setMaxAge($maxAge);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $this->em->persist($surveyPartnerParticipationHistory1);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setClientIP($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);

        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('success', $rtn['status'], '正常处理处于init状态的问卷项目');

        $prizeTicket = $this->em->getRepository('WenwenFrontendBundle:PrizeTicket')->findOneBy(
                    array(
                        'comment' => $key,
                        ));
        $this->assertEquals($surveyId, $prizeTicket->getSurveyId(), 'Prize ticket should be created.');
        $this->assertEquals($user, $prizeTicket->getUser(), 'Prize ticket should be created.');


    }

    public function testProcessTriplesEndlink_tooFastComplete()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $user = new User();
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于close状态 ->不处理endlink
        $surveyId = '1005';
        $partnerName = 'triples';
        $answerStatus = 'complete';
        $key = 'XKDGR';
        $title = '测试用问卷标题5';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 1;
        $ir = 50;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;
        $minAge = 10;
        $maxAge = 100;
        $clientIp = '12334343';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge($minAge);
        $surveyPartner->setMaxAge($maxAge);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT0M15S')));
        $this->em->persist($surveyPartnerParticipationHistory1);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now);
        $surveyPartnerParticipationHistory2->setClientIP($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);

        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('success', $rtn['status'], 'complete的太快了');

        $afterUser = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());
        $this->assertEquals($currentPoint + $screenoutPoint, $afterUser->getPoints(), 'complete的太快了，只给screenout的积分');

        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                array('user' => $user,
                    'surveyPartner' => $surveyPartner,
                    'status' => SurveyStatus::STATUS_SCREENOUT,
                    ));

        $this->assertEquals('This is a too fast complete. userId = ' . $user->getId() . ' surveyId=' . $surveyId . ' partnerName=' . $partnerName , $surveyPartnerParticipationHistory->getComment(), 'complete的太快了，所以增加一条screenout记录');

    }

    public function testProcessTriplesEndlink_projectClosed()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于close状态 ->不处理endlink
        $surveyId = '1005';
        $partnerName = 'triples';
        $answerStatus = 'complete';
        $key = 'XKDGR';
        $title = '测试用问卷标题5';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 50;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;
        $minAge = 10;
        $maxAge = 100;

        $clientIp = 'sdfsdf';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_CLOSE);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge($minAge);
        $surveyPartner->setMaxAge($maxAge);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('failure', $rtn['status'], 'Should be failure because this project is not open. 项目处于close状态 ->不处理endlink');
    }

    public function testProcessTriplesEndlink_noParticipationHistory()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();


        // 项目处于open状态，没有该用户的参与记录 ->不处理endlink
        $partnerName = 'triples';

        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $surveyId = '1001';
        $key = 'XHDFDF';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $title = '测试用问卷标题1';
        $content = '测试用问卷说明1';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $clientIp = '124312';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setContent($content);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('failure', $rtn['status'], 'Should be failure. 项目处于open状态，没有该用户的参与记录 ->不处理endlink');

    }

    public function testProcessTriplesEndlink_reentry_userNotExist()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于init状态，没有该用户的参与记录 ->不处理endlink
        $partnerName = 'triples';
        $surveyId = '1001';
        $answerStatus = 'complete';
        $key = 'XKDGR';
        $title = '测试用问卷标题4';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 50;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;
        $minAge = 10;
        $maxAge = 100;
        $clientIp = 'asdf';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_INIT);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge($minAge);
        $surveyPartner->setMaxAge($maxAge);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink('1234', $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('failure', $rtn['status'], 'Should be failure because this project is not open.');
    }

    public function testProcessTriplesEndlink_reentry_success()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $inviter = new User();
        $inviter->setEmail('inviter@test.com');
        $inviter->setRegisterCompleteDate($now);
        $inviter->setPoints($currentPoint);
        $inviter->setRewardMultiple(1);
        $this->em->persist($inviter);
        $this->em->flush();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);
        $user->setInviteId($inviter->getId());
        $user->setIsEmailConfirmed(1);
        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户有2条参与记录 ->处理endlink
        $partnerName = 'triples';
        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $surveyId ='1003';
        $key = 'surveyPartner商业问卷';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $clientIp = 'asdfasdf';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);
        $this->em->persist($surveyPartner);

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $this->em->persist($surveyPartnerParticipationHistory1);

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('success', $rtn['status'], 'Should be success. 项目处于open状态，该用户有参与记录，且参与状态为init和forward ->处理endlink');
        $this->assertEquals(true, $rtn['ticketCreated'], 'Ticket should be created.');
        $count = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->countByUserAndSurveyPartner($user, $surveyPartner);
        $this->assertEquals(3, $count, 'Participation historys should increase to 3.');
        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner,
                        'status' => $answerStatus,
                        ));
        $this->assertEquals($key, $surveyPartnerParticipationHistory->getUKey(), 'Participation key should be updated.');
        $this->assertEquals($currentPoint + $completePoint, $user->getPoints(), 'Point should be rewarded.');
        $this->assertEquals($currentPoint + $completePoint * 0.1, $inviter->getPoints(), 'Point should be rewarded.');
        $prizeTicket = $this->em->getRepository('WenwenFrontendBundle:PrizeTicket')->findOneBy(
                    array(
                        'comment' => $key,
                        ));
        $this->assertEquals($surveyId, $prizeTicket->getSurveyId(), 'Prize ticket should be created.');
        $this->assertEquals($user, $prizeTicket->getUser(), 'Prize ticket should be created.');
    }

    public function testProcessTriplesEndlink_statusOfParticipationHistoryIsNotCorrect()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户只有有一条参与记录->不处理endlink
        $partnerName = 'triples';
        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $surveyId = '1002';
        $key = 'XHDFDF';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $clientIp = 'adfsaf';

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl($url);
        $surveyPartner->setTitle($title);
        $surveyPartner->setContent($content);
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $surveyPartnerParticipationHistory = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory->setUser($user);
        $surveyPartnerParticipationHistory->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));

        $this->em->persist($surveyPartner);
        $this->em->persist($surveyPartnerParticipationHistory);
        $this->em->flush();

        $rtn = $this->surveyPartnerService->processTriplesEndlink($user->getId(), $answerStatus, $surveyId, $partnerName, $key, $clientIp);
        $this->assertEquals('failure', $rtn['status'], 'Should be success. 项目处于open状态，该用户有参与记录，且参与状态为init ->不处理endlink');
    }

    public function testReward_complete()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $inviter = new User();
        $inviter->setEmail('inviter@test.com');
        $inviter->setRegisterCompleteDate($now);
        $inviter->setPoints($currentPoint);
        $inviter->setRewardMultiple(1);
        $this->em->persist($inviter);
        $this->em->flush();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);
        $user->setInviteId($inviter->getId());

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户有2条参与记录 ->处理endlink
        $key = 'KEY123';
        $partnerName = 'triples';
        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $surveyId ='1003';
        $key = 'XHDFDF';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);


        $rtn = $this->surveyPartnerService->reward($surveyPartner, $user, $answerStatus, $key);
        $this->assertEquals($completePoint, $rtn['rewardedPoint'], '处理结果的增加积分数');
        $this->assertEquals(true, $rtn['ticketCreated'], '处理结果的奖券发放状态');

        $rtnUser = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());
        $this->assertEquals($completePoint+$currentPoint, $rtnUser->getPoints(), '积分应增加' . $completePoint);        
    }

    public function testReward_screenout()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $inviter = new User();
        $inviter->setEmail('inviter@test.com');
        $inviter->setRegisterCompleteDate($now);
        $inviter->setPoints($currentPoint);
        $inviter->setRewardMultiple(1);
        $this->em->persist($inviter);
        $this->em->flush();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);
        $user->setInviteId($inviter->getId());

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户有2条参与记录 ->处理endlink
        $key = 'KEY123';
        $partnerName = 'triples';
        $answerStatus = SurveyStatus::STATUS_SCREENOUT;
        $surveyId ='1003';
        $key = 'XHDFDF';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);


        $rtn = $this->surveyPartnerService->reward($surveyPartner, $user, $answerStatus, $key);
        $this->assertEquals($screenoutPoint, $rtn['rewardedPoint'], '处理结果的增加积分数');
        $this->assertEquals(true, $rtn['ticketCreated'], '处理结果的奖券发放状态');

        $rtnUser = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());
        $this->assertEquals($screenoutPoint+$currentPoint, $rtnUser->getPoints(), '积分应增加' . $screenoutPoint);        
    }

    public function testReward_quotafull()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $inviter = new User();
        $inviter->setEmail('inviter@test.com');
        $inviter->setRegisterCompleteDate($now);
        $inviter->setPoints($currentPoint);
        $inviter->setRewardMultiple(1);
        $this->em->persist($inviter);
        $this->em->flush();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);
        $user->setInviteId($inviter->getId());

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户有2条参与记录 ->处理endlink
        $key = 'KEY123';
        $partnerName = 'triples';
        $answerStatus = SurveyStatus::STATUS_QUOTAFULL;
        $surveyId ='1003';
        $key = 'XHDFDF';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);


        $rtn = $this->surveyPartnerService->reward($surveyPartner, $user, $answerStatus, $key);
        $this->assertEquals($quotafullPoint, $rtn['rewardedPoint'], '处理结果的增加积分数');
        $this->assertEquals(true, $rtn['ticketCreated'], '处理结果的奖券发放状态');

        $rtnUser = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());
        $this->assertEquals($quotafullPoint+$currentPoint, $rtnUser->getPoints(), '积分应增加' . $quotafullPoint);        
    }

    public function testReward_other()
    {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time
        $currentPoint = 100;

        $inviter = new User();
        $inviter->setEmail('inviter@test.com');
        $inviter->setRegisterCompleteDate($now);
        $inviter->setPoints($currentPoint);
        $inviter->setRewardMultiple(1);
        $this->em->persist($inviter);
        $this->em->flush();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);
        $user->setInviteId($inviter->getId());

        $this->em->persist($user);
        $this->em->flush();

        // 项目处于open状态，该用户有2条参与记录 ->处理endlink
        $key = 'KEY123';
        $partnerName = 'triples';
        $answerStatus = 'ERROR';
        $surveyId ='1003';
        $key = 'XHDFDF';
        $title = '测试用问卷标题2';
        $content = '测试用问卷说明2';
        $url = 'http://www.d8aspring.com/?uid=__UID__';
        $loi = 10;
        $ir = 10;
        $completePoint = 298;
        $screenoutPoint = 10;
        $quotafullPoint = 2;

        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName($partnerName);
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId($surveyId);
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle($title);
        $surveyPartner->setReentry(true);
        $surveyPartner->setLoi($loi);
        $surveyPartner->setIr($ir);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint($quotafullPoint);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);


        $rtn = $this->surveyPartnerService->reward($surveyPartner, $user, $answerStatus, $key);
        $this->assertEquals(0, $rtn['rewardedPoint'], '处理结果的增加积分数');
        $this->assertEquals(false, $rtn['ticketCreated'], '处理结果的奖券发放状态');

        $rtnUser = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($user->getId());
        $this->assertEquals($currentPoint, $rtnUser->getPoints(), '积分应增加' . 0);
    }


    public function testIsValidEndlink(){

        $result = $this->surveyPartnerService->isValidEndlink('screenout', 'triples', null, 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('quotafull', 'triples', null, 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('error', 'triples', null, 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('xxx', 'triples', null, 1, '099104111d001exljg');
        $this->assertTrue(! $result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('complete', 'triples', null, 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('complete', 'triples', 'http:\/\/r.researchpanelasia_error.com\/redirect\/reverse\/9ed68ef0e7615306a793792905330e85\/error?uid=099104111d001exljg', 1, '099104111d001exljg');
        $this->assertTrue(! $result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('complete', 'triples', 'http:\/\/r.researchpanelasia.com\/redirect\/reverse\/9ed68ef0e7615306a793792905330e85\/error?uid=099104111d001exljg', 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('complete', 'forsurvey', null, 1, '099104111d001exljg');
        $this->assertTrue($result['status']);

        $result = $this->surveyPartnerService->isValidEndlink('complete', 'xxx', null, 1, '099104111d001exljg');
        $this->assertTrue(! $result['status']);
    }

    public function testProcessForSurveyEndlink_invalidUid_not3params(){
        $uid = '';
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Not a valid uid. uid=' . $uid, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_invalidUid_noMatchedSecretKey(){
        $userId = '123423';
        $surveyPartnerId = '1013';

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId, 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a10a3');
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Not a valid uid. uid=' . $uid, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_surveyPartnerIdNotExist(){
        $userId = '123423';
        $surveyPartnerId = '1013';

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);

        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Not exist surveyPartnerId. surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg'] , 'Error message is not correct.');

    }

    public function testProcessForSurveyEndlink_userIdNotExist(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);
        $this->em->flush();

        $userId = '123456';
        $surveyPartnerId = $surveyPartner->getId();;

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Not exist userId. userId=' . $userId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_testUser_surveyPartnerNotInInitStatus(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('rpa-sys-china@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Test user, surveyPartner not in init status. surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_surveyPartnerNotInOpenStatus(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Normal user, surveyPartner not in open status. surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_ParticipationHistoryNotCorrect(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Participation history is not correct. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_ParticipationHistoryForwardNotExist(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $this->em->persist($surveyPartnerParticipationHistory1);


        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_COMPLETE);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Participation history in forward is not exist. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_ParticipationHistoryUKeyNotMatched(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $this->em->persist($surveyPartnerParticipationHistory1);


        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setUKey($uid . 'ddd');
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Participation history UKey not match uid. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_ParticipationHistoryClientIpNotMatched(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
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
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $this->em->persist($surveyPartnerParticipationHistory1);


        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT30M')));
        $surveyPartnerParticipationHistory2->setUKey($uid);
        $surveyPartnerParticipationHistory2->setClientIp('xxx');
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('failure', $rtn['status']);
        $this->assertEquals('Participation clientIp does not match. userId=' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);

    }

    public function testProcessForSurveyEndlink_normalUser_tooFastComplete(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $screenoutPoint = 10;

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint(298);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $currentPoint = 100;

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = 'complete';
        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT01M')));
        $this->em->persist($surveyPartnerParticipationHistory1);


        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT01M')));
        $surveyPartnerParticipationHistory2->setUKey($uid);
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('success', $rtn['status']);
        $this->assertEquals(SurveyStatus::STATUS_SCREENOUT, $rtn['answerStatus']);
        $this->assertEquals('This is a too fast complete. userId = ' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $rtn['errMsg']);
        $this->assertEquals($currentPoint + $screenoutPoint, $user->getPoints());

        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner,
                        'status' => SurveyStatus::STATUS_SCREENOUT,
                        ));

        $this->assertEquals('This is a too fast complete. userId = ' . $userId . ' surveyPartnerId=' . $surveyPartnerId, $surveyPartnerParticipationHistory->getComment());

    }

    public function testProcessForSurveyEndlink_normalUser_success(){

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $now = new \DateTime(); // current time

        $completePoint = 298;
        $screenoutPoint = 10;

        // 项目处于open状态
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setPartnerName('forsurvey');
        $surveyPartner->setType(SurveyPartner::TYPE_COST);
        $surveyPartner->setSurveyId('noneed');
        $surveyPartner->setUrl('http://www.d8aspring.com/?uid=__UID__');
        $surveyPartner->setTitle('测试用问卷标题1');
        $surveyPartner->setReentry(false);
        $surveyPartner->setLoi(10);
        $surveyPartner->setIr(50);
        $surveyPartner->setCompletePoint($completePoint);
        $surveyPartner->setScreenoutPoint($screenoutPoint);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
        $surveyPartner->setNewUserOnly(true);
        $surveyPartner->setRegisteredAtFrom(0);
        $surveyPartner->setRegisteredAtTo(72);
        $surveyPartner->setMinAge(10);
        $surveyPartner->setMaxAge(100);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setCreatedAt($now);

        $this->em->persist($surveyPartner);

        $now = new \DateTime(); // current time

        $currentPoint = 100;

        $user = new User();
        $user->setEmail('normal@d8aspring.com');
        $user->setRegisterCompleteDate($now);
        $user->setPoints($currentPoint);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $surveyPartnerId = $surveyPartner->getId();

        $uid = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);
        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $clientIp = 'xxx.xxx.xxx.xxx';

        $surveyPartnerParticipationHistory1 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory1->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory1->setUser($user);
        $surveyPartnerParticipationHistory1->setStatus(SurveyStatus::STATUS_INIT);
        $surveyPartnerParticipationHistory1->setCreatedAt($now->sub(new \DateInterval('P0DT05M')));
        $this->em->persist($surveyPartnerParticipationHistory1);


        $surveyPartnerParticipationHistory2 = new SurveyPartnerParticipationHistory();
        $surveyPartnerParticipationHistory2->setSurveyPartner($surveyPartner);
        $surveyPartnerParticipationHistory2->setUser($user);
        $surveyPartnerParticipationHistory2->setStatus(SurveyStatus::STATUS_FORWARD);
        $surveyPartnerParticipationHistory2->setCreatedAt($now->sub(new \DateInterval('P0DT05M')));
        $surveyPartnerParticipationHistory2->setUKey($uid);
        $surveyPartnerParticipationHistory2->setClientIp($clientIp);
        $this->em->persist($surveyPartnerParticipationHistory2);
        $this->em->flush();



        $rtn = $this->surveyPartnerService->processForSurveyEndlink($uid, $answerStatus, $clientIp);

        $this->assertEquals('success', $rtn['status']);
        $this->assertEquals($answerStatus, $rtn['answerStatus']);
        $this->assertEquals('', $rtn['errMsg']);
        $this->assertEquals($surveyPartner->getId() . ' ' . $surveyPartner->getTitle(), $rtn['title']);
        $this->assertEquals($completePoint, $rtn['rewardedPoint']);
        $this->assertEquals(true, $rtn['ticketCreated']);
        $this->assertEquals($currentPoint + $completePoint, $user->getPoints());

        $surveyPartnerParticipationHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->findOneBy(
                    array('user' => $user,
                        'surveyPartner' => $surveyPartner,
                        'status' => $answerStatus,
                        ));

        $this->assertEquals('', $surveyPartnerParticipationHistory->getComment());
        $this->assertEquals($clientIp, $surveyPartnerParticipationHistory->getClientIP());
        $this->assertEquals($uid, $surveyPartnerParticipationHistory->getUKey());

    }

    public function testEncodeToken(){

        $userId = 24567890;

        $surveyPartnerId = 51234;

        $encodedString = $this->surveyPartnerService->encodeToken($userId, $surveyPartnerId);

        //echo 'length=' . strlen($encodedString) . PHP_EOL;
        //echo $encodedString . PHP_EOL;

        $decodedArray = $this->surveyPartnerService->decodeToken($encodedString);

        //var_dump( $decodedArray );

    }

}