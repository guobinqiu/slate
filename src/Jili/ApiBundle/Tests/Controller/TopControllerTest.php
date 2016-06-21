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
