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
     * @group debug
     */
    public function testRetrieveAction()
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

        // clear cache dir
        $cache_dir =$container->getParameter('cache_data_path');
        echo $cache_dir , PHP_EOL;
        exec('rm -rf '. $cache_dir);

        //// get cache key & cache file name
        //$cat_id = $request->query->getInt('cat');
        //$web_id = $request->query->getInt('w');
        //$price_range = $request->query->get('pr');
        //$page_no = $request->query->get('p', 1);

        $url  = $router->generate('jili_emar_product_retrieve', array(), true);

        $client->request('GET', $url);
        $this->assertEquals('200', $client->getResponse()->getStatusCode());

        echo $url,PHP_EOL;

    }
}
