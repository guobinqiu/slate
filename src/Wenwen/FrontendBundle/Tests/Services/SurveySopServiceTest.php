<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\SurveySop;
use Jili\ApiBundle\Entity\SopRespondent;
use Wenwen\FrontendBundle\Model\OwnerType;

class SurveySopServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $surveySopService;

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

        $this->surveySopService = $container->get('app.survey_sop_service');
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
        $this->em->close();
        $this->em = null;
        $this->surveySopService = null;
        $this->userService = null;
    }

    public function testSurveySopService()
    {
        $json =
        '{
            "survey_id": "8006",
            "quota_id": "46737",
            "cpi": "0",
            "ir": "0",
            "loi": "20",
            "is_answered": "0",
            "is_closed": "0",
            "title": "\u5173\u4e8e\u7f8e\u5bb9\u65b9\u9762\u7684\u8c03\u67e5",
            "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=22681",
            "is_fixed_loi": "1",
            "is_notifiable": "1",
            "date": "2016-11-30",
            "extra_info": {
                "point": {
                     "screenout": "2",
                     "quotafull": "1",
                     "complete": "400"
                }
            }
        }';

        $surveyData = json_decode($json, true);

        $this->surveySopService->createOrUpdateSurvey($surveyData); //create
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => 8006));
        $this->assertEquals(0, $survey->getCpi());
        $this->assertEquals(0, $survey->getIr());
        $this->assertEquals(0, $survey->getIsClosed());
        $this->assertNull($survey->getClosedAt());

        $surveyData['cpi'] = 1.23;
        $surveyData['ir'] = 1;
        $surveyData['is_closed'] = 1;
        $this->surveySopService->createOrUpdateSurvey($surveyData); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => 8006));
        $this->assertEquals(1.23, $survey->getCpi());
        $this->assertEquals(1, $survey->getIr());
        $this->assertEquals(1, $survey->getIsClosed());
        $this->assertNotNull($survey->getClosedAt());

        $surveyData['is_closed'] = 0;
        $this->surveySopService->createOrUpdateSurvey($surveyData); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => 8006));
        $this->assertEquals(0, $survey->getIsClosed());
        $this->assertNull($survey->getClosedAt());

        $this->surveySopService->createOrUpdateSurvey($surveyData); //do nothing
        $surveys = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findBy(array('surveyId' => 8006));
        $this->assertCount(1, $surveys);
    }

    public function testProcessSurveyEndlink_OK(){

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRegisterCompleteDate((new \DateTime())->add(new \DateInterval('P01D')));
        $user->setPoints(100);
        $user->setRewardMultiple(1);

        $this->em->persist($user);
        $this->em->flush();

        $userId = $user->getId();
        $completeNBefore = $user->getCompleteN();
        $pointBefore = $user->getPoints();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($userId);
        $sopRespondent->setAppId(27);

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $appMid = $sopRespondent->getAppMid();

        $surveyId = 20131;

        $surveySop = new SurveySop();
        $surveySop->setSurveyId($surveyId);
        $surveySop->setQuotaId(32423);
        $surveySop->setLoi(10);
        $surveySop->setIr(20);
        $surveySop->setTitle('test title');
        $surveySop->setCompletePoint(300);
        $surveySop->setScreenoutPoint(20);
        $surveySop->setQuotafullPoint(20);
        $this->em->persist($surveySop);
        $this->em->flush();


        $answerStatus = SurveyStatus::STATUS_COMPLETE;
        $clientIp = 'xx.xx.xx.xx';

        $tid = $this->surveySopService->createSurveyToken($surveyId, $userId);

        echo PHP_EOL;
        echo 'tid=' . $tid . PHP_EOL;


        $point = $this->surveySopService->processSurveyEndlink($surveyId, $tid, $appMid, $answerStatus, $clientIp);

        echo 'point=' . $point . PHP_EOL;

        $userAfter = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);

        $this->assertEquals( $pointBefore + 300, $userAfter->getPoints(), 'Points should +300.');
        $this->assertEquals( $completeNBefore + 1, $userAfter->getCompleteN(), 'CompleteN should +1');

    }


    public function testIsValidQueryString_invalid_params(){
        $rtn = $this->surveySopService->isValidQueryString('a');
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryString_appIdNotExist(){
        $params = array();
        $params['sig'] = 'fake sig';
        $rtn = $this->surveySopService->isValidQueryString($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryString_sigNotExist(){
        $params = array();
        $params['app_id'] = 'fake app_id';
        $rtn = $this->surveySopService->isValidQueryString($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryString_authenticationFailure(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);
        $params = array();
        $params['sig'] = 'fake sig';
        $params['app_id'] = $sopAppDataSpring['app_id'];
        $params['time'] = time();
        $rtn = $this->surveySopService->isValidQueryString($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryString_app_id_not_exist(){
        $params = array();
        $params['sig'] = 'fake sig';
        $params['app_id'] = 'fake app_id';
        $params['time'] = time();
        $rtn = $this->surveySopService->isValidQueryString($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryString_success(){

        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);

        $params = array();
        $params['app_id'] = $sopAppDataSpring['app_id'];
        $params['time'] = time();
        $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sopAppDataSpring['app_secret']);

        $rtn = $this->surveySopService->isValidQueryString($params);
        $this->assertTrue($rtn);
    }

    public function testIsValidQueryStringByAppMid_invalid_params(){
        $rtn = $this->surveySopService->isValidQueryStringByAppMid('a');
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_appMidNotExist(){
        $params = array();
        $params['sig'] = 'fake sig';
        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_sigNotExist(){
        $params = array();
        $params['app_mid'] = 'fake app_mid';
        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_authenticationFailure(){

        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setAppId($sopAppDataSpring['app_id']);

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $params = array();
        $params['app_mid'] = $sopRespondent->getAppMid();
        $params['sig'] = 'fake sig';
        $params['time'] = 'fake time';

        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_respondent_not_exist(){

        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setAppId($sopAppDataSpring['app_id']);

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $params = array();
        $params['app_mid'] = 'fake app_mid';
        $params['sig'] = 'fake sig';
        $params['time'] = 'fake time';

        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_app_id_not_exist(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setAppId(10); // fake app_id not exist

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $params = array();
        $params['app_mid'] = $sopRespondent->getAppMid();
        $params['sig'] = 'fake sig';
        $params['time'] = 'fake time';

        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertFalse($rtn);
    }

    public function testIsValidQueryStringByAppMid_success(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);

        $user = new User();
        $this->em->persist($user);
        $this->em->flush();

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setAppId($sopAppDataSpring['app_id']); // fake app_id not exist

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $params = array();
        $params['app_mid'] = $sopRespondent->getAppMid();
        $params['time'] = time();
        $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sopAppDataSpring['app_secret']);


        $rtn = $this->surveySopService->isValidQueryStringByAppMid($params);
        $this->assertTrue($rtn);
    }

    public function testIsValidJSONString_invalid_jsonData(){
        $rtn = $this->surveySopService->isValidJSONString(null, 'fake sig');
        $this->assertFalse($rtn);
    }

    public function testIsValidJSONString_invalid_xSopSig(){
        $rtn = $this->surveySopService->isValidJSONString('fake json data', null);
        $this->assertFalse($rtn);
    }

    public function testIsValidJSONString_app_id_not_set(){
        $rtn = $this->surveySopService->isValidJSONString('fake json data', 'fake sig');
        $this->assertFalse($rtn);
    }

    public function testIsValidJSONString_app_id_not_exist(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);
        $params = array();
        $params['app_id'] = 'fake app_id';
        $params['time'] = time();
        $params['par1'] = 'par1value';
        $jsonData = json_encode($params);

        $rtn = $this->surveySopService->isValidJSONString($jsonData, 'fake sig');
        $this->assertFalse($rtn);
    }

    public function testIsValidJSONString_authentication_failure(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);
        $params = array();
        $params['app_id'] = $sopAppDataSpring['app_id'];
        $params['time'] = time();
        $params['par1'] = 'par1value';
        $jsonData = json_encode($params);

        $rtn = $this->surveySopService->isValidJSONString($jsonData, 'fake sig');
        $this->assertFalse($rtn);
    }

    public function testIsValidJSONString_success(){
        $sopAppDataSpring = $this->userService->getSopCredentialsByOwnerType(OwnerType::DATASPRING);
        $params = array();
        $params['app_id'] = $sopAppDataSpring['app_id'];
        $params['time'] = time();
        $params['par1'] = 'par1value';
        $jsonData = json_encode($params);

        $xSopSig = \SOPx\Auth\V1_1\Util::createSignature($jsonData, $sopAppDataSpring['app_secret']);

        $rtn = $this->surveySopService->isValidJSONString($jsonData, $xSopSig);
        $this->assertTrue($rtn);
    }
}