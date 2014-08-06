<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarketActivityClickListRepository extends KernelTestCase {

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

    /**
     * @group point_recent
     */
    public function testGetRecentPoint() {
        $em = $this->em;
        $marketActivityId = 9;
        $result = $em->getRepository('JiliFrontendBundle:MarketActivityClickList')->clickCount($marketActivityId);

        $this->assertEquals(1, $result['num']);
    }
}