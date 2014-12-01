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
    public function setUp() {
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

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_504
     */
    public function testIndexAction() {
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
    public function testSearchBoxAction() {
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
    public function testCategoryApiAction() {
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
    public function testItemAction() {
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
    public function testShopAction() {
        $em = $this->em;
        $client = static :: createClient();
        $container = $client->getContainer();

        // request the url
        $url = $container->get('router')->generate('jili_frontend_taobao_shop', array (), false);
        $this->assertEquals('/taobao/shop', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
