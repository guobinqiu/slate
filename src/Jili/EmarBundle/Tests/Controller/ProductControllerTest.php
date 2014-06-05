<?php

namespace Jili\EmarBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
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
     * check the product retrieve {(x,y)|x in catid , y in web id }
     * @group cache
     * @group emar 
     */
    public function testRetrievePdtListAction()
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
        
        // It is waste time to test everything //
        $url =  'http://jili0129.vgc.net/app_dev.php/emar/product/retrieve?q=&cat=101010000&p=1&pr=&w=1148';
        parse_str( parse_url($url, PHP_URL_QUERY), $queries);
        //get cache file: value

        $params = $this->buildParamFromQuery($queries); 
        $productRequest = $container->get('product.list_get');
        $productRequest->fetch( $params);

        $total = $productRequest->getTotal();
        $cache =  $productRequest->getCacheProxy();
        $value0 = $cache->get();
        $key = $cache->getKey();

        echo 'key:',$key,PHP_EOL;
        $cache_fn0 =$cache_dir .DIRECTORY_SEPARATOR . $key.'.cached';
        
        $this->assertFileExists($cache_fn0);
        exec('rm -rf '. $cache_fn0);
        $this->assertFileNotExists($cache_fn0);
        // request 
        $url = $router->generate('jili_emar_product_retrieve', $queries, true);

        echo $url, PHP_EOL;
        $client->request('GET', $url );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        // to check the caching works, request agian, get the file created time.
        $this->assertFileExists($cache_fn0);

        $actual = file_get_contents($cache_fn0);
        $actual =  preg_replace('/\?k=.*?&/', '?k=&', $actual);
        $expected = preg_replace('/\?k=.*?&/', '?k=&', serialize($value0));

        $this->assertEquals($expected,$actual);

#        unset($cache_fn0);
        unset($raw0);

        $cached = $cache_fn0;
        $mtime = filemtime($cached);
        $ctime = filectime($cached);
        $last_time = ($mtime < $ctime) ? $ctime: $mtime;
        $a1 = $mtime.','. $ctime.','. $last_time;
        // check the again.
        echo $url, PHP_EOL;
        $client->request('GET', $url );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
        // to check the caching works, request agian, get the file created time.
        $this->assertFileExists($cache_fn0);
        //check the cache file : value
        $mtime = filemtime($cached);
        $ctime = filectime($cached);
        $last_time = ($mtime < $ctime) ? $ctime: $mtime;

        $a2 = $mtime.','. $ctime.','. $last_time;
        echo $a1, PHP_EOL, $a2;

    }

    /**
     * check the open.category
     * @group cache 
     */
    public function testRetrieveCategoryAction()
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

// [product.categories] keys & values 
        $generalCategoryGetService = $container->get('general.category_get');
        $category = $generalCategoryGetService->fetch();
        $cache = $generalCategoryGetService->getCacheProxy();

        $category0 = $cache->get();
        $key = $cache->getKey();
        
        echo 'key',$key,PHP_EOL;
        $cache_fn0 =$cache_dir .DIRECTORY_SEPARATOR . $key.'.cached';
        $this->assertFileExists($cache_fn0);
        exec('rm -rf '. $cache_fn0);
        $this->assertFileNotExists($cache_fn0);

        $category_sub = array();
        foreach($category as $cat) {
                $sub_cats = $generalCategoryGetService->fetch(array('parent_id' => $cat['catid']));

                $sub_cache = $generalCategoryGetService->getCacheProxy();
                $category_sub [] = array(
                    'key' =>  $sub_cache->getKey(),
                    'value' => serialize($sub_cache->get())
                );
        }

        foreach( $category_sub as $cat) {
            echo 'sub key: ',$cat['key'],PHP_EOL;
            $cache_fn =$cache_dir .DIRECTORY_SEPARATOR . $cat['key'].'.cached';
            $this->assertFileExists($cache_fn);
            exec('rm -rf '. $cache_fn);
            $this->assertFileNotExists($cache_fn);
        }


        //// get cache key & cache file name
        //$cat_id = $request->query->getInt('cat');
        //$web_id = $request->query->getInt('w');
        //$price_range = $request->query->get('pr');
        //$page_no = $request->query->get('p', 1);
        
// request
        $url  = $router->generate('jili_emar_product_retrieve', array(), true);
        echo $url,PHP_EOL;
        $client->request('GET', $url );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

// [product.categories] check
        foreach( $category_sub as $cat) {
            $cache_fn =$cache_dir .DIRECTORY_SEPARATOR . $cat['key'].'.cached';
            $this->assertFileExists( $cache_fn);
            $this->assertStringEqualsFile($cache_fn, $cat['value']);
        }
        unset($category_sub);
        $this->assertFileExists($cache_fn0);

        $actual = file_get_contents($cache_fn0);

        $this->assertStringEqualsFile($cache_fn0, serialize($category0));
        unset($cache_fn0);
        unset($category0);
    }
 private function buildParamFromQuery($queries) {
        $params = array();
        if( isset($queries['w'])) {
            $params['webid'] =(int) $queries['w'];
        }
        if( isset($queries['cat'])) {
            $params['catid'] = (int) $queries['cat'];
        }
        if( isset($queries['p'])) {
            $params['page_no'] = (int) $queries['p'];
        }
        if( isset($queries['pr'])) {
            $params['price_range'] =(string) $queries['pr'];
        }
        return $params;
 }
}
