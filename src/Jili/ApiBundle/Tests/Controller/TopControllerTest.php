<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;


class TopControllerTest extends WebTestCase
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
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
       $this->em->close();
    }
        //user_91ww_visit              |
        //| user_advertiserment_visit    |
        //| user_game_visit              |
     /**
     * checkin visit 
     **/
    public function testTaskListCheckinAction()
    {    
        echo 'checkin  visit in task list',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // adv visit: remove records
        $day=date('Y-m-d');
        $records =  $em->getRepository('JiliApiBundle:CheckinUserList')->findBy(array('userId'=>$user->getId()  ,'clickDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();
        $records =  $em->getRepository('JiliApiBundle:CheckinClickList')->findBy(array('userId'=>$user->getId()  ,'clickDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();

        // set session for login
        $session = $client->getContainer()->get('session');
        $session->set('uid', $user->getId());
        $session->save();


        // adv visit:  get partial & check html
        $url_task = $container->get('router')->generate('jili_api_top_task');
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $link_name = '每日签到1米粒';
        $link = $crawler->selectLink($link_name);

//$cn =  get_class($link);
//$cm = get_class_methods($cn);
//echo $cn;
//print_r($cm);
        $this->assertEquals(1, count($link));
        $this->assertEquals($link_name,$link->text());
        $this->assertEquals('signs();',$link->attr('onclick'));

        // adv visit: analouge the event
        $url = $router->generate('jili_api_top_checkin');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $url  );

// echo $client->getResponse()->getContent(),PHP_EOL;
        $links= $crawler->filter('li > a');//->extract(array('_text', 'href'));;
        $count_links = count($links);
        for( $i =0 ; $i< $count_links; $i ++ ) {
            $link = $links->eq($i);
            list($cid, $aid, $points) = sscanf($link->attr('onclick'), "goto(%d,%d,%d);" );

            $ajax_url1 =  $router->generate('_advertiserment_click', array('id'=> $aid)  );
            $client->request('GET', $ajax_url1) ;
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $ajax_url1  );
            $this->assertEquals(1, $client->getResponse()->getContent());

            $ajax_url2 =  $router->generate('_checkin_issetClick', array('cid'=> $cid)  );
            $client->request('GET', $ajax_url2) ;
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $ajax_url2  );
            $this->assertEquals(1, $client->getResponse()->getContent());

            $ajax_url3 =  $router->generate('_checkin_clickInsert', array('aid'=>$aid,'cid'=> $cid)  );
            $client->request('GET', $ajax_url3) ;
            $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $ajax_url3  );
            echo '     checkins redirect to:', $client->getResponse()->getContent(),PHP_EOL;
        }

        // checkin visit: check the result html  
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after '.$url.'visit'  );
        $link = $crawler->selectLink($link_name);
        $this->assertEquals(0, count($link));

        // adv visit: check db status
        $records =  $em->getRepository('JiliApiBundle:CheckinUserList')->findBy(array('userId'=>$user->getId()  ,'clickDate'=> $day));
        $this->assertEquals( $count_links, count($records));
        $records =  $em->getRepository('JiliApiBundle:CheckinClickList')->findBy(array('userId'=>$user->getId()  ,'clickDate'=> $day));
        $this->assertEquals(1, count($records));


    }

     /**
     * 91ww visit 
     **/
    public function testTaskList91wwAction()
    {    
        echo '91ww  visit in task list',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // adv visit: remove records
        $day=date('Ymd');
        $records =  $em->getRepository('JiliApiBundle:UserWenwenVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();

        // set session for login
        $session = $client->getContainer()->get('session');
        $session->set('uid', $user->getId());
        $session->save();


        // adv visit:  get partial & check html
        $url_task = $container->get('router')->generate('jili_api_top_task');
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $link_name = '91问问快速问答1米粒';
        $link = $crawler->selectLink($link_name);

        $this->assertEquals(1, count($link));
        $this->assertEquals($link_name,$link->text());
        $href= $link->attr('href');
        $this->assertEquals('http://www.91wenwen.net/vote/#active',$href);
 
        // adv visit: analouge the event
        $url = $router->generate('_default_wenwenVisit');
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $url  );


        // adv visit: check the result html  
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after '.$url.'visit'  );
        $link = $crawler->selectLink($link_name);
        $this->assertEquals(0, count($link));
        // adv visit: check db status

        $records =  $em->getRepository('JiliApiBundle:UserGameVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        $this->assertEquals(1, count($records));
        //todo:  adv visit: check session  status? how to .
    }   //| user_info_visit    

     /**
     * game visit 
     **/
    public function testTaskListGameAction()
    {    
        echo 'game  visit in task list',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // adv visit: remove records
        $day=date('Ymd');
        $records =  $em->getRepository('JiliApiBundle:UserGameVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();

        // set session for login
        $session = $client->getContainer()->get('session');
        $session->set('uid', $user->getId());
        $session->save();


        // adv visit:  get partial & check html
        $url_task = $container->get('router')->generate('jili_api_top_task');
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $link_name = '小鸡找米最高5888米粒';
        $link = $crawler->selectLink($link_name);

        $this->assertEquals(1, count($link));
        $this->assertEquals($link_name,$link->text());
        $href= $link->attr('href');
        $href_parsed = parse_url($href);
        $url_adv_list = $container->get('router')->generate('_game_index');
        $this->assertEquals($href_parsed['path'],$url_adv_list);
 
        // adv visit: analouge the event
        $url = $router->generate('_default_gameVisit');
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $url  );


        // adv visit: check the result html  
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after '.$url.'visit'  );
        $link = $crawler->selectLink($link_name);
        $this->assertEquals(0, count($link));
        // adv visit: check db status

        $records =  $em->getRepository('JiliApiBundle:UserGameVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        $this->assertEquals(1, count($records));
        //todo:  adv visit: check session  status? how to .
    }   //| user_info_visit    

    /**
     * adv visit , on click  ad offer99.
     **/
    public function testTaskListAdvOffer99Action()
    {    
        echo 'adv visit(advertiserment/list) in task list',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');

        $em = $this->em;
        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // adv visit: remove records
        $day=date('Ymd');
        $records =  $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();

        // set session for login
        $session = $client->getContainer()->get('session');
        $session->set('uid', $user->getId());
        $session->save();


        // adv visit:  get partial & check html
        $url_task = $container->get('router')->generate('jili_api_top_task');
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $link_name = '广告任务墙更新';
        $link = $crawler->selectLink($link_name);

        $this->assertEquals(1, count($link));
        $this->assertEquals($link_name,$link->text());
        $href= $link->attr('href');
        $href_parsed = parse_url($href);
        $url_adv_list = $container->get('router')->generate('_advertiserment_list');
        $this->assertEquals($href_parsed['path'],$url_adv_list);
 
        // adv visit: analouge the event
        $url = $url_adv_list;
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), ' clicked '. $url  );


        // adv visit: check the result html  
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after adv visit'  );
        $link = $crawler->selectLink($link_name);
        $this->assertEquals(0, count($link));
        // adv visit: check db status

        $records =  $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        $this->assertEquals(1, count($records));
        //todo:  adv visit: check session  status? how to .
    }
#        $session_keys =  $container->getParameter('cache_config.session.task_list.keys');
#        $session_key = $session_keys['adv_visit'];
#        $this->assertFalse($session->has($session_key));
#
#        echo $session_key, PHP_EOL;

    /**
     * adv visit , on click  ad list.
     **/
    public function testTaskListAdvListAction()
    {
        $this->markTestIncomplete('Ignored for dev');
        echo 'adv visit in task list',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $kernel = $client->getKernel();
        $em = $this->em;

        //user_91ww_visit              |
        //| user_advertiserment_visit    |
        //| user_game_visit              |
        //| user_info_visit    

        // set session for login
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // adv visit: remove records
        $day=date('Ymd');
        $records =  $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        foreach( $records as $record) {
            $em->remove($record);
        }
        $em->flush();
        $em->clear();
        // set session for login
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();

        $logger= $container->get('logger');

        // adv visit:  get partial & check html
        $url_task = $container->get('router')->generate('jili_api_top_task');
        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $link_name = '广告任务墙更新';
        $link = $crawler->selectLink($link_name);

        $this->assertEquals(1, count($link));
        $this->assertEquals($link_name,$link->text());
        $href= $link->attr('href');
        $href_parsed = parse_url($href);
        $url_adv_list = $container->get('router')->generate('_advertiserment_list');
        $this->assertEquals($href_parsed['path'],$url_adv_list);
 
        // adv visit: analouge the event
        // adv visit: check the result html  
        // adv visit: check db status
        // adv visit: check session  status? how to .
          
        $url = $container->get('router')->generate('_advertiserment_offer99');
        echo $url , PHP_EOL;
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );


        $url = $url_task;
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'after adv visit'  );
        $link = $crawler->selectLink($link_name);
        $this->assertEquals(0, count($link));

    }
#        $this->assertEquals('1', $client->getResponse()->getContent());
#        $this->assertEquals('1', $client->getResponse()->getContent());
#    public function testFastLoginAction()
#    {
#        $client = static::createClient();
#        $container = $client->getContainer();
#        $logger= $container->get('logger');
#
#        $query = array('email'=> 'alice.nima@gmail.com', 'pwd'=>'aaaaaa' );
#        $url = $container->get('router')->generate('_default_fastLogin', $query ) ;
#        // $crawler = $client->request('GET', '/hello/Fabien');
#        echo $url, PHP_EOL;
#
#        $client->request('POST', $url ) ;
#        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
#        $this->assertEquals('1', $client->getResponse()->getContent());
#
#        $this->assertEquals('0', '0');
#        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
#    }
}
#        $info = $crawler->filter('li')->extract(array('_text', 'href'));;
#        var_dump($info);

#        $data = $crawler->each(function ($node, $i) {
#            return $node->attr('href');
#        });
#        var_dump($data);
// login post
#        $url = $container->get('router')->generate('_login');
##$url = 'http://localhost/login';
#        $client->request('GET', $url ) ;
#        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
#        $html = $client->getResponse()->getContent();
#        $crawler = new Crawler($html, $url);
##
##echo $url, PHP_EOL;
#        $this->markTestIncomplete('Ignored for dev');
#        $form = $crawler->selectButton('form1')->form();
#        $form['email'] = $query['email'];
#        $form['pwd'] = 'cccccc';
#        $client->submit($form);
#        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
