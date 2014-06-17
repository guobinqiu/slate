<?php
namespace Jili\ApiBundle\Tests\EventListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RebateActivityFunctionalTest extends WebTestCase {

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

    public function testgetRebate() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $rebate_point_service = $container->get('rebate_point.caculator');

        $emar_rebate = $rebate_point_service->getRebate("emar");
        $this->assertEquals(70, $emar_rebate);

        $emar_rebate = $rebate_point_service->getRebate("");
        $this->assertEquals(70, $emar_rebate);
    }
}