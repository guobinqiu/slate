<?php
namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class HomeControllerTest extends WebTestCase
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
        $this->em = $em;
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
     * @group issue_505
     */
    public function testIndexActionWithoutSpm()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $router = $container->get('router');
        $logger = $container->get('logger');

        $spm = 'baidu_partnerb';

        $url = $router->generate('_homepage');

        $this->assertEquals('/', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit landing page with spm ');

        $this->assertEmpty($container->get('session')->get('source_route'));
    }

    /**
     * @group issue_505
     */
    public function testIndexActionWithSpm()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $router = $container->get('router');
        $logger = $container->get('logger');

        $spm = 'baidu_partnerb';

        $url = $router->generate('_homepage');
        $url = $container->get('router')->generate('_homepage', array (
            'spm' => $spm
        ), false);
        $this->assertEquals('/?spm=baidu_partnerb', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit landing page with spm ');

        $session = $container->get('session');
        $this->assertEquals($spm, $session->get('source_route'), 'source_route checking');
    }

    /**
     * @group dev-merge-ui-home_page_url
     */
    public function testLoginIndexAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('jili_frontend_home_index');
        $crawler = $client->request('GET', $url);

        //not login, will follow redirect to login
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/user\/login$/', $client->getResponse()->headers->get('location'), 'need login');

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        //load data
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        $user_id = LoadUserData::$USERS[0]->getId();

        //login
        $session = $client->getRequest()->getSession();
        $session->set('uid', $user_id);
        $session->save();

        //after login, will show home page
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
