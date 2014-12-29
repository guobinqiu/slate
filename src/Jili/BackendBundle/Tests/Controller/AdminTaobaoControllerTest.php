<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\FileSystem\FileSystem;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoComponentData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoCategoryData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoSelfPromotionProductData;

use Jili\BackendBundle\Controller\AdminTaobaoController;

class AdminTaobaoControllerTest extends WebTestCase {

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
        $executor->purge();

        $tn =$this->getName();


        if (in_array($tn, array('testComponentAction','testGetConditions','testKeywordsAction','testSaveComponentAction','testSaveComponentFinishAction','testDeleteComponentAction','testSortComponentAction') ) ) {
            $fixture = new LoadTaobaoComponentData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        } else if(in_array($tn, array('testAddPromotionSelfProductActionNormal','testAddPromotionSelfProductActionNoPic', 'testRemovePromotionSelfProductAction','testListPromotionSelfProductAction','testUpdatePromotionSelfProductAction','testUpdatePromotionSelfProductActionWithPic','testRemovePromotionSelfProductActionWithPic') )  ) {

            $fixture = new LoadTaobaoCategoryData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);

            if (in_array( $tn, array('testRemovePromotionSelfProductAction','testListPromotionSelfProductAction','testUpdatePromotionSelfProductAction','testUpdatePromotionSelfProductActionWithPic','testRemovePromotionSelfProductActionWithPic'))) {
                $fixture1 = new LoadTaobaoSelfPromotionProductData();
                $fixture1->setContainer($container);
                $loader->addFixture($fixture1);
            }

            $executor->execute($loader->getFixtures());

        }

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
        $categoriesId = 1;
        $keywordId = 1;
        $param = $controller->getConditions($componentId, $categoriesId, $keywordId);
        $this->assertNull($param['categoryId']);
        $this->assertNull($param['keywordId']);

        $componentId = 2;
        $categoriesId = 1;
        $keywordId = -1;
        $param = $controller->getConditions($componentId, $categoriesId, $keywordId);
        $this->assertEquals(1, $param['categoryId']);
        $this->assertNull($param['keywordId']);

        $componentId = 2;
        $categoriesId = 1;
        $keywordId = 3;
        $param = $controller->getConditions($componentId, $categoriesId, $keywordId);
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

    /**
     * @group issue_594 
     */
    public function testAddPromotionSelfProductActionNormal()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $root_dir = $container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';

        $url = $container->get('router')->generate('jili_backend_admintaobao_newpromotionselfproduct');
        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/new' ,$url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form =$crawler->selectButton('提交')->form();

        $picture_dir = $container->getParameter('taobao_self_promotion_picture_dir') ;
        $fs = new FileSystem();
        $fs->remove($picture_dir);

        $categories = LoadTaobaoCategoryData::$SELF_PROMOTION_CATEGORIES;
        $params =  array(
        //    'taobaoCategoryId'=> $categories[0]->getId(),
            'title'=>'【天猫】加厚打底裤',
            'price'=>25.00,
            'pricePromotion'=>8.80,
            'clickUrl'=> 'http://s.click.taobao.com/t?e=m%3D2%26s%3DsxbDBv3ziGMcQipKwQzePOeEDrYVVa64pRe%2F8jaAHci5VBFTL4hn2d%2BozAHeYRlk%2BvZA5LFGqMTE%2Ff4qt46kcundZYnGkACiyiq2TwADYwb5sG2hsz8gkAB%2BiJXgCUQAFNPFWMMKbzlMCoznhDWYTsYOae24fhW0'
        );

        $form->setValues(array(
            'taobao_promotion_self_link_product[taobaoCategory]'=> $categories[0]->getId(),
            'taobao_promotion_self_link_product[title]'=> $params['title'],
            'taobao_promotion_self_link_product[price]'=>$params['price'],
            'taobao_promotion_self_link_product[pricePromotion]'=> $params['pricePromotion'],
            //'taobao_promotion_self_link_product[itemUrl]'=>,
            'taobao_promotion_self_link_product[clickUrl]'=>$params['clickUrl'],
            'taobao_promotion_self_link_product[picture]'=>$fixture_dir.DIRECTORY_SEPARATOR.'taobao/pro01_01.jpg',
            //'taobao_promotion_self_link_product[commentDescription]'=>,
            'taobao_promotion_self_link_product[promotionRate]'=>10,

        ));

        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();


        // check the db
        $expected  = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->findOneBy($params); 
        $this->assertNotNull($expected);
        $this->assertInstanceOf( '\\Jili\\FrontendBundle\\Entity\\TaobaoSelfPromotionProducts', $expected);
$this->assertEquals($categories[0]->getId(), $expected->getTaobaoCategory()->getId());
        $target = $picture_dir.$expected->getPictureName();
        // check the image file
        $this->assertFileExists($target);
        @unlink($target);
    }

    /**
     * without picture
     * @group issue_594 
     */
    public function testAddPromotionSelfProductActionNoPic()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em=$this->em;
        $root_dir = $container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';

        $url = $container->get('router')->generate('jili_backend_admintaobao_newpromotionselfproduct');

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form =$crawler->selectButton('提交')->form();
         
        $params =  array(
            'title'=>'【天猫】加厚打底裤',
            'price'=>25.00,
            'pricePromotion'=>8.80,
            'clickUrl'=> 'http://s.click.taobao.com/t?e=m%3D2%26s%3DsxbDBv3ziGMcQipKwQzePOeEDrYVVa64pRe%2F8jaAHci5VBFTL4hn2d%2BozAHeYRlk%2BvZA5LFGqMTE%2Ff4qt46kcundZYnGkACiyiq2TwADYwb5sG2hsz8gkAB%2BiJXgCUQAFNPFWMMKbzlMCoznhDWYTsYOae24fhW0'
        );

        $form->setValues(array(
            'taobao_promotion_self_link_product[taobaoCategory]'=>LoadTaobaoCategoryData::$SELF_PROMOTION_CATEGORIES[0]->getId(),
            'taobao_promotion_self_link_product[title]'=> $params['title'],
            'taobao_promotion_self_link_product[price]'=>$params['price'],
            'taobao_promotion_self_link_product[pricePromotion]'=> $params['pricePromotion'],
            'taobao_promotion_self_link_product[clickUrl]'=>$params['clickUrl'],
            'taobao_promotion_self_link_product[promotionRate]'=>10,

        ));

        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $expected  = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->findOneBy($params); 
        $this->assertNotNull($expected);
        $this->assertInstanceOf( '\\Jili\\FrontendBundle\\Entity\\TaobaoSelfPromotionProducts', $expected);
        $this->assertEquals(LoadTaobaoCategoryData::$SELF_PROMOTION_CATEGORIES[0]->getId(), $expected->getTaobaoCategory()->getId());

    }

    /**
     * @group issue_594 
     */
    public function testListPromotionSelfProductAction()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('jili_backend_admintaobao_listpromotionselfproduct');
        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/list' ,$url);

        //prepare data
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // data ...
        
    }

    /**
     * @group issue_594 
     */
    public function testUpdatePromotionSelfProductAction()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $product = LoadTaobaoSelfPromotionProductData::$PRODUCTS[0];
        $url = $container->get('router')->generate('jili_backend_admintaobao_editpromotionselfproduct', array('id'=> $product->getId() ));

        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/edit/'.$product->getId() ,$url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form =$crawler->selectButton('提交')->form(array(
            'taobao_promotion_self_link_product[price]'=>  $product->getPrice() * 2,
        ), 'PUT');

        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // prepare a product data 
       $after = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($product->getId());
        $this->assertEquals($product->getPrice() * 2 , $after->getPrice());

    }
    /**
     * edit with picture
     * @group issue_594 
     */
    public function testUpdatePromotionSelfProductActionWithPic()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $root_dir = $container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir . DIRECTORY_SEPARATOR . 'fixtures';

        $product = LoadTaobaoSelfPromotionProductData::$PRODUCTS[3];

        $url = $container->get('router')->generate('jili_backend_admintaobao_editpromotionselfproduct', array('id'=> $product->getId() ));

        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/edit/'.$product->getId() ,$url);

        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form =$crawler->selectButton('提交')->form(array(
            'taobao_promotion_self_link_product[picture]'=>$fixture_dir.DIRECTORY_SEPARATOR.'taobao/pro01_01.jpg',
        ), 'PUT');

        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        // check the image uploaded
       $after = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($product->getId());
        $this->assertEmpty($product->getPictureName());
        $this->assertNotEmpty($after->getPictureName());

        $picture_dir = $container->getParameter('taobao_self_promotion_picture_dir') ;
        $target = $picture_dir.$after->getPictureName();
        $this->assertFileExists( $target);

    }

    /**
     * @group issue_594 
     */
    public function testRemovePromotionSelfProductAction()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em  = $this->em;

        $product = LoadTaobaoSelfPromotionProductData::$PRODUCTS[2];

        $url = $container->get('router')->generate('jili_backend_admintaobao_editpromotionselfproduct', array('id'=> $product->getId() ));

        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/edit/'.$product->getId() ,$url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form =$crawler->selectButton('删除')->form(array(), 'DELETE');
        // prepare a product data
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
       $after = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($product->getId());
        $this->assertNull($after);
    }

    /**
     * With image
     * @group issue_594 
     */
    public function testRemovePromotionSelfProductActionWithPic()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $em  = $this->em;

        $product = LoadTaobaoSelfPromotionProductData::$PRODUCTS[0];

        $url = $container->get('router')->generate('jili_backend_admintaobao_editpromotionselfproduct', array('id'=> $product->getId() ));

        $this->assertEquals('https://localhost/backend/admin/taobao/promotion-self-product/edit/'.$product->getId() ,$url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form =$crawler->selectButton('删除')->form(array(), 'DELETE');
        // prepare a product data
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
       $after = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($product->getId());
        $this->assertNull($after);
        // image should not exists anymore
       $picture_name =  $product->getPictureName();
        $this->assertNotEmpty($picture_name);
        $picture_dir = $container->getParameter('taobao_self_promotion_picture_dir') ;
        $target = $picture_dir.$picture_name;
        $this->assertFileNotExists($target);

    }
}
