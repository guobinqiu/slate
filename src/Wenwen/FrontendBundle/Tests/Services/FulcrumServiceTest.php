<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;

class FulcrumSurveyServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $fulcrumSurveyService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->fulcrumSurveyService = $container->get('app.fulcrum_survey_service');

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

    public function testFulcrumSurveyService()
    {
        $json =
        '{
            "survey_id": "4",
            "quota_id": "10",
            "cpi": "0.00",
            "ir": "80",
            "loi": "31",
            "title": "Fulcrum Dummy Survey 4",
            "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=18",
            "date": "2015-01-01",
            "extra_info": {
                "point": {
                    "complete": "300"
                }
            }
        }';

        $arr = json_decode($json, true);

        $this->fulcrumSurveyService->createOrUpdateResearchSurvey($arr); //create
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyFulcrum')->findOneBy(array('surveyId' => 4));
        $this->assertEquals(0.00, $survey->getCpi());
        $this->assertEquals(80, $survey->getIr());

        $arr['cpi'] = 1.23;
        $arr['ir'] = 70;
        $this->fulcrumSurveyService->createOrUpdateResearchSurvey($arr); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyFulcrum')->findOneBy(array('surveyId' => 4));
        $this->assertEquals(1.23, $survey->getCpi());
        $this->assertEquals(70, $survey->getIr());

        $this->fulcrumSurveyService->createOrUpdateResearchSurvey($arr); //do nothing
        $surveys = $this->em->getRepository('WenwenFrontendBundle:SurveyFulcrum')->findBy(array('surveyId' => 4));
        $this->assertCount(1, $surveys);
    }
}