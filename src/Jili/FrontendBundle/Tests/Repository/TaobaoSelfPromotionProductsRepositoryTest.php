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
        if(in_array( $tn ,array('testFetchByRange','testRemove','testFetchWithCategory'))) {
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
     **/
    public function testFetchWithCategory()
    {

        $em = $this->em;
        $actual = $this->em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->fetch();

        $this->assertCount(100, $actual);
    }




}
