<?php

namespace Jili\BackendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadAuditOrderData;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
class DecemberActivityControllerTest extends WebTestCase
{
 
    private $em ;
    private $has_fixture;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container  = static::$kernel->getContainer();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        // $em = ?
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();
        if($tn === 'testAuditActionNormal') {
            $loader= new Loader();
            $fixture= new LoadAuditOrderData();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }
        $this->has_fixture = true;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->has_fixture) {
            $this->em->close();
        }
    }


    /**
     * @group issue_537
     */
    public function testListAllAction()
    {

        $client = $this->client;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall');
       
        $expected_url = 'https://localhost/admin/activity/december/list-orders';
        $this->assertEquals( $expected_url , $url);
        
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall', array('p'=>1));
        $this->assertEquals( $expected_url , $url);

      
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall', array('p'=>10));
        $expected_url = 'https://localhost/admin/activity/december/list-orders/10';
        $this->assertEquals( $expected_url , $url);
        // no data get
    }

    /**
     * @group issue_537
     */
    public function testAuditActionNormal()
    {
        // prepare testing data
        $client = $this->client;
        $container = $this->container;
        $em=$this->em;

        // valid 
        $order = LoadAuditOrderData::$ORDERS[0];
        $user = LoadAuditOrderData::$USERS[0];
        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ));
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[orderPaid]']->setValue('300.90');
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_VALID );
        $form['order[auditBy]']->setValue('yuki');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());

        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo', $actual);
        $this->assertEquals(0 , $actual->getOffcutForNext());
        $this->assertEquals(8 , $actual->getNumOfCommon());
        $this->assertEquals(0 , $actual->getNumOfConsolation());
        $this->assertEquals(300.90 , $actual->getTotalPaid());


        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=> 300.90,
                'isValid'=> GameEggsBreakerTaobaoOrder::ORDER_VALID,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);

        // invalid
        $order = LoadAuditOrderData::$ORDERS[1];
        $user = LoadAuditOrderData::$USERS[1];
        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ), true);
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_INVALID );
        $form['order[auditBy]']->setValue('mandy');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=>0,
                'isValid'=>GameEggsBreakerTaobaoOrder::ORDER_INVALID,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_INIT
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);
        // check 
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());
        $this->assertNUll($actual);


        // uncertain .
        $order = LoadAuditOrderData::$ORDERS[2];
        $user = LoadAuditOrderData::$USERS[2];
        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ), true);
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_UNCERTAIN );
        $form['order[auditBy]']->setValue('mandy');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo', $actual);
        $this->assertEquals(0 , $actual->getOffcutForNext());
        $this->assertEquals(0 , $actual->getTotalPaid());
        $this->assertEquals(0 , $actual->getNumOfCommon());
        $this->assertEquals(1 , $actual->getNumOfConsolation());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=>0,
                'isValid'=>GameEggsBreakerTaobaoOrder::ORDER_UNCERTAIN,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);

        // sb with previous eggsInfo
        $order = LoadAuditOrderData::$ORDERS[3];
        $user = LoadAuditOrderData::$USERS[3];
        $info = LoadAuditOrderData::$INFOS[0];

        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ), true);
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_UNCERTAIN );
        $form['order[auditBy]']->setValue('mandy');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo', $actual);
        $this->assertEquals(49.03 , $actual->getOffcutForNext());
        $this->assertEquals(149.99 , $actual->getTotalPaid());
        $this->assertEquals(4 , $actual->getNumOfCommon());
        $this->assertEquals(4 , $actual->getNumOfConsolation());
        $this->assertNotEquals( $info->getToken(), $actual->getToken());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=>0,
                'isValid'=>GameEggsBreakerTaobaoOrder::ORDER_UNCERTAIN,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);

        // invalid , no changes on token!!
        $order = LoadAuditOrderData::$ORDERS[4];
        $user = LoadAuditOrderData::$USERS[4];
        $info = LoadAuditOrderData::$INFOS[1];

        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ), true);
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_INVALID );
        $form['order[auditBy]']->setValue('mandy');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo', $actual);
        $this->assertEquals(49.03 , $actual->getOffcutForNext());
        $this->assertEquals(149.99 , $actual->getTotalPaid());
        $this->assertEquals(4 , $actual->getNumOfCommon());
        $this->assertEquals(3 , $actual->getNumOfConsolation());
        $this->assertEquals( $info->getToken(), $actual->getToken());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=>0,
                'isValid'=>GameEggsBreakerTaobaoOrder::ORDER_INVALID,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_INIT
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);
        

        // valid ,  changed on token!!
        $order = LoadAuditOrderData::$ORDERS[5];
        $user = LoadAuditOrderData::$USERS[5];
        $info = LoadAuditOrderData::$INFOS[2];

        $url =$container->get('router')->generate('jili_backend_decemberactivity_audit', array('id'=> $order->getId() ), true);
        $this->assertEquals('https://localhost/admin/activity/december/audit-order/'. $order->getId() , $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200,$client->getResponse()->getStatusCode() );
        $form = $crawler->selectButton('提交' ) ->form();
        $form['order[isValid]']->setValue(GameEggsBreakerTaobaoOrder::ORDER_VALID );
        $form['order[auditBy]']->setValue('mandy');
        $form['order[orderPaid]']->setValue(0.97);
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo', $actual);
///        $this->assertEquals(0 , $actual->getOffcutForNext());
        $this->assertEquals(150.96 , $actual->getTotalPaid());
        $this->assertEquals(5 , $actual->getNumOfCommon());
        $this->assertEquals(3 , $actual->getNumOfConsolation());
        $this->assertNotEquals( $info->getToken(), $actual->getToken());
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('id'=> $order->getId(),
                'userId'=>$user->getId(),
                'orderPaid'=>0.97,
                'isValid'=>GameEggsBreakerTaobaoOrder::ORDER_VALID,
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_COMPLETED,
                'isEgged'=>GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED
            ));
        $this->assertNotNUll($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);

    }
}
