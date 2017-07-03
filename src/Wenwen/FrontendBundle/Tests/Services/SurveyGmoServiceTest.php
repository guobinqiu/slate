<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;

class SurveyGmoServiceTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $surveyGmoService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $this->surveyGmoService = $container->get('app.survey_gmo_service');

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
        if($this->em){
            $this->em->close();
        }
    }

    public function testSurveyGmoService()
    {
        $json = '
        [
          {
            "ans_mode": "01",
            "ans_stat_cd": "01",
            "arrivalDay": "2015/12/02",
            "custom_nm": null,
            "encryptId": "fa47bc2ad1944b7b9d7748b67260736b30c173cd99a068e3",
            "enqPerPanelStatus": "05",
            "enq_id": 629275,
            "enq_id_truenavi": null,
            "external_enq_id": null,
            "id": "dmid",
            "lg_img": "mtop_i_cate01.gif",
            "lg_nm": "通常調査",
            "logo_type": "1",
            "loi": 4,
            "main_enq_id": 629276,
            "matter_type": 9,
            "optimize_device": "3",
            "own_flag": "0",
            "page_comment": "",
            "point": 10,
            "point_min": 2,
            "point_sign": "p",
            "point_string": "最大10p",
            "point_type": 0,
            "promotion_type": "0",
            "que_num": 10,
            "redirectSt": "https://st.infopanel.jp/lpark/enqRedirect.do?",
            "research_id": 110200,
            "research_type": "2",
            "si_img": "mtop_i_stus01.gif",
            "situation": "未回答",
            "start_dt": 1448982000000,
            "status": "05",
            "title": "test survey 1"
          }
        ]
        ';
        $surveyData = json_decode($json, true)[0];

        $this->surveyGmoService->createOrUpdateSurvey($surveyData); //create
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => 110200));
        $this->assertEquals('2015/12/02', $survey->getArrivalDay());
        $this->assertEquals('test survey 1', $survey->getTitle());
        $this->assertFalse($survey->isClosed());
        $this->assertNull($survey->getClosedAt());

        $surveyData['status'] = '07';
        $this->surveyGmoService->createOrUpdateSurvey($surveyData); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => 110200));
        $this->assertTrue($survey->isClosed());
        $this->assertNotNull($survey->getClosedAt());

        $surveyData['status'] = '05';
        $this->surveyGmoService->createOrUpdateSurvey($surveyData); //update
        $survey = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findOneBy(array('researchId' => 110200));
        $this->assertFalse($survey->isClosed());
        $this->assertNull($survey->getClosedAt());

        $this->surveyGmoService->createOrUpdateSurvey($surveyData); //do nothing
        $surveys = $this->em->getRepository('WenwenFrontendBundle:SurveyGmo')->findBy(array('researchId' => 110200));
        $this->assertCount(1, $surveys);
    }

    public function testGetSurveyListJson() {
        $userId = '2707797';
        echo $this->surveyGmoService->getSurveyListJson($userId);
    }
}