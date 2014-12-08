<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoComponentData;

class TaobaoComponentRepositoryTest extends KernelTestCase {

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
     * @group issue_523
     */
    public function testFindComponents() {
        $em = $this->em;
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponents(1, 2);
        $this->assertEquals(5, count($components));
        $this->assertEquals('韩版女', $components[0]['keyword']);
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponents(1, 2, 1, 10);
        $this->assertEquals(10, count($components));
    }

    /**
     * @group issue_523
     */
    public function testFindComponentsByCondition() {
        $em = $this->em;

        $param = array ();
        $param['componentId'] = 2;
        $param['categoryId'] = 1;
        $param['keywordId'] = 2;
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponentsByCondition($param);
        $this->assertEquals(1, count($components));
        $this->assertEquals('韩版女', $components[0]['keyword']);

        $param = array ();
        $param['componentId'] = 2;
        $param['categoryId'] = 1;
        $param['keywordId'] = null;
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponentsByCondition($param);
        $this->assertEquals(23, count($components));
        $this->assertEquals('羽绒新品', $components[0]['keyword']);

        $param = array ();
        $param['componentId'] = 1;
        $param['categoryId'] = null;
        $param['keywordId'] = null;
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponentsByCondition($param);
        $this->assertEquals(1, count($components));
        $this->assertEquals('<a data-type="6" data-tmpl="573x66" data-tmplid="140" data-style="2" data-border="0" biz-s_logo="1" biz-s_hot="1" href="#"></a>', $components[0]['content']);
    }

    /**
     * @group issue_523
     */
    public function testFindKeywordByCategoryId() {
        $em = $this->em;
        $categoryId = 1;
        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findKeywordByCategoryId($categoryId);
        $this->assertEquals(23, count($components));
        $this->assertEquals(2, $components[0]['id']);
        $this->assertEquals('韩版女', $components[0]['keyword']);
    }
}