<?php
namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoComponentData;

class TaobaoControllerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() 
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadTaobaoComponentData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->purge();
        $executor->execute($loader->getFixtures());
        $this->has_fixture = true;

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() 
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_504
     */
    public function testIndexAction() 
    {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_index', array (), false);
        $this->assertEquals('/taobao/index', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_504
     */
    public function testSearchBoxAction() 
    {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_searchbox', array (), false);
        $this->assertEquals('/taobao/searchBox', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    /**
     * @group issue_504
     */
    public function testCategoryApiAction() 
    {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        $url = $container->get('router')->generate('jili_frontend_taobao_categoryapi');
        $this->assertEquals('/taobao/categoryApi', $url);
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(1, $response['current_id']);
        $this->assertEquals(5, count($response['keywords']));
        $this->assertEquals(2, $response['page']);
    }

    /**
     * @group issue_504
     */
    public function testItemAction() 
    {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_item', array (), false);
        $this->assertEquals('/taobao/item', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_504
     */
    public function testShopAction() 
    {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_shop', array (), false);
        $this->assertEquals('/taobao/shop', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_553
     */
    public function testAdsRedirect()
    {
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_index', array ('l'=> 1), false);
        $this->assertEquals('/taobao/index?l=1', $url);
        $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $session = $client->getRequest()->getSession();

        // referer url 
        $this->assertTrue($session->has('goToUrl'));
        $this->assertEquals('/taobao/index?l=1' , $session->get('goToUrl'));
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals( '/user/login', $client->getRequest()->getRequestUri());

        // wrong query parameter wont redirect
        $client->restart();// = static :: createClient(); // reboot avoid 302
        $session->clear();
        $url = $container->get('router')->generate('jili_frontend_taobao_index', array ('m'=> 2), false);
        $this->assertEquals('/taobao/index?m=2', $url);
        $client->request('GET', $url, array(), array());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertFalse($session->has('goToUrl'));
        $this->assertEquals( '/taobao/index?m=2', $client->getRequest()->getRequestUri());

        // l=2
        $client->getRequest()->getSession()->clear();
        $url = $container->get('router')->generate('jili_frontend_taobao_index', array ('l'=> 2), false);
        $this->assertEquals('/taobao/index?l=2', $url);
        $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $session = $client->getRequest()->getSession();
        $this->assertTrue($session->has('goToUrl'));
        $this->assertEquals('/taobao/index?l=2' , $session->get('goToUrl'));

        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals( '/user/login', $client->getRequest()->getRequestUri());

        $client->restart();// = static :: createClient(); // reboot avoid 301
        $url = $container->get('router')->generate('jili_frontend_taobao_index', array(), false);
        $this->assertEquals('/taobao/index', $url);
        $client->request('GET', $url, array(), array(), array() );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

}
