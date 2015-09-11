<?php
namespace  Jili\ApiBundle\Tests\Services\Flow;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlowOrderCreateApiProcessorTest extends KernelTestCase {

    /**
     * @group issue_682
     */
    public function testProcess() {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container = static :: $kernel->getContainer();

        // get service
        $service = $container->get('flow_ordercreate.processor');

        $param['custom_product_id'] = '30500';
        $param['mobile'] = '18016018626';
        $param['custom_order_sn'] = 1;
        $return = $service->process($param);

        $this->assertNotNull($return['resultcode']);
        $this->assertNotNull($return['custom_order_sn']);

        //错误场合
        $param['custom_product_id'] = '30500';
        $param['mobile'] = '18016018626';
        $param['custom_order_sn'] = 1;
        $return = $service->process($param);
        $this->assertEquals(207, $return['resultcode']);
        $this->assertEquals('对不起，不能兑换，请联系客服！', $return['error_message']);

        $param['custom_product_id'] = '30500';
        $param['mobile'] = '1801601862600';
        $param['custom_order_sn'] = 10000;
        $return = $service->process($param);
        $this->assertEquals(206, $return['resultcode']);
        $this->assertEquals('手机号码不正确', $return['error_message']);

        //错误场合 手机号码不正确
        $param['custom_product_id'] = '000000';
        $param['mobile'] = '1801601862600';
        $param['custom_order_sn'] = 10000;
        $return = $service->process($param);
        $this->assertEquals(212, $return['resultcode']);
        $this->assertEquals('请求的产品暂时关停', $return['error_message']);
    }
}
