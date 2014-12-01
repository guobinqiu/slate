<?php 
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\DataFixtures\ORM\Repository\PointHistory\LoadIssetInsertData;

class PointHistoryRepositoryTest extends KernelTestCase 
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();

        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);

        $loader = new Loader();
        $fixture = new LoadIssetInsertData();
        $loader->addFixture($fixture);

        $executor->purge();
        $executor->execute($loader->getFixtures());

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
     * @group point
     * @group issue_524 
     */
    public function testIssetInsert()
    {
        // user with no point_history record
        $user = LoadIssetInsertData::$USERS[0];
        $em = $this->em;
        $instance = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->issetInsert( $user->getId(), 30);
        $this->assertNotNull($instance);
        $this->assertEmpty($instance);

        // a yesterday point_history record  and other reason record
        $user = LoadIssetInsertData::$USERS[1];
        $instance = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->issetInsert( $user->getId(), 30);
        $this->assertNotNull($instance);
        $this->assertEmpty($instance);

        // normal 
        $user = LoadIssetInsertData::$USERS[2];
        $instance = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->issetInsert( $user->getId(), 30);
        $this->assertNotNull($instance);
        $this->assertNotEmpty($instance);
        $this->assertCount(1, $instance);
        $this->assertArrayHasKey('id',  $instance[0]);
    }

    /**
     * @group point
     * @group issue_524 
     */
    public function testIsGameSeekerCompletedToday () 
    {
        // user with no point_history record
        $user = LoadIssetInsertData::$USERS[0];
        $em = $this->em;
        $result = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->isGameSeekerCompletedToday( $user->getId());
        $this->assertSame(false ,$result);

        // a yesterday point_history record  and other reason record
        $user = LoadIssetInsertData::$USERS[1];
        $result = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->isGameSeekerCompletedToday( $user->getId());
        $this->assertSame(false ,$result);

        // normal 
        $user = LoadIssetInsertData::$USERS[2];
        $result = $em->getRepository('JiliApiBundle:PointHistory0'. ( $user->getId() % 10))->isGameSeekerCompletedToday( $user->getId());
        $this->assertSame(true ,$result);
    }
}
