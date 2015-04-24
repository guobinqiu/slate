<?php
namespace  Jili\ApiBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadExchangeFlowOrderData;

class ExchangeProcessTest extends KernelTestCase {

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
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_682
     */
    public function testExchangeOk() {
        $em = $this->em;
        $container = $this->container;

        $order = LoadExchangeFlowOrderData :: $EXCHANGE_FLOW_ORDER[0];
        $user_id = $order->getUserId();
        $exchange_id = $order->getExchangeId();

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $user_points_1 = $user->getPoints();

        $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $this->assertNull($exchange->getStatus());

        // get service
        $exchange_service = $container->get('exchange.processor');
        $log_path = $container->getParameter('flow_file_path_flow_api_log');
        $type = 5;
        $return = $exchange_service->exchangeOK($order->getExchangeId(), null, null, $type, $log_path);
        $this->assertTrue($return);

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $user_points_2 = $user->getPoints();
        $this->assertEquals($user_points_2, $user_points_1);

        $exchange2 = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $this->assertEquals(1, $exchange2->getStatus());

        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($user_id % 10))->findOneBySendTo($user_id);
        $this->assertEquals('流量包兑换成功', $sendMessage->getTitle());

        $pointHistory = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->findOneByUserId($user_id);
        $this->assertEquals(- $exchange->getTargetPoint(), $pointHistory->getPointChangeNum());
        $this->assertEquals(24, $pointHistory->getReason());
    }

    /**
     * @group issue_682
     */
    public function testExchangeNg() {
        $em = $this->em;
        $container = $this->container;

        $order = LoadExchangeFlowOrderData :: $EXCHANGE_FLOW_ORDER[0];
        $user_id = $order->getUserId();
        $exchange_id = $order->getExchangeId();

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $user_points_1 = $user->getPoints();

        $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $this->assertNull($exchange->getStatus());

        // get service
        $exchange_service = $container->get('exchange.processor');
        $log_path = $container->getParameter('flow_file_path_flow_api_log');
        $type = 5;
        $return = $exchange_service->exchangeNg($order->getExchangeId(), null, null, $type, $log_path);
        $this->assertTrue($return);

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $user_points_2 = $user->getPoints();
        $this->assertEquals($exchange->getTargetPoint(), $user_points_2 - $user_points_1);

        $exchange2 = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        $this->assertEquals(2, $exchange2->getStatus());

        $sendMessage = $em->getRepository('JiliApiBundle:SendMessage0' . ($user_id % 10))->findOneBySendTo($user_id);
        $this->assertEquals('流量包兑换失败', $sendMessage->getTitle());
    }
}
