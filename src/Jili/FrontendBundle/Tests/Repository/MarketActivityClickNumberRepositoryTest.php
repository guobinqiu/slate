<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarketActivityClickNumberRepositoryTest extends KernelTestCase {

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

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    public function testGetClickNumber() {
        $em = $this->em;
        $marketActivityId = 9;
        $result = $em->getRepository('JiliFrontendBundle:MarketActivityClickNumber')->getClickNumber($marketActivityId);
        $this->assertEquals(3, $result['clickNumber']);
    }
}