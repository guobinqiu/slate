<?php
namespace  Jili\ApiBundle\Tests\Services\Flow;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlowOrderCreateApiProcessorTest extends KernelTestCase {

    /**
     * @group issue_682
     * @group debug
     */
    public function testProcess() {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container = static :: $kernel->getContainer();

        // get service
        $service = $container->get('flow_ordercreate.processor');

        $param['custom_product_id'] = '30010';
        $param['mobile'] = '18016018626';
        $param['custom_order_sn']=1;
        $return = $service->process($param);

        $this->assertEquals(101, $return['resultcode']);
        $this->assertEquals(1, $return['custom_order_sn']);
    }
}