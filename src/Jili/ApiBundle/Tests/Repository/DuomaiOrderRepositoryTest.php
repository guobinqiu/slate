<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;


class DuomaiOrderRepositoryTest extends KernelTestCase {


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
        $container = static :: $kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $this->container = $container;
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
    public function testInit() 
    {
        $em = $this->em;
        $params = array( 'userId'=> 1,
            'adsId'=>132,
            'adsName'=>'xxxbbb',
            'siteId'=>123,
            'linkId'=>12314,
            'ordersPrice'=>0.01,
            'orderTime'=> 1411134232,
            'ocd'=>'f0ij20f99239i09ri920r32'
        );
        $return =$em->getRepository('JiliApiBundle:DuomaiOrder')
            ->init($params);
        $this->assertInstanceOf('\Jili\ApiBundle\Entity\DuomaiOrder', $return);
        $this->assertNotNull($return->getId());
        $this->assertEquals(1, $return->getStatus(),'init order status should be 1');
    }

}
