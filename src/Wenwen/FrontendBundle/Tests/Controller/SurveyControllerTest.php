<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Controller\SurveyController;

class SurveyControllerTest extends WebTestCase
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
     * @group dev-merge-ui-survey-top
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_survey_index');
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
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("问卷列表")')->count() > 0);

        $this->assertCount(1, $crawler->filter('#sop_api_url'));
        $this->assertCount(1, $crawler->filter('#sop_point'));
        $this->assertCount(1, $crawler->filter('#sop_app_id'));
        $this->assertCount(1, $crawler->filter('#sop_app_mid'));
        $this->assertCount(1, $crawler->filter('#sop_sig'));
        $this->assertCount(1, $crawler->filter('#sop_time'));
    }

    /**
     * @group dev-merge-ui-survey-top
     */
    public function testTopAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_survey_top');
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
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#sop_api_url'));
        $this->assertCount(1, $crawler->filter('#sop_point'));
        $this->assertCount(1, $crawler->filter('#sop_app_id'));
        $this->assertCount(1, $crawler->filter('#sop_app_mid'));
        $this->assertCount(1, $crawler->filter('#sop_sig'));
        $this->assertCount(1, $crawler->filter('#sop_time'));
    }

    /**
     * @group dev-merge-ui-survey-top
     */
    public function testGetSopParams()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $controller = new SurveyController();
        $controller->setContainer($container);

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(1);
        $sop_config = $container->getParameter('sop');
        $return = $controller->getSopParams($sop_config, $sop_respondent->getId());
        $this->assertNotEmpty($return['sop_params']['app_id']);
        $this->assertNotEmpty($return['sop_params']['app_mid']);
        $this->assertNotEmpty($return['sop_params']['time']);
        $this->assertNotEmpty($return['sop_api_url']);
        $this->assertNotEmpty($return['sop_point']);
        $this->assertNotEmpty($return['sop_params']['sig']);
    }
}
