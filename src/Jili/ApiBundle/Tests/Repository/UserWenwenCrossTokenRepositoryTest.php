<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class UserWenwenCrossTokenRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $user;

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
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        $this->user = LoadUserData :: $USERS[0];

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
     * @group issue_487
     */
    public function testCreate() {
        $em = $this->em;
        $cross_id = 1;
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->create($cross_id);
        $this->assertEquals($cross_id, $crossToken->getCrossId());
    }

    /**
     * @group issue_487
     */
    public function testDelete() {
        $em = $this->em;
        $cross_id = 2;
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->create($cross_id);
        $this->assertEquals($cross_id, $crossToken->getCrossId());
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->findOneByCrossId($cross_id);
        $this->assertEquals(1, count($crossToken));
        $return = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->delete($cross_id);
        $this->assertTrue($return);
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->findOneByCrossId($cross_id);
        $this->assertEquals(0, count($crossToken));

    }
}
