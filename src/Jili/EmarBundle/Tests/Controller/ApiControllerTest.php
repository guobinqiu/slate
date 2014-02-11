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
            'fead_back'=>'1057639a11'
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


        $fields_required = array('unique_id', 'action_id', 'sid', 'wid', 'order_no', 'order_time', 'prod_count', 'prod_money', 'comm_type', 'commision', 'status', 'am', 'chkcode', 'fead_back');
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

    public function testCallback()
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
            'sid'=>$sid, //  91jili.com 
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
            'fead_back'=>'1057639a11'
        );

        echo $url,PHP_EOL;
        $params['chkcode'] = $this->calcSignature($params);
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());


        echo $url,PHP_EOL;
        $crawler = $client->request('GET',$url, $params) ;

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('0', $client->getResponse()->getContent());

        // db checking...
        //todo: $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment') -> findOneBy(); 

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
