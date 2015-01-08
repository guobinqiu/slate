<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;


use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Jili\ApiBundle\DataFixtures\ORM\Repository\ActivityGatheringCheckinLog\LoadIsCheckedData;

class ActivityGatheringCheckinLogRepositoryTest extends KernelTestCase {


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
        $executor->purge();
        $tn  = $this->getName();
        if ($tn === 'testLog') {
            $fixture = new LoadUserData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        } elseif ($tn === 'testIsChecked') {
            $fixture = new LoadIsCheckedData();
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
     * @group issue_618
     */
    public function testLog()
    {
        $em = $this->em;
        $user = LoadUserData::$USERS[0];
        $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->log(array('userId'=> $user->getId() ));

        $entities = $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->findAll();
        $this->assertCount(1, $entities);
        $this->assertEquals($user->getId(), $entities[0]->getUser()->getId());
   }

    /**
     * @group issue_618
     */
    public function testIsChecked()
    {

        $em = $this->em;
        $user = LoadIsCheckedData::$USERS[0];
        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->isChecked(array('userId'=> $user->getId() ));
        $this->assertTrue($actual);

        $user = LoadIsCheckedData::$USERS[1];
        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->isChecked(array('userId'=> $user->getId() ));
        $this->assertFalse($actual);

    }
}

