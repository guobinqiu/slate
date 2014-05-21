<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebsitesControllerTest extends WebTestCase
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
     * @group cache
     * @group website 
     * @group debug 
     */
    public function testCatCache() 
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;
        // clear cache dir
        $cache_dir =$container->getParameter('cache_data_path');
        echo $cache_dir , PHP_EOL;
        exec('rm -rf '. $cache_dir);

        // get cache file name
        $website_cat_service  = $container->get('website.category_get');
        $website_cat_service->fetch(  );

        $cache = $website_cat_service->getCacheProxy();

        $key = $cache->getKey();
        
        echo 'key:',$key,PHP_EOL;
        $cache_fn =$cache_dir .DIRECTORY_SEPARATOR . $key.'.cached';
        $this->assertFileExists( $cache_fn);

        $data = $cache->get();
        //   clear again
        exec('rm -rf '. $cache_fn);
        $this->assertFileNotExists( $cache_fn);

// request
        $url = $router->generate('jili_emar_websites_shoplist' , array(), true) ;
        echo $url,PHP_EOL;
        $crawler = $client->request('GET', $url  );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        $this->assertFileExists( $cache_fn);
        $this->assertStringEqualsFile(  $cache_fn, serialize($data) );
    }

    /**
     * @group cache
     * @group website 
     * @group debug 
     */
    public function testShopListCache() 
    {
        $client = static::createClient();
        $container = $client->getContainer();
       
        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;

        // clear cache dir
        $cache_dir =$container->getParameter('cache_data_path');
        echo $cache_dir , PHP_EOL;
        exec('rm -rf '. $cache_dir);

        // get cache file name
        $website_list_service  = $container->get('website.list_get');
        $website_list_service->fetch( array() );

        $cache = $website_list_service->getCacheProxy();

        $key = $cache->getKey();
        
        echo 'key:',$key,PHP_EOL;
        $cache_fn =$cache_dir .DIRECTORY_SEPARATOR . $key.'.cached';
        $this->assertFileExists( $cache_fn);

        $data = $cache->get();
        //   clear again
        exec('rm -rf '. $cache_fn);
        $this->assertFileNotExists( $cache_fn);
// again

        // get cache file name
        $website_list_service1  = $container->get('website.list_get');
        $website_list_service1->fetch( array() );

        $cache1 = $website_list_service1->getCacheProxy();

        $key1 = $cache1->getKey();
        
        echo 'key1:',$key1,PHP_EOL;
        $cache_fn1 =$cache_dir .DIRECTORY_SEPARATOR . $key1.'.cached';
        $this->assertFileExists( $cache_fn1);

        $data1 = $cache1->get();
        //   clear again
        exec('rm -rf '. $cache_fn1);
        $this->assertFileNotExists( $cache_fn1);

// request
        $url = $router->generate('jili_emar_websites_shoplist' , array(), true) ;
        echo $url,PHP_EOL;
        $crawler = $client->request('GET', $url  );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        $this->assertFileExists( $cache_fn);

        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

echo 'data:',PHP_EOL;
echo serialize($data),PHP_EOL;
echo 'data1:',PHP_EOL;
echo serialize($data1),PHP_EOL;
        $this->assertStringEqualsFile(  $cache_fn, serialize($data) );


    } 

    /**
     *
     */
    public function testShopListAction()
    {

    }
}
