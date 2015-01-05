<?php
namespace  Jili\ApiBundle\Tests\Services\Bangwoya;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BangwoyaApiLoggerTest extends KernelTestCase {

    /**
     * @group issue_578
     */
    public function testLog() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $apireturn1 = $em->getRepository('JiliApiBundle:BangwoyaApiReturn')->findAll();

        // get service
        $api_logger = $container->get('bangwoya_api.init_log');
        $api_logger->log('content');

        $apireturn2 = $em->getRepository('JiliApiBundle:BangwoyaApiReturn')->findAll();

        $this->assertEquals(1, count($apireturn2) - count($apireturn1));
    }
}