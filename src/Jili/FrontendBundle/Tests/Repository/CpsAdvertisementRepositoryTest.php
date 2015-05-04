<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\Advertiserment\LoadCpsAdvertisementData;

class CpsAdvertisementRepositoryTest extends KernelTestCase {

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
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $fixture = new LoadCpsAdvertisementData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
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
     * @group issue_680 
     */
    public function testFetchCategoryList() 
    {
        $em = $this->em;
        $wcats = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->fetchCategoryList();

        $this->assertNotEmpty($wcats);
        $this->assertCount(29 ,$wcats);

        $this->assertArrayHasKey('综合商城' ,$wcats);

        $this->assertEquals(59 ,$wcats['综合商城']);

        $fixture= array(
            '综合商城' => 59,
            '服装服饰' => 49,
            '手机/数码/家电' => 39,
            '食品/茶叶/酒水' => 38,
            '箱包/眼镜/鞋类' => 36,
            '票务旅游' => 34,
            '医药健康' => 28,
            '珠宝首饰' => 26,
            '团购' => 26,
            '美容化妆' => 24,
            '网络服务/其他' => 19,
            '母婴/儿童用品' => 18,
            '美容化妆 家居家饰' => 17,
            '金融理财' => 16,
            '图书音像' => 13,
            '教育培训' => 10,
            '鲜花礼品' => 10,
            '家居家饰' => 9,
            '奢侈品' => 9,
            '机票酒店旅游' => 8,
            '成人保健' => 7,
            '运动户外' => 6,
            '电视购物' => 6,
            '女性/内衣' => 4,
            '汽车用品' => 3,
            '保险' => 2,
            '名品特卖' => 2,
            '票务' => 2,
            '娱乐交友' => 1
        );

        $this->assertJsonStringEqualsJsonString(
            json_encode($fixture), json_encode($wcats)
        );
        
    }
}
