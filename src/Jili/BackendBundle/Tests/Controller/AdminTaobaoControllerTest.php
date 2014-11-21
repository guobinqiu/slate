<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoComponentData;
use Jili\BackendBundle\Controller\AdminTaobaoController;

class AdminTaobaoControllerTest extends WebTestCase {

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
        //$this->em->close();
    }

    /**
     * @group issue_523
     */
    public function testComponentAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $logger = $container->get('logger');

        $url = 'backend/admin/taobao/component';
        $client = static :: createClient();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('#componentId'));
        $this->assertEquals(' 搜索框分类产品单品店铺', $crawler->filter('#componentId')->text());

        $this->assertCount(1, $crawler->filter('#categoryId'));
        $this->assertEquals(' 精选女装精选男装鞋靴箱包运动户外珠宝配饰手机数码家电办公护肤彩妆母婴用品家居家纺家装设计汇吃美食百货日用汽车摩托花鸟文娱生活服务更多服务', $crawler->filter('#categoryId')->text());

        $this->assertCount(1, $crawler->filter('#keywordId'));
        $this->assertEquals('', $crawler->filter('#keywordId')->text());

        $form = $crawler->selectButton('搜索')->form();
        $form['componentId'] = 2;
        $form['categoryId'] = 1;
        // submit that form
        $crawler = $client->submit($form);
        $this->assertEquals('', $crawler->filter('#keywordId')->text());

        //$logger->info('mmzhang00'.$crawler->filter('#categoryId')->text()."++++++++");

    }

    /**
     * @group issue_523
     */
    public function testGetConditions() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminTaobaoController();
        $controller->setContainer($container);

        $componentId = 1;
        $categoryId = 1;
        $keywordId = 1;
        $param = $controller->getConditions($componentId, $categoryId, $keywordId);
        $this->assertNull($param['categoryId']);
        $this->assertNull($param['keywordId']);

        $componentId = 2;
        $categoryId = 1;
        $keywordId = -1;
        $param = $controller->getConditions($componentId, $categoryId, $keywordId);
        $this->assertEquals(1, $param['categoryId']);
        $this->assertNull($param['keywordId']);

        $componentId = 2;
        $categoryId = 1;
        $keywordId = 3;
        $param = $controller->getConditions($componentId, $categoryId, $keywordId);
        $this->assertEquals(1, $param['categoryId']);
        $this->assertEquals(3, $param['keywordId']);
    }

    /**
     * @group issue_523
     */
    public function testGetComponentCategory() {

    }

    /**
     * @group issue_523
     */
    public function testKeywordsAction() {

    }

    /**
     * @group issue_523
     */
    public function testSaveComponentAction() {

    }

    /**
     * @group issue_523
     */
    public function testDeleteComponentAction() {

    }

    /**
     * @group issue_523
     */
    public function testSortComponentAction() {

    }

}