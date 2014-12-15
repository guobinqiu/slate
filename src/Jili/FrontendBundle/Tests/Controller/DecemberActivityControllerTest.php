<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


use Jili\FrontendBundle\DataFixtures\ORM\Controller\DecemberActivity\LoadGetEggsInfoData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadTaobaoOrdersData;
class DecemberActivityControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     **/
    private $em;

    /**
     * @var boolean 
     **/
    private $has_fixture;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();


        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $tn = $this->getName();
        if( in_array($tn, array('testGetEggsInfoActionNormal','testBreakEggActionNormal'))){
            $fixture = new LoadGetEggsInfoData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());

        } elseif ('testEggsSentStatAction' === $tn) {
            $fixture = new LoadTaobaoOrdersData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }
        $this->has_fixture = true ;

        $this->em  = $em;
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
    public function testIndexAction()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_index');
        $this->assertEquals('/activity/december/', $url);
    }

    /**
     * @group issue_537
     */
    public function testAddTaobaoOrderActionValidation()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_addtaobaoorder');
        $this->assertEquals('/activity/december/add-taobao-order', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // post without sign in.
        
        $crawler = $client->request('POST', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

// post invalid date field
        $this->assertEquals('/activity/december/add-taobao-order', $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('123456789012345');
        $form['order[orderAt]']->setValue('2');

        $session  = $container->get('session');
        $session->set('uid' , 1);
        $session->save();
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $error_message = trim( $crawler->filter('div.alert-error')->eq(0)->text() );
        $this->assertEquals('*需要填写有效的日期, 如: 2014-12-20',$error_message);
        // invalid  order id wrong length 
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('12345678901234');
        $form['order[orderAt]']->setValue('2014-12-12');
        $client->submit($form);
        $crawler = $client->followRedirect();
        $error_message = trim( $crawler->filter('div.alert-error')->eq(0)->text() );
        $this->assertEquals('*需要填0~9组成的订单号',$error_message);
        $error_message = trim( $crawler->filter('div.alert-error')->eq(1)->text() );
        $this->assertEquals('*需要填15位订单号',$error_message);


        // invalid  order id wrong character 
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('12345678901234a');
        $form['order[orderAt]']->setValue('2014-12-12');
        $client->submit($form);
        $crawler = $client->followRedirect();
        $error_message = trim( $crawler->filter('div.alert-error')->eq(0)->text() );
        $this->assertEquals('*需要填0~9组成的订单号',$error_message);

    }
    /**
     * @group issue_537
     */
    public function testAddTaobaoOrderActionValidationII()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_addtaobaoorder');
        $session  = $container->get('session');
        $session->set('uid' , 1);
        $session->save();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('123456789012345');
        $form['order[orderAt]']->setValue(date('Y-m-d'));
        $crawler = $client->submit($form);
        // invalid duplicated order id 
        //post again
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('123456789012345');
        $form['order[orderAt]']->setValue(date('Y-m-d'));
        $crawler = $client->submit($form);

        $crawler = $client->followRedirect();
        $error_message = trim( $crawler->filter('div.alert-error')->eq(0)->text() );
        $this->assertEquals('*你已经提交过相同的订单号',$error_message);
    }

    /**
     * @group issue_537
     */
    public function testAddTaobaoOrderActionNormal()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_addtaobaoorder');
        $this->assertEquals('/activity/december/add-taobao-order', $url);

        $session  = $container->get('session');
        $session->set('uid' , 1);
        $session->save();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());


        $form = $crawler->selectButton('submit')->form();
        $form['order[orderId]']->setValue('123456789012345');
        $form['order[orderAt]']->setValue(date('Y-m-d'));
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('/activity/december/', $client->getRequest()->getRequestUri());

        $day = new \DateTime();
        $day->setTime(0,0);

        // check result
        $expected_record= $this->em 
            ->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('userId'=> 1, 'orderId'=>'123456789012345' , 'orderAt'=> $day));
        $this->assertNotNull( $expected_record);

    }

    /**
     * @group issue_537
     */
    public function testGetEggsInfoActionNormal()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_geteggsinfo');
        $this->assertEquals('/activity/december/get-eggs-info', $url);

        $user  = LoadGetEggsInfoData::$USERS[0];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();

        $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $actual_info  = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());

        $startAt = new \Datetime('2015-01-20 00:00:00');
        $now = new \Datetime();
        $isStart =  ( $now >= $startAt ) ? 'true': 'false'  ;
        $expected_response = '{"code":0,"data":{"token":"'.$actual_info->getToken() .'","numOfEggs":4,"numOfConsolationEggs":3,"lessForNextEgg":19.97,"isStart":'.$isStart.'}}';

        $this->assertEquals($expected_response, $client->getResponse()->getContent());

    }

    /**
     * @group issue_537
     */
    public function testBreakEggActionNormal() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');

        $this->assertEquals('/activity/december/break-egg', $url);
        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[0];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();

        $info = LoadGetEggsInfoData::$INFOS[0];

        $data = array('data' =>$info->getToken(), 'eggType'=> 0);

        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        echo $client->getResponse()->getContent();
        echo PHP_EOL;
    }

    /**
     * @group issue_537
     */
    public function testEggsSentStatAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_eggssentstat');

        $config =$container->getParameter('game_eggs_breaker');
        $file = $config['sent_stat'];
        @unlink($file);

        $this->assertEquals('/activity/december/eggs-sent-stat', $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10 , $crawler->filter('ul > li') );
        @unlink($file);
    }
}
