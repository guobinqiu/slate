<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadSopResearchSurveyData;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;

class ProjectSurveyControllerTest extends WebTestCase
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
            $loader->addFixture(new LoadSopResearchSurveyData());
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
        $url = $this->container->get('router')->generate('_project_survey_information');
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
        $research = array();
        $research['title'] = 'dummy title';
        $research['difficulty'] = 'normal';
        $research['loi'] = 10;
        $research['extra_info']['point']['complete'] = 400;
        $research['url'] = 'dummy_url';
        $research['survey_id'] = $survey_id;

        $url = $this->container->get('router')->generate('_project_survey_information', array('research' => $research, 'difficulty' => '普通'));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findOneBy(array(
            'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_INIT,
        ));
        $this->assertNotNull($statusHistory);
        $this->assertEquals($statusHistory->getIsAnswered(), SurveyStatus::UNANSWERED);
    }

    public function testForwardAction()
    {
        $url = $this->container->get('router')->generate('_project_survey_forward');
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
        $research = array();
        $research['survey_id'] = $survey_id;
        $research['url'] = $url;

        $research = $this->container->get('app.sop_survey_service')->addSurveyUrlToken($research, $users[0]->getId());
        $this->assertNotEquals($url, $research['url']);

        $token = $this->container->get('app.sop_survey_service')->getSurveyToken($survey_id, $users[0]->getId());
        $this->assertEquals($url . '&sop_custom_token=' . $token, $research['url']);

        $url = $this->container->get('router')->generate('_project_survey_forward', array('research' => $research));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findOneBy(array(
            'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_FORWARD
        ));
        $this->assertNotNull($statusHistory);
        $this->assertEquals($statusHistory->getIsAnswered(), SurveyStatus::UNANSWERED);

        $createdAt = new \Datetime();
        $statusHistory->setCreatedAt($createdAt->modify('-5 minute'));
        $this->em->flush();
    }

    public function testEndlinkAction()
    {
        $url = $this->container->get('router')->generate('_project_survey_information');
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
        $token = $this->container->get('app.sop_survey_service')->getSurveyToken($survey_id, $users[0]->getId());
        $app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $url = $this->container->get('router')->generate('_project_survey_endlink', array (
            'survey_id' => $survey_id,
            'answer_status' => SurveyStatus::STATUS_COMPLETE,
            'app_mid' => $app_mid,
            'tid' => $token,
        ));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $app_mid = $this->container->get('app.survey_service')->getSopRespondentId($users[0]->getId());
        $statusHistory = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findOneBy(array(
            'appMid' => $app_mid,
            'surveyId' => $survey_id,
            'status' => SurveyStatus::STATUS_COMPLETE,
        ));
        $this->assertNotNull($statusHistory);
        $this->assertEquals($statusHistory->getIsAnswered(), SurveyStatus::ANSWERED);

        $statusHistories = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findBy(array(
            'appMid' => $app_mid,
            'surveyId' => $survey_id,
        ));
        $this->assertCount(3, $statusHistories);

        $prizeTicket = $this->em->getRepository('WenwenFrontendBundle:PrizeTicket')->findOneBySurveyId($survey_id);
        $this->assertNotNull($prizeTicket);
        $this->assertEquals(PrizeItem::TYPE_BIG, $prizeTicket->getType());

        $participationHistory = $this->em->getRepository('WenwenAppBundle:SopResearchSurveyParticipationHistory')->findOneBy(array(
            'partnerAppProjectId' => $survey_id,
            'appMemberId' => $app_mid
        ));
        $this->assertNotNull($participationHistory);
        $this->assertEquals(400, $participationHistory->getPoint());

        $taskHistory = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($users[0]->getId() % 10))->findOneByUserId($users[0]->getId());
        $this->assertEquals(400, $taskHistory->getPoint());
        $this->assertEquals(TaskType::SURVEY, $taskHistory->getTaskType());
        $this->assertEquals(CategoryType::SOP_COST, $taskHistory->getCategoryType());

        $point = $this->em->getRepository('JiliApiBundle:PointHistory0' . ($users[0]->getId() % 10))->findOneByUserId($users[0]->getId());
        $this->assertEquals(400, $point->getPointChangeNum());
        $this->assertEquals(CategoryType::SOP_COST, $point->getReason());

        $this->em->detach($users[0]);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($users[0]->getId());
        $this->assertEquals(500, $user->getPoints());

        $crawler = $this->client->request('GET', $url);
        $statusHistories = $this->em->getRepository('WenwenFrontendBundle:SopResearchSurveyStatusHistory')->findBy(array(
            'appMid' => $app_mid,
            'surveyId' => $survey_id,
        ));
        $this->assertCount(3, $statusHistories);
    }
}