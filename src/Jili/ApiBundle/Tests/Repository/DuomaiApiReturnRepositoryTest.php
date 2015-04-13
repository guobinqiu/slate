<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

class DuomaiApiReturnRepositoryTest extends KernelTestCase {

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
    public function testLogEmpty() 
    {
        $em = $this->em;
        $return =$em->getRepository('JiliApiBundle:DuomaiApiReturn')
            ->log();
        $this->assertSame(null, $return);
        $logEntities = $this->em->getRepository('JiliApiBundle:DuomaiApiReturn')->findAll();
        $this->count(0, $logEntities);
    }

    /**
     * @group issue_680 
     */
    public function testLogNotEmpty() 
    {
        $em = $this->em;
        $return =  $em->getRepository('JiliApiBundle:DuomaiApiReturn')
            ->log('/api/duomai/getInfo?a=1&b=2');

        $this->assertSame(null, $return);

        $entities = $em->getRepository('JiliApiBundle:DuomaiApiReturn')
            ->findAll();

        $logEntities = $this->em->getRepository('JiliApiBundle:DuomaiApiReturn')->findAll();
        $this->count(1, $logEntities);
        $this->assertEquals('/api/duomai/getInfo?a=1&b=2',$logEntities[0]->getContent());
    }
}


