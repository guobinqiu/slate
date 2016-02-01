<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class ProjectSurveyControllerTest extends WebTestCase
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
     * @group dev-merge-ui-survey-list
     *
     */
    public function testInformationAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_project_survey_information');
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

        $url = $container->get('router')->generate('_project_survey_information', array (
            'survey_id' => 4
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-merge-ui-survey-list
     */
    public function testEndlinkAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_project_survey_endlink', array (
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

        $url = $container->get('router')->generate('_project_survey_endlink', array (
            'survey_id' => 4,
            'answer_status' => 'complete'
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}