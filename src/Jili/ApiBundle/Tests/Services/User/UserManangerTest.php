<?php
namespace Jili\ApiBundle\Tests\Services\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadLandingTracerCodeData;

class UserManagerTest extends  KernelTestCase
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

    public function testSetRegistrationRouteInvalidParams()
    {
        // inser user
        // purge the table
        // insert tables ??
        $container  = static::$kernel->getContainer();
        $userManager = $container->get('user_manager') ;
        $em = $this->em;
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadLandingTracerCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());
        $user = LoadLandingTracerCodeData::$USER[0];

        $userManager->setRegistrationRoute(array(  'source_route'=>'baidu_partnerg' ) );
        // order by id desc  
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 0,$records, 'check the user_manager table');

        $userManager->setRegistrationRoute(array( 'user_id'=> $user->getId()) );
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 0,$records, 'check the user_manager table');
    }
    /**
     * @group debug  
     * @group issue_396  
     * @group signup_trace 
     */
    public function testSetRegistrationRoute()
    {
        // inser user
        // purge the table
        // insert tables ??
        $container  = static::$kernel->getContainer();
        $userManager = $container->get('user_manager') ;
        $em = $this->em;
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadLandingTracerCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());
        $user = LoadLandingTracerCodeData::$USER[0];

        $userManager->setRegistrationRoute(array( 'user_id'=> $user->getId(), 'source_route'=>'baidu_partnerg' ) );
        // order by id desc  
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 1,$records, 'check the user_manager table');

        $this->assertEquals( $user->getId() , $records[0]->getUserId() , 'check the user_source_logger table');
        $this->assertEquals( 'baidu_partnerg' ,$records[0]->getSourceRoute(), 'check the user_source_logger table');

    }


}
