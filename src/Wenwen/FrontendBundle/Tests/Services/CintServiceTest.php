<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;

class CintSurveyServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $cintSurveyService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->cintSurveyService = $container->get('app.cint_survey_service');

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

    public function testSopSurveyService()
    {
        $json =
        '{
            "survey_id": "10000",
            "quota_id": "20000",
            "cpi": "0.00",
            "ir": "80",
            "loi": "10",
            "is_answered": "0",
            "is_closed": "0",
            "title": "Cint Dummy Survey",
            "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
            "date": "2015-01-01",
            "extra_info": {
                "point": {
                    "complete": "400",
                    "screenout": "10",
                    "quotafull": "10"
                }
            }
        }';

        $survey = json_decode($json, true);

        $this->cintSurveyService->createOrUpdateResearchSurvey($survey); //create
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => 10000));
        $this->assertEquals(0.00, $survey->getCpi());
        $this->assertEquals(80, $survey->getIr());
        $this->assertEquals(0, $survey->getIsClosed());

        $survey['cpi'] = 1.23;
        $survey['ir'] = 70;
        $survey['is_closed'] = 1;
        $this->cintSurveyService->createOrUpdateResearchSurvey($survey); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findOneBy(array('surveyId' => 10000));
        $this->assertEquals(1.23, $survey->getCpi());
        $this->assertEquals(70, $survey->getIr());
        $this->assertEquals(1, $survey->getIsClosed());

        $this->cintSurveyService->createOrUpdateResearchSurvey($survey); //do nothing
        $surveys = $this->em->getRepository('WenwenFrontendBundle:SurveyCint')->findBy(array('surveyId' => 10000));
        $this->assertCount(1, $surveys);
    }
}