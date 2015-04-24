<?php
namespace  Jili\ApiBundle\Tests\Services\Flow;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlowOrderApiLoggerTest extends KernelTestCase {

    /**
     * @group issue_682
     */
    public function testLog() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $apireturn1 = $em->getRepository('JiliApiBundle:FlowOrderApiReturn')->findAll();

        // get service
        $api_logger = $container->get('flow_order_api.init_log');
        $api_logger->log('content');

        $apireturn2 = $em->getRepository('JiliApiBundle:FlowOrderApiReturn')->findAll();

        $this->assertEquals(1, count($apireturn2) - count($apireturn1));
    }
}
