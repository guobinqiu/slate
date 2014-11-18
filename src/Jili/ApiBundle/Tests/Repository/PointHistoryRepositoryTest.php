<?php 

namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;


use Jili\ApiBundle\DataFixtures\ORM\Repository\PointRepository\LoadIssetInsertData;

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
     * @group debug 
     */
    public function testIssetInsert()
    {
        $user = LoadIssetInsertData::$USERS[0];
        $this->em->getRepository('JiliApiBundle:PointHistory0'. ( $user % 10))->issetInsert( );;
    }
    /**
     * @group point
     * @group issue_524 
     * @group debug 
     */
    public function testIsGameSeekerCompletedToday () 
    {

$user = LoadIssetInsertData::$USERS[0];
    }
}
