<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
     * @group issue_469
     */
    function testIsAutoCheckin()
    {
        $rep =  $this->em->getRepository('JiliApiBundle:UserConfigurations');
        $user= LoadUserConfigurationsRepositoryCodeData::$USER[0];
        $this->assertNull(  $rep->isAutoCheckin($user->getId()), 'user without configs');
        // return null
       
        // return true
        $user= LoadUserConfigurationsRepositoryCodeData::$USER[1];
        $this->assertTrue( $rep->isAutoCheckin($user->getId()), 'user with configs = 1');
        
        // return false
        $user= LoadUserConfigurationsRepositoryCodeData::$USER[2];
        $this->assertFalse( $rep->isAutoCheckin($user->getId()), 'user with configs =0');
    }

    /**
     * @group issue_469
     */
    function testSearchUserConfigurationa()
    {
        $rep = $this->em->getRepository('JiliApiBundle:UserConfigurations');
        $configs  = LoadUserConfigurationsRepositoryCodeData::$CONFIGS;
        $this->assertCount(3, $rep->searchUserConfiguration() , ' search all' );

        $this->assertCount(0, $rep->searchUserConfiguration('auto') , ' search noexists flagname' );
        $this->assertEmpty( $rep->searchUserConfiguration('auto') , ' search  noexists flagname' );

        $user = LoadUserConfigurationsRepositoryCodeData::$USER[0];
        $return = $rep->searchUserConfiguration('auto', $user->getId() );
        $this->assertCount(0, $return, ' search noexists flagname , exist userId ' );
        $this->assertEmpty( $return, ' search noexists flagname, exist userId ' );
        
        // $return = $rep->searchUserConfiguration('auto_checkin', 1);
        $return = $rep->searchUserConfiguration('auto_checkin', 9999);
        $this->assertEmpty( $return, ' search exists flagname, none exist userId ' );

        $return = $rep->searchUserConfiguration('auto_checkin' );
        $this->assertCount( 2, $return,'query by flagName ' );

        $user = LoadUserConfigurationsRepositoryCodeData::$USER[2];
        $return = $rep->searchUserConfiguration( null, $user->getId() );
        $this->assertCount(2, $return, ' query by userId ' );

        $return = $rep->searchUserConfiguration('auto_checkin', $user->getId() );
        $this->assertCount(1, $return, ' search exists flagname, exist userId ' );

        $this->assertEquals($configs[1]->getId() ,$return[0]->getId() );
    }
}
