<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Cookie;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadAdvertisermentMarketActivityData;


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
    /**
     * @group user
     * @group cookie
     * @group login
     */
    public function testCookieLoginHomepage()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');

        $em = $this->em;
        $query = array('email'=> 'chiangtor@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        $url_homepage= $router->generate('_homepage' , array(), true);
        echo $url_homepage ,PHP_EOL;

        $secret =  $container->getParameter('secret');

        $token = $this->buildToken(array('email'=> $query['email'], 'pwd'=> 'aaaaaa') , $secret );
        $cookie = new Cookie('jili_rememberme', $token, time() + 3600 * 24 * 365, '/', null, false, false);
        $client->getCookieJar()->set($cookie);

        //$cookie = new Cookie('jili_rememberme', $user->getId(), time() + 3600 * 24 * 365, '/', null, false, false);
       // $client->getCookieJar()->set($cookie);

       // $cookie = new Cookie('jili_nick', $user->getNick(), time() + 3600 * 24 * 365, '/', null, false, false);
       // $client->getCookieJar()->set($cookie);

        $crawler = $client->request('GET', $url_homepage);

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));
        $this->assertEquals( $user->getId() , $session->get('uid'));
        $this->assertTrue( $session->has('nick'));
        $this->assertEquals( $user->getNick() , $session->get('nick'));

        $this->assertTrue( $session->has('points'));
        $this->assertEquals( $user->getPoints() , $session->get('points'));

        $url = $router->generate('jili_api_top_userinfo');
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        // check the partial
        if( $user->getIconPath()) {
            $this->assertEquals('/uploads/user/38/1388737850_5011.jpeg', $crawler->filter('img')->attr('src'));
        } else {
            $this->assertEquals('/other140307/defaultFace.jpg', $crawler->filter('img')->attr('src'));
        }

        $this->assertEquals($user->getPoints() .'当前米粒数',$crawler->filter('dt')->eq(0)->text() );

        $task =  $em->getRepository('JiliApiBundle:TaskHistory0'. ( $user->getId() % 10 ) );
        $confirmPoints = $task->getConfirmPoints($user->getId());
        if(!$confirmPoints){
            $confirmPoints = 0;
        }

        $this->assertEquals($confirmPoints .'确认中米粒数',$crawler->filter('dt')->eq(1)->text() );
    }
    /**
     * @group session
     */
    public function testUserInfoAction()
    {
        echo 'top.userInfo testing',PHP_EOL;

        $client = static::createClient();
        $container = $client->getContainer();

        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;


        // request
        $url = $router->generate('jili_api_top_userinfo');
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        // check the partial
        $this->assertEquals('/other140307/defaultFace.jpg',       $crawler->filter('img')->attr('src'));

        var_dump($crawler->filter('dd')->eq(0)->text());
        echo  $crawler->filter('dd')->eq(1)->text(),PHP_EOL;

        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        // post to login , for sessions:
        $url = $container->get('router')->generate('_login', array(), true);
##$url = 'http://localhost/login';
        echo __LINE__,' ',$url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('loginSubmit')->form();

        $form['email'] = $query['email'];
        $form['pwd'] = 'aaaaaa';
        $client->submit($form);

        $this->assertEquals(301, $client->getResponse()->getStatusCode() );

        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));

        // set session for login
//        $session->set('uid', $user->getId());
//        $session->save();

#
#        $keys = $container->getParameter('cache_config.session.points.keys');

        // request
        $url = $router->generate('jili_api_top_userinfo');
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(301, $client->getResponse()->getStatusCode() );

         $crawler=$client->followRedirect();
//        echo $client->getResponse()->getContent(),PHP_EOL;

        // check the partial
        // /other140307/defaultFace.jpg
        $this->assertEquals('/other140307/defaultFace.jpg', $crawler->filter('img')->attr('src'));

        var_dump($crawler->filter('dd')->eq(0)->text());
        echo $crawler->filter('dd')->eq(1)->text(),PHP_EOL;
    }
    /**
     * @group cache
     */
    public function testCallboardAction()
    {
        echo 'callboard partial ',PHP_EOL;
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;
        $cache_fn= $container->getParameter('cache_config.api.top_callboard.key');
        // remove the cache file
        $cache_data_path = $container->getParameter('cache_data_path');
        $fn = $cache_data_path.DIRECTORY_SEPARATOR.$cache_fn.'.cached';

        exec('rm -f ' .$fn);
        $this->assertFileNotExists($fn);

        // request
        $url = $router->generate('jili_api_top_callboard');
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $this->assertFileExists($fn);

        // the count
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboardLimit(6);

        $exp_total = count($callboard);
        if( $exp_total >= 3 ) {
            $exp_ul1_count = 3;
            $exp_ul2_count = $exp_total -  3;
        } else {
            $exp_ul1_count = $exp_total;
            $exp_ul1_count = 0;
        }

        $ul1 = $crawler->filter('ul')->eq(0)->children('li');
        $ul2 = $crawler->filter('ul')->eq(1)->children('li');
        $this->assertEquals( $exp_ul1_count, $ul1->count() );
        if( $exp_ul2_count > 0 ) {
            $this->assertEquals( $exp_ul2_count, $ul2->count() );
        }

        if( $exp_total > 0 ) {
            $hrefs =array();
            foreach ($callboard as $key) {
                $exp_links[] = array('name'=> '【'.$key['categoryName'].'】'. mb_substr($key['title'] ,0,17,'utf8'),
                    'href'=> $router->generate('_callboard_info', array('id'=> $key['id']) )
                );
            }
            $li = $crawler->filter('li');
            $this->assertEquals( $exp_total, $li->count() );
            for($i = 0; $i < $exp_total; $i++ ) {
                $this->assertEquals($exp_links[$i]['name'] , $li->eq($i)->text());
                $this->assertStringEndsWith($exp_links[$i]['href'], $li->eq($i)->children('a')->eq(0)->attr('href'));
            }
        }
        // check the cache contents.
        $this->assertFileExists($fn);
        $this->assertStringEqualsFile($fn, serialize($callboard) ,' the content in file ' .$fn);
        exec('rm ' .$fn);
    }
        //user_91ww_visit              |
        //| user_advertiserment_visit    |
        //| user_game_visit              |
    /**
     * checkin visit
     * @group session
     * @group task_list
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
     * @group session
     * @group task_list
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

        $records =  $em->getRepository('JiliApiBundle:UserWenwenVisit')->findBy(array('userid'=>$user->getId()  ,'visitDate'=> $day));
        $this->assertEquals(1, count($records));
        //todo:  adv visit: check session  status? how to .
    }   //| user_info_visit

    /**
     * game visit
     * @group session
     * @group task_list
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
        $url_adv_list = $container->get('router')->generate('_game_chick');
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
     * @group session
     * @group task_list
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
     * @group session
     * @group task_list
     **/
    public function testTaskListAdvListAction()
    {
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
    private function buildToken($user , $secret)
    {
        $token = implode('|',$user) .$secret;//.$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }

    /**
     * @group market
     * @group issue_476
     **/
    public function testMarketAction()
    {
        $client = static::createClient();
        $container = static :: $kernel->getContainer();
        $em = $this->em;

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadAdvertisermentMarketActivityData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        //测试数据中，商家活动说明
        $desc =  LoadAdvertisermentMarketActivityData::$MARKET_ACTIVITY->getActivityDescription();

        $crawler = $client->request('GET', '/top/market');
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertTrue($crawler->filter('html:contains("'.$desc.'")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("最高返")')->count() > 0);
    }

}