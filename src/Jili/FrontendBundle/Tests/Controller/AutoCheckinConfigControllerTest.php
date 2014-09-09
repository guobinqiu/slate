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
    public function testCreateAction()
    {
        $this->assertEquals('1', 1);
    }

    /**
     * @group debug
     */
    public function testDeleteAction()
    {
        $this->assertEquals('1', 1);
    }
    /**
     * @group debug
     */
    public function testUpdateAction()
    {

        $this->assertEquals('1', 1);
    }
    /**
     * @group debug
     */
    public function testGetAction()
    {
        $this->assertEquals('1', 1);
    }


} 

