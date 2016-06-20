<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\Cookie;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadAdvertisermentMarketActivityData;

use Jili\ApiBundle\DataFixtures\ORM\LoadTopCallboardCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadCookieLoginHomepageCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoTaskHistoryData;

class TopControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $container;

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
        $container =  static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserInfoCodeData();
        $fixture->setContainer($container);

        $fixture1 = new LoadUserInfoTaskHistoryData();
        $fixture1->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $loader->addFixture($fixture1);

        $tn = $this->getName();
        if ($tn == 'testCallboardAction') {
            $fixture = new LoadTopCallboardCodeData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);
        }
        $executor->execute($loader->getFixtures());

        $this->container = $container;
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
     * @group session
     */
    public function testUserInfoAction()
    {
        //echo 'top.userInfo testing',PHP_EOL;

        $client = static::createClient();
        $container = $client->getContainer();

        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;


        // request
        $url = $router->generate('jili_api_top_userinfo');
        $this->assertStringEndsWith('/top/userInfo', $url);

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        // check the partial
        $this->assertContains('/images/headPortBg.jpg', $crawler->filter('img')->attr('src'));

        
       // var_dump($crawler->filter('dd')->eq(0)->text());
       // echo  $crawler->filter('dd')->eq(1)->text(),PHP_EOL;

        $query = array('email'=> 'alice.nima@gmail.com');
        $user = LoadUserInfoCodeData::$USERS[0];


//        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
 //       if(! $user) {
 //           echo 'bad email:',$query['email'], PHP_EOL;
 //           return false;
 //       }

        // post to login , for sessions:
        $url = $container->get('router')->generate('_login', array(), true);
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('submit_button')->form();

        $form['email'] = $query['email'];
        $form['pwd'] = 'aaaaaa';
        $client->submit($form);

        $this->assertEquals(301, $client->getResponse()->getStatusCode() );

        $session = $container->get('session');
        $this->assertTrue( $session->has('uid'));
#        $keys = $container->getParameter('cache_config.session.points.keys');

        // request
        $url = $router->generate('jili_api_top_userinfo');
        $this->assertEquals('/top/userInfo', $url);
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(301, $client->getResponse()->getStatusCode() );

        $crawler=$client->followRedirect();
        $this->assertTrue( $session->has('uid'));
        $this->assertEquals( $user->getId(), $session->get('uid'));
 //       echo $client->getResponse()->getContent(),PHP_EOL;
        // check the partial
        $this->assertContains('/images/headPortBg.jpg', $crawler->filter('img')->attr('src'));

        $this->assertEquals( '当前米粒数',$crawler->filter('li')->eq(0)->text() , $user->getPoints() .' should be render' );
        $this->assertEquals( '500确认中米粒数', $crawler->filter('li')->eq(1)->text() );

        $session->set('uid', $user->getId());
        $session->set('points', $user->getPoints());
        $session->set('icon_path', $user->getIconPath());
        $session->save();

        $url = $router->generate('jili_api_top_userinfo');
        $this->assertEquals('/top/userInfo', $url);
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
//        echo $client->getResponse()->getContent(),PHP_EOL;

        $this->assertContains('/uploads/user/5/1392030971_6586.jpeg', $crawler->filter('img')->attr('src'));

//        $task =  $em->getRepository('JiliApiBundle:TaskHistory0'. ( $user->getId() % 10 ) );
//        $confirmPoints = $task->getConfirmPoints($user->getId());
//        if(!$confirmPoints){
//            $confirmPoints = 0;
//        }
//       echo $session->get('user.points.confirmming'),PHP_EOL; 

        $this->assertEquals('500确认中米粒数', $crawler->filter('li')->eq(1)->text() );
        $this->assertEquals( '89当前米粒数',$crawler->filter('li')->eq(0)->text() , $user->getPoints() .' should be render' );
    }
    /**
     * @group cache
     */
    public function testCallboardAction()
    {
        //echo 'callboard partial ',PHP_EOL;
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
        //echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertFileExists($fn);

        $exp_total = 4 ;//count($callboard);

        // the count
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboardLimit($exp_total);

//        echo '----start----';
//        echo $client->getResponse()->getContent();
//        echo '----end----';
        $ul1 = $crawler->filter('ul')->eq(0)->children('li');
        $this->assertEquals( 4, $ul1->count() );

        $hrefs =array();
        foreach ($callboard as $key) {
            $exp_links[] = array('name'=> mb_substr($key['title'] ,0,17,'utf8'),
                'href'=> $router->generate('_callboard_info', array('id'=> $key['id']) )
            );
        }

        $li = $crawler->filter('li');
        $this->assertEquals( $exp_total, $li->count() );
        for($i = 0; $i < $exp_total; $i++ ) {
            $this->assertEquals($exp_links[$i]['name'] , $li->eq($i)->text());
            $this->assertEquals($exp_links[$i]['href'], $li->eq($i)->filter('a')->eq(0)->attr('href'));
        }

        // check the cache contents.
        $this->assertFileExists($fn);


        //$this->assertStringEqualsFile($fn, serialize($callboard) ,' the content in file ' .$fn);
        exec('rm ' .$fn);
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
