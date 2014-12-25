<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadHandleExchangeWenData;

class ExchangeFromWenwenRepository extends KernelTestCase {

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
        $executor->purge();

        // load fixtures
        $fixture = new LoadHandleExchangeWenData();
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
     * @group issue_535
     */
    public function testExFromWen() {
        $start_time = null;
        $end_time = null;

        $exFrWen = $this->em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time, $end_time);
        $this->assertEquals(1, count($exFrWen));
        $this->assertNotNull(1, $exFrWen[0]['userWenwenCrossId']);

        $start_time = date('Y-m-d') . " 20:00:00";
        $exFrWen = $this->em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time, $end_time);
        $this->assertEquals(0, count($exFrWen));

        $start_time = date('Y-m-d') . " 00:00:00";
        $end_time = date('Y-m-d') . " 23:00:00";
        $exFrWen = $this->em->getRepository('JiliApiBundle:ExchangeFromWenwen')->exFromWen($start_time, $end_time);
        $this->assertEquals(1, count($exFrWen));
    }
}
