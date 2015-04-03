<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


use Jili\FrontendBundle\DataFixtures\ORM\Controller\DecemberActivity\LoadGetEggsInfoData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadTaobaoOrdersData;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoCategoryData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadTaobaoSelfPromotionProductData;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadLogsData;

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
        $container = static::$kernel->getContainer();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $tn = $this->getName();
        if( in_array($tn, array('testGetEggsInfoActionNormal','testBreakEggActionNormal','testBreakEggActionCommon','testBreakEggActionConsolation','testBreakEggActionConsolationZero','testBreakEggActionNone','testBreakEggActionNormalOneMoreChance'))){
            $fixture = new LoadGetEggsInfoData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());

        } elseif ('testEggsSentStatAction' === $tn) {
            $fixture = new LoadTaobaoOrdersData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        } elseif ('testBrokersListAction' === $tn) {
            $fixture = new LoadLogsData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());

        } else if(in_array( $tn ,array('testIndexAction','testAddTaobaoOrderActionNormal' ,'testAddTaobaoOrderActionValidation','testAddTaobaoOrderActionValidationII','testAddTaobaoOrderActionValidationIII') )) {
            $loader  = new Loader();
            $fixture = new LoadTaobaoCategoryData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);

            $fixture1 = new LoadTaobaoSelfPromotionProductData();
            $fixture1->setContainer($container);
            $loader->addFixture($fixture1);
            $executor->execute($loader->getFixtures());
        }

        $this->has_fixture = true ;
        $container  = static::$kernel->getContainer();

        // points pool related
        if( in_array($tn, array('testGetEggsInfoActionNormal','testBreakEggActionNormal','testBreakEggActionCommon','testBreakEggActionConsolation','testBreakEggActionConsolationZero','testBreakEggActionNone','testBreakEggActionNormalOneMoreChance'))){
            // prepare the points pool
            $configs = $container->getParameter('game_eggs_breaker');
            $cache_dir =$container->getParameter('cache_data_path');

            $file_strategy = $cache_dir.'/game_eggs_breaker/common/points_strategy_conf.json';
            if( file_exists($file_strategy)) {
                unlink($file_strategy);
            }
            $file_consolation_strategy = $cache_dir.'/game_eggs_breaker/consolation/points_strategy_conf.json';
            if( file_exists($file_strategy)) {
                unlink($file_strategy);
            }

            if (file_exists($file_consolation_strategy)) {
                unlink($file_consolation_strategy);
            }
            $dir = dirname($file_strategy);
            if( ! file_exists($dir)) {
                mkdir(  $dir , 0700, true) ;
            }
            $dir = dirname($file_consolation_strategy);
            if( ! file_exists($dir)) {
                mkdir(  $dir , 0700, true) ;
            }

            if( $tn === 'testBreakEggActionConsolationOneMoreChance') {
                file_put_contents( $file_consolation_strategy, json_encode(array(array(1,-1))));
            } if($tn === 'testBreakEggActionConsolationZero' )  {
                file_put_contents( $file_consolation_strategy, json_encode(array(array(1,0))));
            } else{
                file_put_contents( $file_consolation_strategy, json_encode(array(array(1,1))));
            }

            if( $tn === 'testBreakEggActionNormalOneMoreChance') {
                file_put_contents( $file_strategy, json_encode(array(array(1,-1))));
            } else {
                file_put_contents( $file_strategy, json_encode(array(array(1,7))));

            }

            $file_pool = str_replace('YYYYmmdd', '', $configs['common']['points_pool']);

            if(file_exists($file_pool)){
                unlink($file_pool);
            }
            $file_pool = str_replace('YYYYmmdd', '', $configs['consolation']['points_pool']);

            if(file_exists($file_pool)){
                unlink($file_pool);
            }
        }
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

        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    /**
     * @group issue_537
     */
    public function testAddTaobaoOrderActionValidation()
    {
    //    $this->markTestSkipped('this function has been offline');

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

        $html  = $client->getResponse()->getContent();

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
        $this->assertEquals('*需要填15-16位订单号',$error_message);


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
        $this->assertEquals('*你已经提交过相同的订单号.',$error_message);
    }


    /**
     * @group issue_592
     */
    public function testAddTaobaoOrderActionValidationIII()
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
        $form['order[orderId]']->setValue('1 2 3456789012345 ');
        $form['order[orderAt]']->setValue(date('Y-m-d'));
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
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

        $expected_response = '{"code":0,"data":{"token":"'.$actual_info->getToken() .'","numOfEggs":4,"numOfConsolationEggs":3,"lessForNextEgg":10.03,"isOpenSeason":'.$isStart.'}}';

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
        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $this->assertRegExp('/{"code":0,"data":{"points":(1|7)}}/', $client->getResponse()->getContent());


        //userpoint task_history point_history log
        $em = $this->em;

        $this->assertNotNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ) )->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId())));

        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId() ));
        $this->assertNotNull($user_actual);
        $this->assertGreaterThan($user->getPoints() ,$user_actual->getPoints());
        $diff = $user_actual->getPoints() - $user->getPoints();
        $this->assertRegExp('/(1|7)/', (string) $diff , ' diff after egg breaks');
    }

    /**
     * @group issue_537
     */
    public function testBreakEggActionCommon()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');
        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[1];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();
        $info = LoadGetEggsInfoData::$INFOS[1];
        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"data":{"points":7}}', $client->getResponse()->getContent());

        $em = $this->em;
        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId(),'points' => 107 ));
        $this->assertNotNull($user_actual);
        $this->assertNotNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));
        $this->assertNotNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId())));
    }

    /**
     * @group issue_537
     */
    public function testBreakEggActionConsolation()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');

        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[2];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();
        $info = LoadGetEggsInfoData::$INFOS[2];
        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"data":{"points":1}}', $client->getResponse()->getContent());

        $em = $this->em;
        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId() ,'points'=>101));

        $this->assertNotNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId())));

    }


    /**
     * @group issue_537
     */
    public function testBreakEggActionConsolationZero()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');
        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[2];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();
        $info = LoadGetEggsInfoData::$INFOS[2];
        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"data":{"points":0}}', $client->getResponse()->getContent());

        $em = $this->em;
        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId() ,'points'=>100));

        $this->assertNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNotNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId())));
    }

    /**
     * @group issue_537
     */
    public function testBreakEggActionNone()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');
        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[3];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();
        $info = LoadGetEggsInfoData::$INFOS[3];
        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertEquals('{}', $client->getResponse()->getContent());

        $em = $this->em;
        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId(),'points'=> 100 ));
        $this->assertNotNull($user_actual);

        $this->assertNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ) )->findOneBy(array('userId'=> $user->getId())));

        $this->assertNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId())));

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

    /**
     * @group issue_537
     */
    public function  testBrokersListAction()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();

        $url =$container->get('router')->generate('jili_frontend_decemberactivity_brokerlist');

        $config =$container->getParameter('game_eggs_breaker');
        $file = $config['broken_stat'];
        @unlink($file);

        $this->assertEquals('/activity/december/broken-stat', $url);
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(10 , $crawler->filter('ul > li') );
        @unlink($file);
    }

    /**
     *
     * @group issue_537
     */
     public function testBreakEggActionNormalOneMoreChance()
     {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_breakegg');
        $this->assertEquals('/activity/december/break-egg', $url);
        // ajax post
        $user  = LoadGetEggsInfoData::$USERS[1];
        $session  = $container->get('session');
        $session->set('uid' , $user->getId() );
        $session->save();

        $info = LoadGetEggsInfoData::$INFOS[1];

        $data = array('token' =>$info->getToken(), 'eggType'=> 0);
        $client->request('POST', $url, $data, array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200,$client->getResponse()->getStatusCode());

        $this->assertEquals('{"code":0,"data":{"points":0,"is_once_more":true}}', $client->getResponse()->getContent());

        $em = $this->em;
        $user_actual = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=> $user->getId(),'points' => 100 ));
        $this->assertNotNull($user_actual);
        $this->assertNull($em->getRepository('JiliApiBundle:TaskHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));

        $this->assertNull($em->getRepository('JiliApiBundle:PointHistory0'.($user->getId() % 10 ))->findOneBy(array('userId'=> $user->getId())));
        $this->assertNotNull($em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->findOneBy(array('userId'=> $user->getId(),
        'pointsAcquired'=> 0 )));
     }

}
