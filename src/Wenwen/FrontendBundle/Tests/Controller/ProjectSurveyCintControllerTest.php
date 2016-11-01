<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Controller\ProjectSurveyCintController;
use Wenwen\FrontendBundle\Entity\CategoryType;


class ProjectSurveyCintControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new LoadUserData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;

        @session_start();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @group dev-merge-ui-survey-list-cint
     *
     */
    public function testInformationAction()
    {
        $client = static::createClient(array(),array('HTTPS' => true));
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_cint_project_survey_information');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //login 后
        $session = $container->get('session');
        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();


        $cint_research = array();
        $cint_research['title'] = 'dummy title';
        $cint_research['difficulty'] = 'normal';
        $cint_research['loi'] = 10;
        $cint_research['extra_info']['point']['complete'] = 400;
        $cint_research['url'] = 'dummy url';
        $cint_research['survey_id'] = 1;
        $url = $container->get('router')->generate('_cint_project_survey_information', array('cint_research' => $cint_research, 'difficulty' => '普通'));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-merge-ui-survey-list-cint
     */
    public function testEndlinkAction()
    {
        $client = static::createClient(array(),array('HTTPS' => true));
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_cint_project_survey_endlink', array (
            'survey_id' => 4,
            'answer_status' => 'test'
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //login 后
        $session = $container->get('session');
        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $url = $container->get('router')->generate('_cint_project_survey_endlink', array (
            'survey_id' => 4,
            'answer_status' => 'complete'
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-merge-ui-survey-list-cint
     */
    public function testAgreementCompleteAction()
    {
        $client = static::createClient(array(),array('HTTPS' => true));
        $container = $client->getContainer();
        $em = $this->em;

        $session = $container->get('session');
        $users = $em->getRepository('WenwenFrontendBundle:User')->findAll();
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

            $url = $container->get('router')->generate('_cint_project_survey_agreement_complete', $invalid_params);
            $crawler = $client->request('GET', $url);
            //$this->assertEquals(404, $client->getResponse()->getStatusCode());
        }

        {
            //cint agreement end page
            $params = array (
                'agreement_status' => 'AGREED',
                'time' => time()
            );
            $sop_config = $container->getParameter('sop');
            $params['sig'] = \SOPx\Auth\V1_1\Util::createSignature($params, $sop_config['auth']['app_secret']);

            $url = $container->get('router')->generate('_cint_project_survey_agreement_complete', $params);
            $crawler = $client->request('GET', $url);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            $em->clear();

            // check DB
            $history = $em->getRepository('WenwenAppBundle:CintUserAgreementParticipationHistory')->findOneByUserId($users[0]->getId());
            $this->assertNotEmpty($history);
            $this->assertEquals(10, $history->getAgreementStatus());

            $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findOneByUserId($user_id);
            $this->assertEquals(1, $task->getPoint());
            $this->assertEquals(ProjectSurveyCintController::COMMENT, $task->getTaskName());

            $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
            $this->assertEquals(1, $point->getPointChangeNum());
            $this->assertEquals(CategoryType::CINT_EXPENSE, $point->getReason());

            $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);
            $this->assertEquals($point_1 + 1, $user->getPoints());
        }
    }
}