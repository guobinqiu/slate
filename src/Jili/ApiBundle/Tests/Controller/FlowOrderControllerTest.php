<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadExchangeFlowOrderData;

class FlowOrderControllerTest extends WebTestCase {

    /**
      * @var \Doctrine\ORM\EntityManager
      */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $contianer = static :: $kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new LoadExchangeFlowOrderData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $contianer;
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
    public function testGetInfoAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $order = LoadExchangeFlowOrderData :: $EXCHANGE_FLOW_ORDER[0];

        $url = $container->get('router')->generate('_api_flow_getinfo');
        $post['custom_order_sn'] = $order->getId();
        $post['status'] = 'success';
        $post['msg'] = '';
        $post['desc'] = '';
        $this->assertEquals('/api/flow/getInfo',$url);
        $crawler = $client->request('POST', $url, array (), array (), array ('REMOTE_ADDR'=> '59.83.33.8'), json_encode($post));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $return = $client->getResponse()->getContent();
        $this->assertEquals('true', $return);
    }
}
