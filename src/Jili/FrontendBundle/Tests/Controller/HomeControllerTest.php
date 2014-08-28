<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\LoadUserVoteApiCodeData;

class HomeControllerTest extends WebTestCase
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
//        $test_name = $this->getName();
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
     * @group issue_437
     */
    public function testVoteAction() 
    {
        // write testing data 

        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $router = $container->get('router');
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserVoteApiCodeData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        

        // add vote_api.text
        $output_filename = "/data/91jili/logs/wenwen/vote.csv";
        $content = '2437,"【生活】残害身体的健康杀手,你知道哪个?",1409151600,1409583599,http://www.91wenwen.net/vote/show/2437,http://d1909s8qem9bat.cloudfront.net/vote_image/c/7/c7399584a285b9ef01ff4ead67c6199a060c196d_s.jpg';
        exec('mkdir -p /data/91jili/logs/wenwen/'); 
        $fh = fopen($output_filename, 'w+');
        fwrite( $fh, $content );
        fclose($fh);

        $vote = str_getcsv( $content);
        // request the url
        
        $url = $router->generate('jili_frontend_home_vote', array(), false );
        $this->assertEquals('/home/vote', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        // check the link without token. 
        $link_node = $crawler->filter('div.quickQInfo  a')->eq(0);
        $link = $link_node->link();
        $this->assertEquals('【生活】残害身体的健康杀手,你知道哪个?' ,$link_node->text() , 'check the text');
        $this->assertEquals('http://www.91wenwen.net/vote/show/2437?c=91jili', $link->getUri(), 'Check vote uri' );

        // check the link with token. 
        $user = LoadUserVoteApiCodeData::$USER[0];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();


        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        // check the link without token. 
        $link_node = $crawler->filter('div.quickQInfo  a')->eq(0);
        $link = $link_node->link();
        $this->assertEquals('【生活】残害身体的健康杀手,你知道哪个?' ,$link_node->text() , 'check the text');
        $this->assertEquals('http://www.91wenwen.net/vote/show/2437?c=91jili?t=45a53d4a8d954be13cd258578da54cab4730184b', $link->getUri(), 'Check vote uri' );
    }
}
