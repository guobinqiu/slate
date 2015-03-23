<?php
namespace  Jili\ApiBundle\Tests\Services\Bangwoya;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadExchangeFlowOrderData;

class FlowOrderRequestProcessorTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $fixture = new LoadExchangeFlowOrderData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * @group issue_682
     */
    public function testProcess() {
        $em = $this->em;
        $container = $this->container;

        $order = LoadExchangeFlowOrderData :: $EXCHANGE_FLOW_ORDER[0];
        $data = array ();
        $data['custom_order_sn'] = $order->getId();
        $data['status'] = 'ng';
        $service = $container->get('flow_order_request.processor');
        $result = $service->process($data);
        $this->assertFalse($result);

        $data = array ();
        $data['custom_order_sn'] = 123456;
        $data['status'] = 'error';
        $service = $container->get('flow_order_request.processor');
        $result = $service->process($data);
        $this->assertFalse($result);

        $data = array ();
        $data['custom_order_sn'] = $order->getId();
        $data['status'] = 'error';
        $service = $container->get('flow_order_request.processor');
        $result = $service->process($data);
        $this->assertTrue($result);

        $data = array ();
        $data['custom_order_sn'] = $order->getId();
        $data['status'] = 'success';
        $service = $container->get('flow_order_request.processor');
        $result = $service->process($data);
        $this->assertTrue($result);
    }

    /**
     * @group issue_682
     */
    public function testCheckData() {
        $container = $this->container;

        $data = array ();
        $data['custom_order_sn'] = 1;
        $service = $container->get('flow_order_request.processor');
        $result = $service->checkData($data);
        $this->assertFalse($result);

        $data = array ();
        $data['status'] = 'success';
        $service = $container->get('flow_order_request.processor');
        $result = $service->checkData($data);
        $this->assertFalse($result);

        $data = array ();
        $data['custom_order_sn'] = 1;
        $data['status'] = 'ng';
        $service = $container->get('flow_order_request.processor');
        $result = $service->checkData($data);
        $this->assertFalse($result);

        $data = array ();
        $data['custom_order_sn'] = 1;
        $data['status'] = 'success';
        $service = $container->get('flow_order_request.processor');
        $result = $service->checkData($data);
        $this->assertTrue($result);

        $data = array ();
        $data['custom_order_sn'] = 1;
        $data['status'] = 'error';
        $service = $container->get('flow_order_request.processor');
        $result = $service->checkData($data);
        $this->assertTrue($result);
    }
}