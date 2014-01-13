<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferwowControllerTest extends WebTestCase
{
   /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

//  memberid  true  您的网站用户的唯一编号，与“步骤3”memberid的对应
//  point true 奖励用户的虚拟货币数量
//  eventid true 回传数据的唯一流水号，合作客户需要记录并且验证唯一性，主要用于结算和对账
//  websiteid true 网站ID 
//  immediate true 0：非即时返利活动,处于待审核状态；
//  1：即时返利活动，需发放奖励给会员；
//  2：非即时返利活动，审核通过，重新回传，发放奖励给会员；
//  3：非即时返利活动，审核不通过，重新回传，不发放奖励；
//  
//  programname false 活动名称 
//  sign  false 网站主与我方约定受权的key，key生成规则由双方讨论决定，网站主或我方对受权的key进行校验，用受权的key对回传参数进行校验；
//  
//  key：为双方约好的key值；
//  加密格式：
//  sign=strtoupper(md5(memberId+point+eventId+websiteId+immediate+key)
//  
//  加密为32位全大写格式
//  注：
//  1、什么情况下重新发送回传数据：
//  （1）HTTP状态非200； ???
//  （2）immediate=0的情况下，才允许接收相同eventid的推送记录，返利 
//       （immediate=2），或不返利（immediate=3）。
//  2、请在给用户发放奖励的位置，通过eventid进行排重处理，即已经给会员发放奖励的eventid不能重复发放，以免因为重复提交请求导致多次发放。 
 
    // validateion test
    public function testGetApwInfo()
    {

        $client = static::createClient();
        $contianer = $client->getContainer();

        $em  = $contianer->get('doctrine.orm.default_entity_manager');
        $logger = $contianer->get('logger');
        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {

#            $this->em->getRepository('JiliApiBundle:TaskHistory'. ( $u) )->
            $em->remove($offer_order);
            $em->flush();
        }

        $params_1 = array('memberid'=>null,'point'=>null,'eventid'=>null,'websiteid'=>null , 'immediate'=>null  );
        $params_2 = array('memberid'=>null,'point'=>null,'eventid'=>null,'websiteid'=>null , 'immediate'=>null ,'programname'=>null );
        $params_3 = array('memberid'=>null,'point'=>null,'eventid'=>null,'websiteid'=>null , 'immediate'=>null ,'programname'=>null , 'sign'=>null);
        $params_4 = array('memberid'=>null,'point'=>null,'eventid'=>null,'websiteid'=>null , 'immediate'=>null , 'sign'=>null);

        /*
         *
         成功回值
    {"memberid":"1001","point":"20","websiteid":"1096","eventid":"asd45sd57s45d45s4d55g45k65ed89rg","immediate":"0","status":"success"}

    失败返回值
    {"memberid":"1001","point":"20","websiteid":"1096","eventid":"asd45sd57s45d45s4d55g45k65ed89rg","immediate":"0","status":"failure","errno":"offerwow-03"}
         */

//1出现空参数
        $crawler = $client->request('GET', '/api/offerwow/getInfo?'. http_build_query($params_1 )  );
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals( '{"memberid":"","point":"","websiteid":"","eventid":"","immediate":"","status":"failure","errno":"offerwow-01"}',$i);

        // 2网站id不存在 
        $params_1 = array('memberid'=>'80','point'=>'20','websiteid'=>'0','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'0');
        $u = '/api/offerwow/getInfo?'. http_build_query($params_1 )  ;
        echo PHP_EOL;
        echo __LINE__,"\t",$u,PHP_EOL;
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $params_1['status'] = 'failure';
        $params_1['errno'] = 'offerwow-02';
        $e = json_encode($params_1);
        $this->assertEquals( $e , $i);
        #         $this->assertEquals( '{"memberid":"80","point":"20","websiteid":"0","eventid":"asd45sd57s45d45s4d55g45k65ed89rg","immediate":"0","status":"failure","errno":"offerwow-02"}',$i);

        // 3uid会员不存在"
        $params_1 = array('memberid'=>'0a','point'=>'20','websiteid'=>'1096','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'0');
        $u = '/api/offerwow/getInfo?'. http_build_query($params_1 )  ;
        echo __LINE__,"\t",$u,PHP_EOL;
        $params_1['status'] = 'failure';
        $params_1['errno'] = 'offerwow-02';
        $e = json_encode($params_1);

        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;

        $this->assertEquals( $e, $i);

// sign=strtoupper(md5(memberId+point+eventId+websiteId+immediate+key)

// a valid memberid is in format of uid_adid
        
        //4 已发放奖励的Eventid重复 with sign
        $key = '91jili2offerwow';
        $params_1 = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'0');
        $params_1['sign'] =strtoupper(md5($params_1['memberid'] . $params_1['point'] .$params_1['eventid'] .$params_1['websiteid'] .$params_1['immediate'] .  $key  )  );

        $u = '/api/offerwow/getInfo?'. http_build_query($params_1 )  ;
        echo __LINE__,"\t",$u,PHP_EOL;

        $params_1['status'] = 'success';
#        $params_1['errno'] = 'signature fail';
        $e = json_encode($params_1);

        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);


        // 4 . duplidated eventid??, no sign
        $params_1 = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'0');
        $u = '/api/offerwow/getInfo?'. http_build_query($params_1 )  ;
        echo __LINE__,"\t",$u,PHP_EOL;
        
        $params_1['status'] = 'success';

        $e = json_encode($params_1);

        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

        // signagure false
    }

    // instant success
    public function testGetApwInfo2() {
        $client = static::createClient();
        $contianer = $client->getContainer();
        $em  = $contianer->get('doctrine.orm.default_entity_manager');

        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {
            $em->remove($offer_order);
            $em->flush();
        }

        // immediate = 1
        $params_1 = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'1');

        $key = '91jili2offerwow';
        $params_1['sign'] =strtoupper(md5($params_1['memberid'].$params_1['point'].$params_1['eventid'].$params_1['websiteid'].$params_1['immediate'].$key));

        $u = '/api/offerwow/getInfo?'. http_build_query($params_1);
        echo __LINE__,"\t",$u,PHP_EOL;

        #$params_1['status'] = 'failure';
        #$params_1['errno'] = 'offerwow-04';
        
        $params_1['status'] = 'success';
        $e = json_encode($params_1);

        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);


        #$offer_order = $this->em->getRepository('JiliApiBundle:')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');

    }

    // pending  success
    public function testGetApwInfo3() {
        $client = static::createClient();
        $contianer = $client->getContainer();
        $em  = $contianer->get('doctrine.orm.default_entity_manager');

        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {
            $em->remove($offer_order);
            $em->flush();
        }


        // immediate = 0
        $key = '91jili2offerwow';
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'0');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);


        // success : immeidate= 2;
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'2');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

        // again
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'2');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'failure';
        $req['errno'] = 'offerwow-04';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);
    }
 
    // pending failure
    public function testGetApwInfo4() {
        $client = static::createClient();
        $contianer = $client->getContainer();
        $em  = $contianer->get('doctrine.orm.default_entity_manager');

        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {
            $em->remove($offer_order);
            $em->flush();
        }

        // immediate = 0
        $req = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'0');
        $key = '91jili2offerwow';
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));

        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

        
        $offer_order2 = $em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');


        $this->assertEquals(2,(int) $offer_order2->getStatus() );

        // pending fail: immeidate= 3;
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'3');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);
        
        $offer_order3 = $this->em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');

        $this->assertEquals(4, (int) $offer_order3->getStatus() );

        // again
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'3');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'failure';
        $req['errno'] = 'offerwow-04';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

    }

    // instant success immediate = 1
    public function testGetApwInfo5() {
        $client = static::createClient();
        $contianer = $client->getContainer();
        $em  = $contianer->get('doctrine.orm.default_entity_manager');

        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {
            $em->remove($offer_order);
            $em->flush();
        }


        // immediate = 1
        $req = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'1');
        $key = '91jili2offerwow';
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));

        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

        
        $offer_order2 = $this->em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        $this->assertEquals(3,(int) $offer_order2->getStatus() );

        // instant success: immeidate= 1;
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'1');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'failure';
        $req['errno'] = 'offerwow-04';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);
        

        $offer_order3 = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        $this->assertEquals(3, (int) $offer_order3->getStatus() );

        // again: duplicated eventid erron
        $req=array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg','immediate'=>'3');
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));
        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'failure';
        $req['errno'] = 'offerwow-04';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

    }
    // instant success immediate = 1
    public function testGetApwInfo5() {
        $client = static::createClient();
        $contianer = $client->getContainer();
        $em  = $contianer->get('doctrine.orm.default_entity_manager');

        $offer_order = $contianer->get('doctrine')->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid('asd45sd57s45d45s4d55g45k65ed89rg');
        if( $offer_order ) {
            $em->remove($offer_order);
            $em->flush();
        }


        // immediate = 1
        $req = array('memberid'=>'1057638','point'=>'20','websiteid'=>'1162','eventid'=>'asd45sd57s45d45s4d55g45k65ed89rg', 'immediate'=>'1');
        $key = '91jili2offerwow';
        $req['sign'] =strtoupper(md5($req['memberid'].$req['point'].$req['eventid'].$req['websiteid'].$req['immediate'].$key));

        $u = '/api/offerwow/getInfo?'. http_build_query($req);
        echo __LINE__,"\t",$u,PHP_EOL;
        $req['status'] = 'success';
        $e = json_encode($req);
        $crawler = $client->request('GET', $u);
        $i = $client->getResponse()->getContent() ;
        $this->assertEquals($e, $i);

    }
}    
