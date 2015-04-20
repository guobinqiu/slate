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
     * @group debug 
     */
    public function testFetchCategoryList() 
    {
        $em = $this->em;
        $wcats = $em->getRepository('JiliFrontendBundle:CpsAdvertisement')
            ->fetchCategoryList();

        $this->assertNotEmpty($wcats);
        $this->assertCount(38 ,$wcats);

        $this->assertHasKey('综合商城' ,$wcats);

        $this->assertEquals(59 ,$wcats['综合商城']);

        $fixture= array(
            '综合商城'                   =>  59,
            '服装服饰'                   =>  49,
            '手机/数码/家电'             =>  39,
            '食品/茶叶/酒水'             =>  38,
            '票务旅游'                   =>  33,
            '箱包/眼镜/鞋类'             =>  33,
            '医药健康'                   =>  27,
            '团购'                       =>  26,
            '珠宝首饰'                   =>  26,
            '美容化妆'                   =>  24,
            '网络服务/其他'              =>  19,
            '美容化妆 家居家饰'          =>  17,
            '母婴/儿童用品'              =>  17,
            '金融理财'                   =>  14,
            '图书音像'                   =>  13,
            '教育培训'                   =>  11,
            '鲜花礼品'                   =>  11,
            '家居家饰'                   =>  10,
            '奢侈品'                     =>   9,
            '成人保健'                   =>   7,
            '运动户外'                   =>   6,
            '电视购物'                   =>   6,
            '机票酒店旅游'               =>   6,
            '服装/时尚'                  =>   6,
            '商务/商店'                  =>   4,
            '女性/内衣'                  =>   4,
            '旅游/旅行/酒店'             =>   4,
            '汽车用品'                   =>   3,
            '其他'                       =>   2,
            '保险'                       =>   2,
            '名品特卖'                   =>   2,
            '健身美容'                   =>   2,
            '票务'                       =>   2,
            '食品/饮料'                  =>   2,
            '礼品/鲜花'                  =>   1,
            '电脑/互联网/IT/软件'        =>   1,
            '娱乐交友'                   =>   1,
            '电子/家电'                  =>   1
        );
        $this->assertJsonStringEqualsJsonString(
            json_encode($fixture), json_encode($wcats)
        );
        
    }
}
