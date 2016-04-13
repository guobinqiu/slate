<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class AdminPanelistControllerTest extends WebTestCase
{
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

        // load fixtures
        $fixture = new AdminPanelistControllerTestFixture();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;

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
     * @group dev-backend_panelist
     */
    public function testSearchAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        //检索默认页面
        $url = 'admin/panelist/search';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('panelsearch', $client->getResponse()->getContent());

        //click serach
        $form = $crawler->selectButton('s')->form();
        $form['panelistSerach[user_id]'] = 31;
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testEditPanelist()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $user = AdminPanelistControllerTestFixture::$USER;

        //沒有id
        $url = 'admin/panelist/edit';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('No such panelist_id', $client->getResponse()->getContent());

        //有id
        $url = $container->get('router')->generate('_admin_panelist_edit', array (
            'id' => $user->getId()
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin/panelist/editConfirm', $client->getResponse()->getContent());

        //提交到确认页面
        $form = $crawler->selectButton('Confirm')->form();
        $form['user[nick]'] = 'I am nick';
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin/panelist/editCommit', $client->getResponse()->getContent());

        //完成编辑
        $form = $crawler->selectButton('Confirm')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Update completed!', $client->getResponse()->getContent());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testPointHistoryAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $user = AdminPanelistControllerTestFixture::$USER;

        //沒有id
        $url = 'admin/panelist/pointHistory';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('No such panelist_id', $client->getResponse()->getContent());

        //有id
        $url = $container->get('router')->generate('_admin_panelist_pointhistory', array (
            'id' => $user->getId()
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminPanelistControllerTestFixture implements ContainerAwareInterface, FixtureInterface
{
    public static $USER;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //load data for testing
        $ad = new \Jili\ApiBundle\Entity\AdCategory();
        $ad->setDisplayName('广告体验');
        $manager->persist($ad);
        $manager->flush();

        $hobby = new \Jili\ApiBundle\Entity\HobbyList();
        $hobby->setHobbyName('上网');
        $manager->persist($hobby);
        $manager->flush();

        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick('test1');
        $user->setEmail('test_1@d8aspring.com');
        $user->setIsEmailConfirmed(1);
        $user->setPwd('123qwe');
        $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_JILI);
        $user->setHobby($hobby->getId());
        $user->setCity(2);
        $manager->persist($user);
        $manager->flush();
        self::$USER = $user;

        $point_history = 'Jili\ApiBundle\Entity\PointHistory0' . (($user->getId()) % 10);
        $po = new $point_history();
        $po->setUserId($user->getId());
        $po->setPointChangeNum(100);
        $po->setReason($ad->getId());
        $manager->persist($po);
        $manager->flush();

        $sop_respondent = new \Jili\ApiBundle\Entity\SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
    }
}
