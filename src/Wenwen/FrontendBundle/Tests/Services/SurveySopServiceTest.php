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

class SurveySopServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $surveySopService;

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

        $this->em->persist($sopRespondent);
        $this->em->flush();

        $appMid = $sopRespondent->getId();

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
}