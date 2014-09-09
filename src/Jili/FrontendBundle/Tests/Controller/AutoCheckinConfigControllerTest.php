<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class AutoCheckinConfigControllerTest extends WebTestCase
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
        $container = static::$kernel->getContainer();


        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $tn = $this->getName();
        if( in_array($tn, array('testDeleteAction','testUpdateAction', 'testGetAction')) ) {

        // $executor->execute($loader->getFixtures());
        }


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
     * @group debug
     */
    public function testCreate()
    {
        $client = static::createClient();
$container = $this->container;
 

        $url =  $container->get('router')->generate('jili_frontend_autocheckinconfig_create');
        $crawler = $client->request('PUT', $url);
// check request status code 
// ajax call 
// check response  
// checkin insert data. 

        $this->assertEquals('1', 1);
    }

    /**
     * @group debug
     */
    public function testDelete()
    {
// jili_frontend_autocheckinconfig_delete
        $this->assertEquals('1', 1);
    }
    /**
     * @group debug
     */
    public function testUpdate()
    {

// jili_frontend_autocheckinconfig_update
        $this->assertEquals('1', 1);
    }
    /**
     * @group debug
     */
    public function testGet()
    {
// jili_frontend_autocheckinconfig_get
        $this->assertEquals('1', 1);
    }


} 

