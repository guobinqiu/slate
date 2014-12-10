<?php

namespace Jili\BackendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DecemberActivityControllerTest extends WebTestCase
{
 
    private $em ;
    private $has_fixture;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container  = static::$kernel->getContainer();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        // $em = ?
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();
        $this->has_fixture = true;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->has_fixture) {
            $this->em->close();
        }
    }


    /**
     * @group issue_537
     */
    public function testListAllAction()
    {

        $client = $this->client;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall');
       
        $expected_url = 'https://localhost/admin/activity/december/list-orders';
        $this->assertEquals( $expected_url , $url);
        
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall', array('p'=>1));
        $this->assertEquals( $expected_url , $url);

      
        $url =$container->get('router')->generate('jili_backend_decemberactivity_listall', array('p'=>10));
        $expected_url = 'https://localhost/admin/activity/december/list-orders/10';
        $this->assertEquals( $expected_url , $url);

        // no data get
        
    }
}
