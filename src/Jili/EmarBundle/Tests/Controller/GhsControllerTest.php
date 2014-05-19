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
     */
    public function testPromotionAction() {
        $client = static::createClient();
        $container = $client->getContainer();
       
        $logger= $container->get('logger');
        $router = $container->get('router');
        $em = $this->em;

        // rm the cached files, cache_data
        
        $cache_dir =$container->getParameter('cache_data_path');
        exec('rm -rf '. $cache_dir);
        // check not exists.

        // request 
        $url = $router->generate('jili_emar_ghs_promotion', array('tmpl'=> 'top','max'=> 9,'p'=>3 ), true);
        echo $url, PHP_EOL;

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        echo $client->getResponse()->getContent(),PHP_EOL;
        // check exists.

    }
}
