<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Wenwen\AppBundle\Entity\SsiRespondent;
use Wenwen\FrontendBundle\Controller\SsiPartnerController;

class SsiPartnerControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

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
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testPermissionYes()
    {
        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user_id = $users[0]->getId();

        $url = $container->get('router')->generate('_ssi_partner_permission');

        $crawler = $client->request('GET', $url);

        //not login, will follow redirect to _ssi_partner_error
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/ssi_partner/error'));
        $crawler = $client->followRedirect();
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        //login
        $this->login($client);
        $this->assertEquals(1, $client->getContainer()->get('session')->get('uid'));

        //status: login, will show permission page
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('input[name="SsiPartnerPermission[token]"]')->count() > 0);

        //permission: yes , redirect commit
        $form = $crawler->filter('form[id=ssi_permission_form]')->form();
        $form['SsiPartnerPermission[permission_flag]'] = '1';
        $crawler = $client->submit($form);
        $this->assertEquals('http://localhost/ssi_partner/commit', $client->getRequest()->getUri());
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect redirectAction
        $crawler = $client->followRedirect();
        $this->assertEquals('http://localhost/ssi_partner/redirect', $client->getRequest()->getUri());
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect survey site
        $crawler = $client->followRedirect();
        $this->assertEquals('http://tracking.surveycheck.com/aff_c?aff_id=1346&aff_sub5=wwcn-' . $user_id . '&offer_id=2189', $client->getRequest()->getUri());

        //check db
        $em->clear();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        $this->assertNotEmpty($ssi_respondent);
        $this->assertEquals(1, $ssi_respondent->getStatusFlag());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testPermissionNo()
    {
        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user_id = $users[0]->getId();

        //login
        $this->login($client);

        //status: login, will show permission page
        $url = $container->get('router')->generate('_ssi_partner_permission');
        $crawler = $client->request('GET', $url);

        //permission: no , redirect commit
        $form = $crawler->filter('form[id=ssi_permission_form]')->form();
        $form['SsiPartnerPermission[permission_flag]'] = '0';
        $crawler = $client->submit($form);
        $this->assertEquals('http://localhost/ssi_partner/commit', $client->getRequest()->getUri());
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect completeAction
        $crawler = $client->followRedirect();
        $this->assertEquals('http://localhost/ssi_partner/complete', $client->getRequest()->getUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check db
        $em->clear();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        $this->assertNotEmpty($ssi_respondent);
        $this->assertEquals(0, $ssi_respondent->getStatusFlag());

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals($users[0]->getPoints() + 1, $user->getPoints());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testPrescreenAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user_id = $users[0]->getId();
        $this->login($client);

        $url = $container->get('router')->generate('_ssi_partner_prescreen');

        //資格なし1: has no SsiRespondent
        $crawler = $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals('http://localhost/ssi_partner/error', $client->getRequest()->getUri());

        //資格なし2: has  SsiRespondent , but status flag != 1
        $em->clear();
        $ssi_respondent = new SsiRespondent();
        $ssi_respondent->setUserId($user_id);
        $ssi_respondent->setStatusFlag(0);
        $em->persist($ssi_respondent);
        $em->flush();
        $em->clear();
        $crawler = $client->request('GET', $url);
        $crawler = $client->followRedirect();
        $this->assertEquals('http://localhost/ssi_partner/error', $client->getRequest()->getUri());

        //資格がある: has  SsiRespondent ,  status flag = 1
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        $ssi_respondent->setStatusFlag(1);
        $em->persist($ssi_respondent);
        $em->flush();
        $em->clear();
        $crawler = $client->request('GET', $url);
        $this->assertEquals('http://localhost/ssi_partner/prescreen', $client->getRequest()->getUri());

        //submit redirect survey site
        $form = $crawler->filter('form[id=ssi_redirect_form]')->form();
        $crawler = $client->submit($form);
        $this->assertEquals('http://localhost/ssi_partner/redirect', $client->getRequest()->getUri());

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect survey site
        $crawler = $client->followRedirect();
        $this->assertEquals('http://tracking.surveycheck.com/aff_c?aff_id=1346&aff_sub5=wwcn-' . $user_id . '&offer_id=2189', $client->getRequest()->getUri());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testPrescreeningCompleteAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user_id = $users[0]->getId();
        $this->login($client);

        $url = $container->get('router')->generate('_ssi_partner_prescreeningcomplete');

        //set SsiRespondent ,  status flag = 1
        $ssi_respondent = new SsiRespondent();
        $ssi_respondent->setUserId($user_id);
        $ssi_respondent->setStatusFlag(1);
        $em->persist($ssi_respondent);
        $em->flush();
        $em->clear();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check db
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        $this->assertNotEmpty($ssi_respondent);
        $this->assertEquals(10, $ssi_respondent->getStatusFlag());

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals($users[0]->getPoints() + 1, $user->getPoints());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testErrorActionAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $url = $container->get('router')->generate('_ssi_partner_error');
        $crawler = $client->request('GET', $url);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     *
     */
    public function testGivePoint()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $users = $em->getRepository('JiliApiBundle:User')->findAll();
        $user_id = $users[0]->getId();

        $controller = new SsiPartnerController();
        $controller->givePoint($container->get('points_manager'), $user_id, 1, '申请参与SSI市场调查项目');

        //check db
        $em->clear();
        $task = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(1, $task->getPoint());
        $this->assertEquals('申请参与SSI市场调查项目', $task->getTaskName());

        $point = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(1, $point->getPointChangeNum());
        $this->assertEquals(93, $point->getReason());

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEquals($users[0]->getPoints() + 1, $user->getPoints());
    }

    private function login($client)
    {
        $session = $client->getContainer()->get('session');
        $users = $this->em->getRepository('JiliApiBundle:User')->findAll();
        $session->set('uid', $users[0]->getId());
        $session->save();
    }
}