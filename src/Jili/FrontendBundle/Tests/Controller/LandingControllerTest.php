<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LandingControllerTest extends WebTestCase
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
     * @group issue_448 
     */
    public function testExternal()
    {
        
        $client = static::createClient();
        $container = $client->getContainer();

        $url = $container->get('router')->generate('_landing_external' );
        $url_expected  ='https://localhost/external-landing';
        $this->assertEquals($url_expected, $url, 'check the routing url, with https://');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'simple GET Request'  );

        // check the form
        // post the form
       // 
        //$client = static::createClient();
        //$crawler = $client->request('GET', '/hello/Fabien');
        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
}
