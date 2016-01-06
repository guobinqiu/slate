<?php
namespace  Jili\ApiBundle\Tests\Services\Dmdelivery;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class ClientTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

//        $fixture = new LoadExchangeFlowOrderData();
//        $loader = new Loader();
//        $loader->addFixture($fixture);
//        $executor->execute($loader->getFixtures());

        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    public function testDemo() 
    {
        $container= $this->container;
        $client = $container->get('soap.mail.listener');
        $this->assertInstanceOf( '\Jili\ApiBundle\Services\Dmdelivery\Client', $client);

        $this->assertEquals(1,1);
    }
}
