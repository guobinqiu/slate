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
        $this->em->close();
    }

    /**
     * @group issue_523
     */
    public function testComponentAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $logger = $container->get('logger');

        $url = 'backend/admin/taobao/component';
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
    public function testKeywordsAction() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_admin_taobao_keywords', array (
            'categoryId' => 1
        ), true);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $array = json_decode($content, true);
        $this->assertEquals(23, count($array));
        $this->assertEquals(2, $array[0]['id']);
        $this->assertEquals('韩版女', $array[0]['keyword']);
    }

    /**
     * @group issue_523
     */
    public function testSaveComponentAction() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminTaobaoController();
        $controller->setContainer($container);
        $url = $container->get('router')->generate('_admin_taobao_saveComponent', array (), true);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_523
     */
    public function testSaveComponentFinishAction() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new AdminTaobaoController();
        $controller->setContainer($container);

        // insert
        $post_data['componentId'] = 1;
        $post_data['categoryId'] = -1;
        $post_data['keyword'] = '';
        $post_data['content'] = '淘宝Test';

        $url = $container->get('router')->generate('_admin_taobao_saveComponentFinish', array (), true);
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneByContent($post_data['content']);
        $this->assertNotNull($taobaoComponent);
        $this->assertEquals($post_data['content'], $taobaoComponent->getContent());

        // update
        $this->em->clear();
        $post_data = array ();
        $post_data['componentId'] = 1;
        $post_data['categoryId'] = -1;
        $post_data['keyword'] = '';
        $post_data['content'] = 'Test淘宝Test';
        $post_data['id'] = $taobaoComponent->getId();

        $url = $container->get('router')->generate('_admin_taobao_saveComponentFinish', array (), true);
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneByContent($post_data['content']);
        $this->assertNotNull($taobaoComponent);
        $this->assertEquals($post_data['content'], $taobaoComponent->getContent());
    }

    /**
     * @group issue_523
     */
    public function testDeleteComponentAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById(1);
        $this->assertNotNull($taobaoComponent);

        $session = $container->get('session');
        $session->set('admin_taobao_condition', array ());
        $session->save();

        $url = $container->get('router')->generate('_admin_taobao_deleteComponent', array (
            'id' => 1
        ), true);
        $crawler = $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById(1);
        $this->assertNull($taobaoComponent);
    }

    /**
     * @group issue_523
     */
    public function testSortComponentAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $post_data['sort_1'] = 2;
        $post_data['sort_2'] = 1;

        $session = $container->get('session');
        $session->set('admin_taobao_condition', array ());
        $session->save();

        $url = $container->get('router')->generate('_admin_taobao_sortComponent', array (), true);
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById(1);
        $this->assertEquals(2, $taobaoComponent->getSort());
        $taobaoComponent = $this->em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById(2);
        $this->assertEquals(1, $taobaoComponent->getSort());
    }

}