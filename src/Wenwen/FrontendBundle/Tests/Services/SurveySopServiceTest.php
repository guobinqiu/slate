<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;

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
        $this->assertEmpty($survey->getClosedAt());

        $surveyData['cpi'] = 1.23;
        $surveyData['ir'] = 1;
        $surveyData['is_closed'] = 1;
        $this->surveySopService->createOrUpdateSurvey($surveyData); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneBy(array('surveyId' => 8006));
        $this->assertEquals(1.23, $survey->getCpi());
        $this->assertEquals(1, $survey->getIr());
        $this->assertEquals(1, $survey->getIsClosed());
        $this->assertNotEmpty($survey->getClosedAt());

        $this->surveySopService->createOrUpdateSurvey($surveyData); //do nothing
        $surveys = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findBy(array('surveyId' => 8006));
        $this->assertCount(1, $surveys);
    }
}