<?php
namespace  Jili\ApiBundle\Tests\Services\Flow;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FlowMobileValidateApiProcessorTest extends KernelTestCase {

    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @group issue_682
     */
    public function testProcess() {
        $container = $this->container;

        // get service
        $service = $container->get('flow_mobilevalidate.processor');
        $return = $service->process('13761756201');
        $this->assertEquals(200, $return['resultcode']);
        $this->assertEquals('移动', $return['provider']);
        $this->assertEquals('上海', $return['province']);
        $this->assertNotNull($return['product_list']);
        $this->assertEquals(20150, $return['product_list'][0]['custom_product_id']);
        $this->assertEquals(150, $return['product_list'][0]['packagesize']);
        $this->assertEquals(15.500, $return['product_list'][0]['custom_prise']);
        $this->assertEquals(1660, $return['product_list'][0]['change_point']);

        //有错误场合
        $return = $service->process('1376175620');
        $this->assertEquals(206, $return['resultcode']);
        $this->assertEquals('手机号码不正确', $return['error_message']);
    }

    /**
     * @group issue_682
     */
    public function testGetChangePoint() {
        $container = $this->container;
        $data['product_list'] = array (
            0 => array (
                'custom_product_id' => 20030,
                'packagesize' => 30,
                'custom_prise' => 4.000
            ),
            1 => array (
                'custom_product_id' => 20070,
                'packagesize' => 70,
                'custom_prise' => 8.000
            ),
            2 => array (
                'custom_product_id' => 20150,
                'packagesize' => 150,
                'custom_prise' => 15.500
            ),
            3 => array (
                'custom_product_id' => 20500,
                'packagesize' => 500,
                'custom_prise' => 24.000
            ),
            4 => Array (
                'custom_product_id' => 21000,
                'packagesize' => 1024,
                'custom_prise' => 40.000
            )
        );

        $service = $container->get('flow_mobilevalidate.processor');
        $return = $service->getChangePoint($data);
        $this->assertEquals(3, count($return['product_list']));
        $this->assertEquals(round(15.500 * 1.07, 1) * 100, $return['product_list'][0]['change_point']);
        $this->assertEquals(round(24.000 * 1.03, 1) * 100, $return['product_list'][1]['change_point']);
        $this->assertEquals(round(40.000 * 1.01, 1) * 100, $return['product_list'][2]['change_point']);
    }
}
