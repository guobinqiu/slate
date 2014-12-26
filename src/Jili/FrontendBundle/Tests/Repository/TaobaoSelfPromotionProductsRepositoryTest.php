<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\Entity\TaobaoCategory;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoCategoryData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoSelfPromotionProductData;

class TaobaoSelfPromotionProductsRepositoryTest extends KernelTestCase 
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
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container  = static :: $kernel->getContainer();
        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadTaobaoCategoryData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $tn = $this->getName();
        if(in_array( $tn ,array('testFetchByRange','testRemove'))) {
            $fixture1 = new LoadTaobaoSelfPromotionProductData();
            $fixture1->setContainer($container);
            $loader->addFixture($fixture1);
        }

        $executor->purge();
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_594
     */
    public function  testInsert() 
    {

        $em = $this->em;
        $params = array(
            'title'=>'【天猫】电脑屏幕防辐射 贴膜'  ,
            'price'=> 22.00,
            'pricePromotion'=> 7.90,
            'clickUrl'=>'http://s.click.taobao.com/t?e=m%3D2%26s%3Dw0GvCHM7Pr8cQipKwQzePOeEDrYVVa64pRe%2F8jaAHci5VBFTL4hn2dPNz8lX%2Bm09DOz%2BQ0BmwbzE%2Ff4qt46kcundZYnGkACiyiq2TwADYwZJ3n63Gp0ZhdzgqBjt9TgiXkknyJYcnZp4A7P1X76t%2ByGFCzYOOqAQ',
        );

        $entity = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->insert( $params );

        $expected =$em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->findBy($params);

        $this->assertEquals(1, count($expected));

        // with pics
        //
    }


    /**
     * @group issue_594
     */
    public function testFetchByRange()
    {
        $em = $this->em;
        $actual = $this->em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->fetchByRange(1,10);
        $this->assertEquals(100, $actual['total']);
        //
    }


    /**
     * @group issue_594
     */
    public function testRemove()
    {
        $em = $this->em;
        $picture_dir = $this->container->getParameter('taobao_self_promotion_picture_dir') ;

        // with image
        $id = LoadTaobaoSelfPromotionProductData::$PRODUCTS[0]->getId();
        $target = $picture_dir.LoadTaobaoSelfPromotionProductData::$PRODUCTS[0]->getPictureName();
        $this->assertFileExists($target);
        $before = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($id);
        $this->assertNotNull($before);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\TaobaoSelfPromotionProducts',$before);
        $actual = $this->em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->remove($id,$picture_dir);
        $this->assertNull($em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($id));
        $this->assertFileNotExists($target);

        // without image
        $id = LoadTaobaoSelfPromotionProductData::$PRODUCTS[1]->getId();
        $before = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($id);
        $this->assertNotNull($before);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\TaobaoSelfPromotionProducts',$before);
        $actual = $this->em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->remove($id,$picture_dir);
        $this->assertNull($em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->findOneById($id));
    }


}
