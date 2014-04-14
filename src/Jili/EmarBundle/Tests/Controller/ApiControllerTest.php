<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
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
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->em  = $em;
        $this->updateUniqueId();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function HoldTestCallbackValidation() 
    {

        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $em = $this->em;

        $sid = 458631;
        $wid = 708089;
        $ad_id = 83;
        $action_id =  6941;
        $unique_id = $this->unique_id; 

        // 1.0 login for session checking.
        $email = 'alice.nima@gmail.com';
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        $session = $container->get('session');
        $session->set('uid', $user->getId()  );
        $session->save();


        // 1.1 trigger the advertiserment click event.
        $url = $container->get('router')->generate('_advertiserment_click', array('id'=> $ad_id)  ) ;
        echo $url,PHP_EOL;

        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // 1.2 analogue the 1st callback.
        $url = $container->get('router')->generate('jili_emar_api_callback' ) ;
        echo $url,PHP_EOL;

        $params = array(
            'unique_id'=>$unique_id,
            'action_id'=>$action_id,
            'prod_type'=>'yhq',
            'create_date'=>'2011-09-19+18%3A21%3A18',
            'action_name'=>'DangdangCPS%BE%A9%B6%ABCPS',
            'sid'=>$sid, //$sid, //  91jili.com 
            'wid'=>$wid, //  account id on yiqifa.com 
            'order_no'=>'A19182109822_1',
            'order_time'=>'2011-09-19+18%3A21%3A09',
            'prod_id'=>'21000043',
            'prod_name'=>'abc',
            'prod_count'=>'1',
            'prod_money'=>'126.0',
            'feed_back'=>'1253',
            'status'=>'R',
            'comm_type'=>'yhq',
            'commision'=>'5.04', // for 91jili.com website.
            'am'=>'123',
            #            'chkcode'=>'c1beb34c3ba2735250894f7314bcc642',
            'feed_back'=>$user->getId()
        );

        $tmp = $params['sid'] ;
        $params['sid'] = 10;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('2', $client->getResponse()->getContent());
        $params['sid'] = $tmp;

        // again
        echo $url,PHP_EOL;
        $tmp = $params['wid'];
        $params['wid'] = 11;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('2', $client->getResponse()->getContent());
        $params['wid'] = $tmp;


        $fields_required = array('unique_id', 'action_id', 'sid', 'wid', 'order_no', 'order_time', 'prod_count', 'prod_money', 'comm_type', 'commision', 'status', 'am', 'chkcode', 'feed_back');
        foreach($fields_required as $f) {
            $tmp = $params[$f];
            $params[$f] = '';
            echo $url,': set empty to param ', $f,PHP_EOL;

            if( $f != 'chkcode' ) {
                $params['chkcode'] = $this->calcSignature($params);
            }
            $crawler = $client->request('GET',$url, $params) ;

            $this->assertEquals(200, $client->getResponse()->getStatusCode() );
            $this->assertEquals('2', $client->getResponse()->getContent());

            $params[$f] = '';
            echo $url,': set null to param ', $f,PHP_EOL;

            if( $f != 'chkcode' ) {
                $params['chkcode'] = $this->calcSignature($params);
            }
            $crawler = $client->request('GET',$url, $params) ;
            $this->assertEquals(200, $client->getResponse()->getStatusCode() );
            $this->assertEquals('2', $client->getResponse()->getContent());

            $params[$f] = $tmp;
        }

        // error action id 
        $f = 'action_id';
        $tmp = $params[$f];
        $params[$f] = '23';
        echo $url,': set 23 to param ', $f,PHP_EOL;

        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('2', $client->getResponse()->getContent());
        $params[$f] = $tmp;

    }

    /*
     *
     * callback逻辑测试
     */
    public function holdTestCallback()
    {

        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $em = $this->em;

        $sid = 458631;
        $wid = 708089;
        $ad_id = 83;
        $action_id =  6941;
        $unique_id = $this->unique_id; 

        // 1.0 login for session checking.
        $email = 'alice.nima@gmail.com';
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        $session = $container->get('session');
        $session->set('uid', $user->getId()  );
        $session->save();


        // 1.1 trigger the advertiserment click event.
        $url = $container->get('router')->generate('_advertiserment_click', array('id'=> $ad_id)  ) ;
        echo $url,PHP_EOL;

        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // 1.2 analogue the 1st callback.
        $url = $container->get('router')->generate('jili_emar_api_callback' ) ;
        echo $url,PHP_EOL;

        $params = array(
            'unique_id'=>$unique_id,
            'create_date' =>'2014-02-12+14%3A54%3A57',
            'action_id'=>$action_id,
            'action_name'=>'DangdangCPS%BE%A9%B6%ABCPS',
            'sid'=>$sid, //  91jili.com 
            'wid'=>$wid, //  account id on yiqifa.com 
            'order_no'=>'A19182109822_1',
            'order_time'=>'2011-09-19+18%3A21%3A09',
            'prod_id'=>'',
            'prod_name'=>'',
            'prod_count'=>'1',
            'prod_money'=>126.0,
            'feed_back'=>$user->getId(),
            'status'=>'R',
            'comm_type'=>0,
            'commision'=>0.0, // for 91jili.com website.
            #'chkcode'=>'c1beb34c3ba2735250894f7314bcc642',
            'prod_type'=>0,
            'am'=>0.0,
        );

        echo $url,PHP_EOL;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());


        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 
        
        echo $url,PHP_EOL;
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        // status validate
        echo $url,PHP_EOL;
        $params['status'] = 'A';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 

        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());


        echo $url,PHP_EOL;
        $params['status'] = 'R';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        $url_user_adtaste =  $container->get('router')->generate('_user_adtaste' , array('type'=> 0) ) ;

        $url_user_info =  $container->get('router')->generate('_user_info' ) ;

    }

    /**
     * for debug
     */
    public function testCallback()
    {
        $qs = '/jili-jiang/web/emar/api/callback?unique_id=449649582&create_date=2014-02-12+16%3A11%3A01&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=708089&order_no=1062604092&order_time=2014-02-12+16%3A03%3A07&prod_id=&prod_name=&prod_count=1&prod_money=8888.0&feed_back=1094007&status=R&comm_type=0&commision=26.0&chkcode=9b4aba2c618a12bd6395e6a493041d53&prod_type=0&am=0.0&exchange_rate=0.0';

//        $qs = '/jili-jiang/web/emar/api/callback?unique_id=449649581&create_date=2014-02-12+16%3A11%3A01&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=708089&order_no=1062604080&order_time=2014-02-12+16%3A03%3A07&prod_id=&prod_name=&prod_count=1&prod_money=8947.0&feed_back=&status=R&comm_type=0&commision=26.0&chkcode=fe72a333e3956bacbcfc549ed85fe67e&prod_type=0&am=0.0&exchange_rate=0.0';


        $b = parse_url($qs);
        parse_str($b['query']);
        $params = compact( 'unique_id', 'create_date', 'action_id','action_name', 'sid','wid', 'order_no','order_time','prod_id','prod_name','prod_count','prod_money','feed_back','status','comm_type','commision','chkcode','prod_type','am','exchange_rate');

        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $em = $this->em;

        $sid = 458631;
        $wid = 708089;
        $ad_id = 83;
        $action_id =  6941;
        $unique_id = $this->unique_id; 

        // 1.0 login for session checking.
        $email = 'alice.nima@gmail.com';
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        $session = $container->get('session');
        $session->set('uid', $user->getId()  );
        $session->save();


        // 1.1 trigger the advertiserment click event.
        $url = $container->get('router')->generate('_advertiserment_click', array('id'=> $ad_id)  ) ;
        echo $url,PHP_EOL;

        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // 1.2 analogue the 1st callback.
        $url = $container->get('router')->generate('jili_emar_api_callback' ) ;
        echo $url,PHP_EOL;

        $params['unique_id']=$unique_id;
        $params['action_id']=$action_id;
       # $params['sid']=$sid; //  91jili.com 
       # $params['wid']=$wid; //  account id on yiqifa.com 
        $params['feed_back']=$user->getId();

        echo $url,PHP_EOL;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());


        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 
        
        echo $url,PHP_EOL;
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        // status validate
        echo $url,PHP_EOL;
        $params['status'] = 'A';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 

        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());


        echo $url,PHP_EOL;
        $params['status'] = 'R';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        $url_user_adtaste =  $container->get('router')->generate('_user_adtaste' , array('type'=> 0) ) ;
        $url_user_info =  $container->get('router')->generate('_user_info' ) ;


       // sub order 
        $qs = '/jili-jiang/web/emar/api/callback?unique_id=449650613&create_date=2014-02-12+16%3A12%3A23&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=708089&order_no=1062647585&order_time=2014-02-12+16%3A03%3A07&prod_id=&prod_name=&prod_count=1&prod_money=59.0&feed_back=1094007&status=R&comm_type=0&commision=0.0&chkcode=c55e0886bab540be1b428718ddd5904a&prod_type=0&am=0.0&exchange_rate=0.0';
        $b = parse_url($qs);
        parse_str($b['query']);
        $params = compact( 'unique_id', 'create_date', 'action_id','action_name', 'sid','wid', 'order_no','order_time','prod_id','prod_name','prod_count','prod_money','feed_back','status','comm_type','commision','chkcode','prod_type','am','exchange_rate');
        $sid = 458631;
        $wid = 708089;
        $ad_id = 83;
        $action_id =  6941;
        $unique_id = $this->updateUniqueId() ; 

        $params['unique_id']=$unique_id;
        $params['action_id']=$action_id;
       # $params['sid']=$sid; //  91jili.com 
       # $params['wid']=$wid; //  account id on yiqifa.com 
        $params['feed_back']=$user->getId();

        echo $url,PHP_EOL;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());


        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 
        
        echo $url,PHP_EOL;
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        // status validate
        echo $url,PHP_EOL;
        $params['status'] = 'A';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 

        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());


        echo $url,PHP_EOL;
        $params['status'] = 'R';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

    }
    //
    public function testNoAdvertiserment()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $em = $this->em;

       // no in advertiserment. 
        $qs = '/jili-jiang/web/emar/api/callback?unique_id=449650613&create_date=2014-02-12+16%3A12%3A23&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=708089&order_no=1062647585&order_time=2014-02-12+16%3A03%3A07&prod_id=&prod_name=&prod_count=1&prod_money=59.0&feed_back=1094007&status=R&comm_type=0&commision=0.0&chkcode=c55e0886bab540be1b428718ddd5904a&prod_type=0&am=0.0&exchange_rate=0.0';
        $b = parse_url($qs);
        parse_str($b['query']);
        $params = compact( 'unique_id', 'create_date', 'action_id','action_name', 'sid','wid', 'order_no','order_time','prod_id','prod_name','prod_count','prod_money','feed_back','status','comm_type','commision','chkcode','prod_type','am','exchange_rate');


        $sid = 458631;
        $wid = 708089;
        $action_id =  4330;

        $unique_id = $this->updateUniqueId() ; 
        // 1.0 login for session checking.
        $email = 'alice.nima@gmail.com';
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        $session = $container->get('session');
        $session->set('uid', $user->getId()  );
        $session->save();

        $params['unique_id']=$unique_id;
        $params['action_id']=$action_id;
       # $params['sid']=$sid; //  91jili.com 
       # $params['wid']=$wid; //  account id on yiqifa.com 
        $params['feed_back']=$user->getId();

        $url = $container->get('router')->generate('jili_emar_api_callback' ) ;
        echo $url,PHP_EOL;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());


        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 
        
        echo $url,PHP_EOL;
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        // status validate
        echo $url,PHP_EOL;
        $params['status'] = 'A';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());

        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 

        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());


        echo $url,PHP_EOL;
        $params['status'] = 'R';
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

    }
    

    private function calcSignature( $params) {
        $DataSecret = static::$kernel->getContainer()->getParameter('emar_com.91jili_com.key');
        $str = $params['action_id'].$params['order_no'].$params['prod_money'].$params['order_time'].$DataSecret ;
        return  md5( $str);
    }


    private function updateUniqueId() {
        $em = $this->em;
        $i = 0;
        do{
            $unique_id = mt_rand( 10000000,99999999);
            $o = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneByOcd($unique_id ) ;
            $i++;
        } while( $i < 5 && empty( $o) );

        if( ! empty( $o)  ) {
            echo 'No unique id generated!!';
            exit;
        }

        $this->unique_id = $unique_id;
        return $unique_id;
    }
}
# //http://domain/getyiqifa?
#unique_id=68383916
#action_id=6941
#prod_type=yhq
#create_date=2011-09-19+18%3A21%3A18
#action_name=DangdangCPS
#sid=622
#wid=368482
#order_no=A19182109822_1
#order_time=2011-09-19+18%3A21%3A09
#prod_id=21000043
#prod_name=abc
#prod_count=1
#prod_money=126.0
#feed_back=1253
#status=R
#comm_type=yhq
#commision=5.04
#am=123
#chkcode=c1beb34c3ba2735250894f7314bcc642
#
# //http://domain/getyiqifa?unique_id=68383916&action_id=6941&prod_type=yhq&create_date=2011-09-19+18%3A21%3A18&action_name=DangdangCPS&sid=622&wid=368482&order_no=A19182109822_1&order_time=2011-09-19+18%3A21%3A09&prod_id=21000043&prod_name=abc&prod_count=1&prod_money=126.0&feed_back=1253&status=R&comm_type=yhq&commision=5.04&am=123&chkcode=c1beb34c3ba2735250894f7314bcc642
#
#//http://fanlio.com/getdata/yiqifa
#//http://fanlio.com/getdata/yiqifa?unique_id=25474435&create_date=2010-10-27+17%3A01%3A59&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=36231&wid=161189&order_no=27463290&order_time=2010-10-27+17%3A00%3A41&prod_id=&prod_name=&prod_count=1&prod_money=6999.0&feed_back=8&status=R&comm_type=basic&commision=25.0&chkcode=2069642738cdfe86e3175c7fb7ad9bdf&prod_type=&exchange_rate=0.0

#unique_id=25474435
#create_date=2010-10-27+17%3A01%3A59
#action_id=254
#action_name=%BE%A9%B6%ABCPS
#sid=36231
#wid=161189
#order_no=27463290
#order_time=2010-10-27+17%3A00%3A41
#prod_id=
#prod_name=
#prod_count=1
#prod_money=6999.0
#feed_back=8
#status=R
#comm_type=basic
#commision=25.0
#chkcode=2069642738cdfe86e3175c7fb7ad9bdf
#prod_type=
#exchange_rate=0.0

#//http://www.dieke.cn/yiqifa_interface.php
#//http://www.dieke.cn/yiqifa_interface.php?unique_id=24653428&create_date=2010-10-18+14%3A32%3A55&action_id=247&action_name=%B5%B1%B5%B1%CD%F8CPS&sid=55380&wid=162702&order_no=3149020315&order_time=2010-10-18+14%3A31%3A44&prod_id=&prod_name=&prod_count=1&prod_money=158.0&feed_back=54321&status=R&comm_type=%B0%D9%BB%F5&commision=2.0&chkcode=16e6911677c57e9a2dceeee93a087336&prod_type=%B0%D9%BB%F5&exchange_rate=0.0
#unique_id=24653428
#create_date=2010-10-18+14%3A32%3A55
#action_id=247
#action_name=%B5%B1%B5%B1%CD%F8CPS
#sid=55380
#wid=162702
#order_no=3149020315
#order_time=2010-10-18+14%3A31%3A44
#prod_id=
#prod_name=
#prod_count=1
#prod_money=158.0
#feed_back=54321
#status=R
#comm_type=%B0%D9%BB%F5
#commision=2.0
#chkcode=16e6911677c57e9a2dceeee93a087336
#prod_type=%B0%D9%BB%F5
#exchange_rate=0.0

#192.168.1.1 - - [12/Feb/2014:14:55:28 +0800] "GET /jili-jiang/web/emar/api/callback?unique_id=449623860&create_date=2014-02-12+14%3A54%3A57&action_id=254&action_name=%BE%A9%B6%ABCPS&sid=458631&wid=708089&order_no=1060978034&order_time=2014-02-12+14%3A47%3A33&prod_id=&prod_name=&prod_count=1&prod_money=12.0&feed_back=1094007&status=R&comm_type=0&commision=0.0&chkcode=587e47d2ddabb22663e0d44adbc35854&prod_type=0&am=0.0&exchange_rate=0.0 HTTP/1.1" 200 1 "-" "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) )"

// unique_id=449623860
// create_date=2014-02-12+14%3A54%3A57
// action_id=254
// action_name=%BE%A9%B6%ABCPS
// sid=458631
// wid=708089
// order_no=1060978034
// order_time=2014-02-12+14%3A47%3A33
// prod_id=
// prod_name=
// prod_count=1
// prod_money=12.0
// feed_back=1094007
// status=R
// comm_type=0
// commision=0.0
// chkcode=587e47d2ddabb22663e0d44adbc35854
// prod_type=0
// am=0.0
// exchange_rate=0.0 
