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

        //$this->assertEquals(302, $client->getResponse()->getStatusCode());
        //$client->followRedirect();
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // check uri 
        //$session = $client->getRequest()->getSession();
        //$this->assertTrue( $session->has('goToUrl'));
        //$this->assertEquals('/activity/december/', $session->get('goToUrl'));

        //        $form = $crawler->selectButton('submit')->getForm();

        // $client->getResponse()->getContent();

        // invalid form inputs 
        // duplicated post ? 
        //$session  = $client->getRequest()->getSession();
        //$session->set('uid' , 1);
        //$session->save();

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
        $form['order[orderId]']->setValue('myorderid001');
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
            ->findOneBy(array('userId'=> 1, 'orderId'=>'myorderid001' , 'orderAt'=> $day));
        $this->assertNotNull( $expected_record);

    }

    /**
     * @group issue_537
     * @group debug 
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
        $expected_response = '{"code":0,"data":{"token":"'.$actual_info->getToken() .'","numOfEggs":4,"numOfConsolationEggs":3,"lessForNextEgg":0,"isStart":'.$isStart.'}}';

        $this->assertEquals($expected_response, $client->getResponse()->getContent());

    }

    /**
     * @group issue_537
     */
    public function testBreakEggActionNormal() 
    {
// prepare data token 
// prepare pointsPool

// ajax post
        $this->assertEquals(1,1);

// 
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
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    //    echo $client->getResponse()->getContent();
     //   echo PHP_EOL;

    }
}
