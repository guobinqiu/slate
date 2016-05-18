<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

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
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_cint_project_survey_information');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //login 后
        $session = $container->get('session');
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();


        $dummy_data = '{
               "survey_id": "10000",
               "quota_id": "20000",
               "cpi": "0.00",
               "ir": "80",
               "loi": "10",
               "is_answered": "0",
               "is_closed": "0",
               "title": "Cint Survey",
               "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
               "date": "2015-01-01",
               "extra_info": {
                 "point": {
                   "complete": "40",
                   "screenout": "10",
                   "quotafull": "10"
                 }
               }
              }';

        $cint_research = json_decode($dummy_data, true);
        $url = $container->get('router')->generate('_cint_project_survey_information', array('cint_research' => $cint_research));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-merge-ui-survey-list-cint
     */
    public function testEndlinkAction()
    {
        $client = static::createClient();
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
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
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
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $session = $container->get('session');
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
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
            $this->assertEquals(404, $client->getResponse()->getStatusCode());
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
            $this->assertEquals('同意Cint问卷', $task->getTaskName());

            $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
            $this->assertEquals(1, $point->getPointChangeNum());
            $this->assertEquals(93, $point->getReason());

            $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
            $this->assertEquals($point_1 + 1, $user->getPoints());
        }
    }
}