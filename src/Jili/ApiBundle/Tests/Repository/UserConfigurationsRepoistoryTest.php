<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\UserEdmUnsubscribeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserConfigurationsRepositoryCodeData;

class UserConfigurationsRepositoryTest  extends KernelTestCase 
{
    
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();


        $tn = $this->getName();
        if (in_array($tn, array('testIsAutoCheckin')) ){

            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            // load fixtures
            $fixture = new LoadUserConfigurationsRepositoryCodeData();
            $fixture->setContainer($container);

            $loader = new Loader();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());

        }
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }
    
    /**
     */
    function testIsAutoCheckin()
    {
        // return null
       
        // return false
        
        // return true

    }
}
