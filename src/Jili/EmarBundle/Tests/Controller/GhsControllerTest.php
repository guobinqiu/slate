<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GhsControllerTest extends WebTestCase
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
     * @group ghs
     * @group cache 
     */
    public function testPromotionAction() {
        $client = static::createClient();
        $container = $client->getContainer();
       
        $logger= $container->get('logger');
        $router = $container->get('router');
        $session=$container->get('session');
        $em = $this->em;

        // rm the cached files, cache_data
        $cache_dir =$container->getParameter('cache_data_path');
        echo $cache_dir , PHP_EOL;
        exec('rm -rf '. $cache_dir);

        $api_name = 'ghs.list_get';
        $queries = array('tmpl'=> 'top','max'=> '9','p'=>'3' ); # must be number in string type


        /// check the cached file. 
        $listRequest = $container->get($api_name);
        $listRequest->setPageSize($queries['max'] );
        $params = array('page_no' =>  $queries['p']);
        $list = $listRequest->setApp('search')->fetchDistinct( $params );

        $cache = $listRequest->getCacheProxy();

        $key = $cache->getKey();
        
        echo 'key:',$key,PHP_EOL;
        $cache_fn =$cache_dir .DIRECTORY_SEPARATOR . $key.'.cached';

        echo $cache_fn,PHP_EOL;
        $this->assertFileExists($cache_fn);

        echo 'rm ',$cache_fn,PHP_EOL;
        exec('rm -rf '. $cache_fn);
        $this->assertFileNotExists($cache_fn);

        // request 
        $url = $router->generate('jili_emar_ghs_promotion',$queries , true);
        echo $url, PHP_EOL;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $last_page_session_key = $api_name.'.'.$queries['tmpl'].'.fetched';
        $this->assertTrue( $session->has($last_page_session_key));

        $last_page = $session->get($last_page_session_key);
        echo 'last_page', $last_page,PHP_EOL;

        echo 'Exits after request',$cache_fn,PHP_EOL;
        $this->assertFileExists($cache_fn);

#        $this->assertFileEquals( serialize( $) ,$cache_fn);


    }
}
