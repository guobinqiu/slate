<?php
namespace  Jili\ApiBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AlertToSlackTest extends KernelTestCase {

    /**
     */
    public function testSendAlertToSlack() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container = static :: $kernel->getContainer();
        $service = $container->get('alert_to_slack');
        $content = 'this is a testing notification (ignore me)';
        $return = $service->sendAlertToSlack($content);
        $this->assertTrue($return);
    }
}