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
use Wenwen\FrontendBundle\Model\OwnerType;
use Wenwen\FrontendBundle\Model\SurveyStatus;

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

        $surveyId = 10000;
        $cint_research = array();
        $cint_research['title'] = 'dummy title';
        $cint_research['difficulty'] = 'normal';
        $cint_research['loi'] = 10;
        $cint_research['extra_info']['point']['complete'] = 400;
        $cint_research['url'] = 'dummy_url';
        $cint_research['survey_id'] = $surveyId;

        $url = $this->container->get('router')->generate('_cint_project_survey_information', array('cint_research' => $cint_research, 'difficulty' => '普通'));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
            'surveyId' => $surveyId,
            'status' => SurveyStatus::STATUS_INIT,
            'userId' => $users[0]->getId(),
        ));
        $this->assertNotNull($participation);
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

        $surveyId = 10000;
        $url = 'dummy_url';
        $cint_research = array();
        $cint_research['survey_id'] = $surveyId;
        $cint_research['url'] = $url;

        $cint_research = $this->container->get('app.survey_cint_service')->addSurveyUrlToken($cint_research, $users[0]->getId());
        $this->assertNotEquals($url, $cint_research['url']);

        $token = $this->container->get('app.survey_cint_service')->getSurveyToken($surveyId, $users[0]->getId());
        $this->assertEquals($url . '&sop_custom_token=' . $token, $cint_research['url']);

        $url = $this->container->get('router')->generate('_cint_project_survey_forward', array('cint_research' => $cint_research));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $participation = $this->em->getRepository('WenwenFrontendBundle:SurveyCintParticipationHistory')->findOneBy(array(
            'surveyId' => $surveyId,
            'status' => SurveyStatus::STATUS_FORWARD,
            'userId' => $users[0]->getId(),
        ));
        $this->assertNotNull($participation);

        $createdAt = new \Datetime();
        $participation->setCreatedAt($createdAt->modify('-5 minute'));
        $participation->setUpdatedAt($createdAt->modify('-5 minute'));
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

        $surveyId = 10000;
        $token = $this->container->get('app.survey_cint_service')->getSurveyToken($surveyId, $users[0]->getId());

        $sopRespondent = $this->container->get('app.survey_sop_service')->createSopRespondent($users[0]->getId());
        $appMid = $sopRespondent->getAppMid();

        $url = $this->container->get('router')->generate('_cint_project_survey_endlink', array (
            'survey_id' => $surveyId,
            'answer_status' => SurveyStatus::STATUS_COMPLETE,
            'app_mid' => $appMid,
            'tid' => $token,
        ));
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $prizeTicket = $this->em->getRepository('WenwenFrontendBundle:PrizeTicket')->findOneBySurveyId($surveyId);
        $this->assertNotNull($prizeTicket);
        $this->assertEquals(PrizeItem::TYPE_BIG, $prizeTicket->getType());
    }

    public function testAgreementCompleteAction()
    {
        $session = $this->container->get('session');
        $users = $this->em->getRepository('WenwenFrontendBundle:User')->findAll();
        $point_1 = $users[0]->getPoints();
        $user_id = $users[0]->getId();
        $session->set('uid', $user_id);
        $session->save();

        $sopRespondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sopRespondent->setUserId($user_id);
        $sopRespondent->setAppId(27);
        $this->em->persist($sopRespondent);
        $this->em->flush();

        $params = array (
            'agreement_status' => 'AGREED',
            'time' => time(),
            'app_mid' => $sopRespondent->getAppMid(),
        );
        $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, '1436424899-bd6982201fb7ea024d0926aa1b40d541badf9b4a');

        $url = $this->container->get('router')->generate('_cint_project_survey_agreement_complete', $params);
        $crawler = $this->client->request('GET', $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // check DB
        $history = $this->em->getRepository('WenwenAppBundle:CintUserAgreementParticipationHistory')->findOneByUserId($user_id);
        $this->assertNotEmpty($history);
        $this->assertEquals(10, $history->getAgreementStatus());

        $task = $this->em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(10, $task->getPoint());
        $this->assertEquals(ProjectSurveyCintController::COMMENT, $task->getTaskName());

        $point = $this->em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(10, $point->getPointChangeNum());
        $this->assertEquals(CategoryType::CINT_EXPENSE, $point->getReason());

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($user_id);
        $this->assertEquals($point_1 + 10, $user->getPoints());
    }
}