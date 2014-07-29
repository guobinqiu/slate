<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadLandingTracerCodeData;

class UserSignUpRouteRepositoryTest extends KernelTestCase
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
     * @group debug
     * @group issue_396
     * @group signup_trace
     */
    public function testSigned()
    {
        $container  = static::$kernel->getContainer();
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

        $repository = $em->getRepository('JiliApiBundle:UserSignUpRoute');

        $repository->insert(array( 'user_id'=> $user->getId(), 'source_route'=>'baidu_partnerg' ) );
        // order by id desc
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 1,$records, 'check the UserSignUpRouteRepository insert ');

        $this->assertEquals( $user->getId() , $records[0]->getUserId() , 'check the user_source_logger table');
        $this->assertEquals( 'baidu_partnerg' ,$records[0]->getSourceRoute(), 'check the user_source_logger table');
    }
}
