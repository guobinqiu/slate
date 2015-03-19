<?php
namespace  Jili\ApiBundle\Tests\Services\Flow;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlowMobileValidateApiProcessorTest extends KernelTestCase {

    /**
     * @group issue_682
     */
    public function testProcess() {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container = static :: $kernel->getContainer();

        // get service
        $service = $container->get('flow_mobilevalidate.processor');
        $return = $service->process('13761756201');
        $this->assertEquals(200, $return['resultcode']);
        $this->assertEquals('移动', $return['provider']);
        $this->assertEquals('上海', $return['province']);
        $this->assertEquals(3, count($return['product_list']));
        $this->assertEquals(20150, $return['product_list'][0]['custom_product_id']);
        $this->assertEquals(150, $return['product_list'][0]['packagesize']);
        $this->assertEquals(15.500, $return['product_list'][0]['custom_prise']);
        $this->assertEquals(1660, $return['product_list'][0]['change_point']);
    }
}