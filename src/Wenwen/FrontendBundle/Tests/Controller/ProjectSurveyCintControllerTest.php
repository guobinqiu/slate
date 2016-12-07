<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\Controller\ProjectSurveyCintController;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadSurveyCintData;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;

class ProjectSurveyCintControllerTest extends WebTestCase
{
    private $client;

    private $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $client = static::createClient(array(), array('HTTPS' => true));
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        if (in_array($this->getName(), array('testInformationAction', 'testAgreementCompleteAction')))
        {
            $loader = new Loader();
            $loader->addFixture(new LoadUserData());
            $loader->addFixture(new LoadSurveyCintData());
            $purger = new ORMPurger();
            $executor = new ORMExecutor($em, $purger);
            $executor->execute($loader->getFixtures());
        }

        $this->client = $client;
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
        $this->client = null;
        $this->container = null;
    }

    public function testInformationAction()
    {
        $url = $this->container->get('router')->generate('_cint_project_survey_information');
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //login 后
        $session = $this->container->get('session');
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();

        $survey_id = 10000;
        $cint_research = array();
        $cint_research['title'] = 'dummy title';
        $cint_research['difficulty'] = 'normal';
        $cint_research['loi'] = 10;
        $cint_research['extra_info']['point']['complete'] = 400;
        $cint_research['url'] = 'dummy_url';
        $cint_research['survey_id'] = $survey_id;

        $url = $this->container->get('router')->generate('_cint_project_survey_information', array('cint_research' => $cint_research, 'difficulty' => '普通'));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //$app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
            //'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_INIT,
            'userId' => $users[0]->getId(),
        ));
        $this->assertNotNull($statusHistory);
    }

    public function testForwardAction()
    {
        $url = $this->container->get('router')->generate('_cint_project_survey_forward');
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //login 后
        $session = $this->container->get('session');
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();

        $survey_id = 10000;
        $url = 'dummy_url';
        $cint_research = array();
        $cint_research['survey_id'] = $survey_id;
        $cint_research['url'] = $url;

        $cint_research = $this->container->get('app.survey_cint_service')->addSurveyUrlToken($cint_research, $users[0]->getId());
        $this->assertNotEquals($url, $cint_research['url']);

        $token = $this->container->get('app.survey_cint_service')->getSurveyToken($survey_id, $users[0]->getId());
        $this->assertEquals($url . '&sop_custom_token=' . $token, $cint_research['url']);

        $url = $this->container->get('router')->generate('_cint_project_survey_forward', array('cint_research' => $cint_research));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        //$app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
            //'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_FORWARD,
            'userId' => $users[0]->getId(),
        ));
        $this->assertNotNull($statusHistory);

        $createdAt = new \Datetime();
        $statusHistory->setCreatedAt($createdAt->modify('-5 minute'));
        $this->em->flush();
    }

    public function testEndlinkAction()
    {
        $url = $this->container->get('router')->generate('_cint_project_survey_information');
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //login 后
        $session = $this->container->get('session');
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();

        $survey_id = 10000;
        $token = $this->container->get('app.survey_cint_service')->getSurveyToken($survey_id, $users[0]->getId());
        $app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $url = $this->container->get('router')->generate('_cint_project_survey_endlink', array (
            'survey_id' => $survey_id,
            'answer_status' => SurveyStatus::STATUS_COMPLETE,
            'app_mid' => $app_mid,
            'tid' => $token,
        ));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        //$app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
            //'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_COMPLETE,
            'userId' => $users[0]->getId(),
        ));
        $this->assertNotNull($statusHistory);

        $statusHistories = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findBy(array(
            //'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'userId' => $users[0]->getId(),
        ));
        $this->assertCount(3, $statusHistories);

        $prizeTicket = $this->em->getRepository('WenwenFrontendBundle:PrizeTicket')->findOneBySurveyId($survey_id);
        $this->assertNotNull($prizeTicket);
        $this->assertEquals(PrizeItem::TYPE_BIG, $prizeTicket->getType());

//        $taskHistory = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($users[0]->getId() % 10))->findOneByUserId($users[0]->getId());
//        $this->assertEquals(400, $taskHistory->getPoint());
//        $this->assertEquals(TaskType::SURVEY, $taskHistory->getTaskType());
//        $this->assertEquals(CategoryType::CINT_COST, $taskHistory->getCategoryType());
//
//        $point = $this->em->getRepository('JiliApiBundle:PointHistory0' . ($users[0]->getId() % 10))->findOneByUserId($users[0]->getId());
//        $this->assertEquals(400, $point->getPointChangeNum());
//        $this->assertEquals(CategoryType::CINT_COST, $point->getReason());
//
//        $this->em->detach($users[0]);
//        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($users[0]->getId());
//        $this->assertEquals(500, $user->getPoints());

        $crawler = $this->client->request('GET', $url);
        $statusHistories = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findBy(array(
            //'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'userId' => $users[0]->getId(),
        ));
        $this->assertCount(3, $statusHistories);
    }

    public function testAgreementCompleteAction()
    {
        $session = $this->container->get('session');
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();
        $point_1 = $users[0]->getPoints();
        $user_id = $users[0]->getId();
        $session->set('uid', $user_id);
        $session->save();

        {
            //cint agreement end page with invalid sig
            $invalid_params = array (
                'agreement_status' => 'AGREED',
                'time' => time(),
                'sig' => 'fake'
            );

            $url = $this->container->get('router')->generate('_cint_project_survey_agreement_complete', $invalid_params);
            $crawler = $this->client->request('GET', $url);
            //$this->assertEquals(404, $client->getResponse()->getStatusCode());
        }

        {
            //cint agreement end page
            $params = array (
                'agreement_status' => 'AGREED',
                'time' => time()
            );
            $sop_config = $this->container->getParameter('sop');
            $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sop_config['auth']['app_secret']);

            $url = $this->container->get('router')->generate('_cint_project_survey_agreement_complete', $params);
            $crawler = $this->client->request('GET', $url);
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

            $this->em->clear();

            // check DB
            $history = $this->em->getRepository('WenwenAppBundle:CintUserAgreementParticipationHistory')->findOneByUserId($users[0]->getId());
            $this->assertNotEmpty($history);
            $this->assertEquals(10, $history->getAgreementStatus());

            $task = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findOneByUserId($user_id);
            $this->assertEquals(1, $task->getPoint());
            $this->assertEquals(ProjectSurveyCintController::COMMENT, $task->getTaskName());

            $point = $this->em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
            $this->assertEquals(1, $point->getPointChangeNum());
            $this->assertEquals(CategoryType::CINT_EXPENSE, $point->getReason());

            $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            $this->assertEquals($point_1 + 1, $user->getPoints());
        }
    }
}