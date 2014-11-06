<?php
namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\ApiBundle\Utility\FileUtil;

use Symfony\Component\Filesystem\Filesystem;
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

        // add vote_api.text
        $output_filename = $container->getParameter('file_path_wenwen_vote');
        $content = '[{"id":"2434","title":"\u3010\u751f\u6d3b\u3011\u4f60\u77e5\u9053\u54ea\u4e9b\u662f\u4e0d\u80fd\u653e\u7f6e\u5ba4\u5185\u7684\u9c9c\u82b1\u5417\uff1f","start_time":1408892400,"end_time":1409324399,"vote_url":"http:\/\/www.91wenwen.net\/vote\/show\/2434","image_url":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/2\/1\/21db99e8b039f557b0b5bc2464e18852fe252e1b_s.jpg"},{"id":"2435","title":"\u3010\u751f\u6d3b\u3011\u4f60\u4f1a\u6bcf\u5929\u5199\u65e5\u8bb0\u4e48?","start_time":1408978800,"end_time":1409410799,"vote_url":"http:\/\/www.91wenwen.net\/vote\/show\/2435","image_url":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/f\/0\/f0186d0629ea310b14f0280b302ea86d5b27cbc4_s.jpg"},{"id":"2436","title":"\u3010\u751f\u6d3b\u3011\u5173\u4e8e\u87d1\u8782\u4f60\u4e0d\u77e5\u9053\u7684\u7279\u6027\u6709\u54ea\u4e9b\uff1f","start_time":1409065200,"end_time":1409497199,"vote_url":"http:\/\/www.91wenwen.net\/vote\/show\/2436","image_url":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/2\/c\/2c38b21a6349558318dd65fdb4ced08a8db6debd_s.jpg"},{"id":"2437","title":"\u3010\u751f\u6d3b\u3011\u6b8b\u5bb3\u8eab\u4f53\u7684\u5065\u5eb7\u6740\u624b,\u4f60\u77e5\u9053\u54ea\u4e2a?","start_time":1409151600,"end_time":1409583599,"vote_url":"http:\/\/www.91wenwen.net\/vote\/show\/2437","image_url":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/c\/7\/c7399584a285b9ef01ff4ead67c6199a060c196d_s.jpg"},{"id":"2438","title":"\u3010\u751f\u6d3b\u3011\u4f60\u8ba4\u4e3a\u600e\u6837\u624d\u80fd\u6709\u6548\u7684\u6e05\u7406\u9ed1\u5934\uff1f","start_time":1409238000,"end_time":1409669999,"vote_url":"http:\/\/www.91wenwen.net\/vote\/show\/2438","image_url":"http:\/\/d1909s8qem9bat.cloudfront.net\/vote_image\/3\/1\/31e55ffe0c02061ea78f35f176ba3d8263dfd7e8_s.jpg"}]';
        //exec('mkdir -p /data/91jili/logs/wenwen/');
        
        $file_path = dirname($output_filename);
        $fs = new Filesystem();
        if( true !==  $fs->exists($file_path) ) {
            $fs->mkdir($file_path);
        }
        $fh = fopen($output_filename, 'w+');
        fwrite( $fh, $content );
        fclose($fh);

        //get vote data from file
        $votes = json_decode($content, true);
        $wenwen_vote_mark = $container->getParameter('wenwen_vote_mark');
        $votes = FileUtil :: readJosnFile($output_filename);
        $vote = array_pop($votes);
        $vote_url = $vote['vote_url'] . "?" . $wenwen_vote_mark;

        // request the url
        $url = $router->generate('jili_frontend_home_vote', array(), false );
        $this->assertEquals('/home/vote', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        // check the link .
        $link_node = $crawler->filter('div.quickQInfo  a')->eq(0);
        $link = $link_node->link();
        $this->assertEquals($vote['title'], $link_node->text() , 'check the text');
        $this->assertEquals($vote_url, $link->getUri(), 'Check vote uri' );
    }

    
    /**
     * @group issue_505
     */
    public function testIndexActionWithoutSpm()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $router = $container->get('router');
        $logger= $container->get('logger');
    
        $spm = 'baidu_partnerb';

        $url = $router->generate('_homepage');

        $this->assertEquals('/',$url);

        $crawler = $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit landing page with spm ');
        
        $this->assertEmpty(  $container->get('session')->get('source_route'));
    }

    /**
     * @group issue_505
     */
    public function testIndexActionWithSpm()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $router = $container->get('router');
        $logger= $container->get('logger');
    
        $spm = 'baidu_partnerb';

        $url = $router->generate('_homepage');
        $url = $container->get('router')->generate('_homepage', array('spm'=>$spm) , false);
        $this->assertEquals('/?spm=baidu_partnerb', $url);
        
        $crawler = $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit landing page with spm ');

        $session= $container->get('session');
        $this->assertEquals($spm, $session->get('source_route'), 'source_route checking');
    }
}

