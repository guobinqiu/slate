<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadTopCallboardCodeData;
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

        // request
        $url = $router->generate('jili_api_top_callboard');
        //echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

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

    }
}
