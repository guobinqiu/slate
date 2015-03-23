<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Controller\ExchangeController;
use Jili\ApiBundle\Entity\PointsExchange;

class ExchangeControllerTest extends WebTestCase {

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

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
    }

    /**
     * @group issue_682
     */
    public function testFlowInfoAction() {
        $client = static :: createClient();
        $container = static :: $kernel->getContainer();
        $em = $this->em;
        $session = $container->get('session');

        $session->remove('uid');
        $session->remove('csrf_token');
        $session->save();

        $query = array (
            'tokenKey' => '123',
            'uid' => 1
        );
        $url = $container->get('router')->generate('_exchange_flowInfo', $query);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_682
     */
    public function testGetFlowListAction() {
        $client = static :: createClient();
        $container = static :: $kernel->getContainer();
        $em = $this->em;
        $session = $container->get('session');

        $session->remove('uid');
        $session->remove('csrf_token');
        $session->save();

        $query = array (
            'tokenKey' => '123',
            'uid' => 1
        );
        $url = $container->get('router')->generate('_exchange_flowList', $query);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_682
     */
    public function testCheckFlowMobile() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $controller = new ExchangeController();
        $controller->setContainer($container);
        $em = $this->em;

        $user_id = 1;

        $existMobile = '';
        $mobile = '';
        $re_mobile = '';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertEquals('输入的手机格式不正确', $return);

        $existMobile = '';
        $mobile = '13761756201';
        $re_mobile = '';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertEquals('2次输入的手机号码不相同', $return);

        $existMobile = '';
        $mobile = '23761756201';
        $re_mobile = '';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertEquals('输入的手机格式不正确', $return);

        $existMobile = '';
        $mobile = '13761756201';
        $re_mobile = '13761756201';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertNull($return);

        $existMobile = '13761756201';
        $mobile = '';
        $re_mobile = '';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertEquals('请输入您的手机号码', $return);

        $pointschange = new PointsExchange();
        $pointschange->setUserId(1);
        $pointschange->setType(5);
        $pointschange->setSourcePoint(2000);
        $pointschange->setTargetPoint(100);
        $pointschange->setTargetAccount('13761756201');
        $pointschange->setExchangeItemNumber(30);
        $pointschange->setIp('127.0.0.0');
        $em->persist($pointschange);
        $em->flush();

        $existMobile = '13761756201';
        $mobile = '';
        $re_mobile = '';
        $return = $controller->checkFlowMobile($existMobile, $mobile, $re_mobile, $user_id);
        $this->assertNull($return);
    }

    /**
    * @group issue_682
    */
    public function testGetFlowSaveAction() {
        $client = static :: createClient();
        $container = static :: $kernel->getContainer();
        $em = $this->em;
        $session = $container->get('session');

        $session->remove('uid');
        $session->remove('csrf_token');
        $session->save();

        $query = array (
            'tokenKey' => '123',
            'uid' => 1
        );
        $url = $container->get('router')->generate('_exchange_flowSave', $query);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}