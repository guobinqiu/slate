<?php
namespace  Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AlertToSlackTest extends KernelTestCase {

    /**
     */
    public function testSendMessage() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container = static :: $kernel->getContainer();
        $service = $container->get('alert_to_slack');
        $content = 'test: Alert to slack from 91jili system.';
        $return = $service->sendMessage($content);
        $this->assertTrue($return);
    }
}