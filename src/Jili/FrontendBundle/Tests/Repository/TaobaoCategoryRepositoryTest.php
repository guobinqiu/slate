<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoCategoryData;
use Jili\FrontendBundle\Entity\TaobaoCategory;

class TaobaoCategoryRepositoryTest extends KernelTestCase {

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
        $fixture = new LoadTaobaoCategoryData();
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
     * @group issue_594
     */
    public function testFindCategorys() {
        $em = $this->em;
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys();
        $this->assertEquals(16, count($keywords));

        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys(1);
        $this->assertEquals(1, count($keywords));

        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys(0,TaobaoCategory::COMPONENTS );
        $this->assertEquals(16, count($keywords));

        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys(1,TaobaoCategory::COMPONENTS );
        $this->assertEquals(1, count($keywords));

 
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findCategorys(0,TaobaoCategory::SELF_PROMOTION);
        $this->assertEquals(11, count($keywords));

    }

}
