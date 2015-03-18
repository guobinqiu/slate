<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\Repository\ActivityGatheringTaobaoOrder\LoadInsertData;

class ActivityGatheringTaobaoOrderRepositoryTest extends KernelTestCase 
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
        $executor->purge();
        $tn  = $this->getName();
        if (in_array($tn, array('testInsert','testIsCheckedCurrentYearMonth','testInsertException'))) {
            $fixture = new LoadInsertData();
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
    public function testIsCheckedCurrentYearMonth()
    {
        $em = $this->em;
        $user = LoadInsertData::$USERS[0];
        $order = LoadInsertData::$ORDERS[0];

        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->isCheckedCurrentYearMonth(array(
                'userId'=>$user->getId()));

        $this->assertTrue($actual);

        $user = LoadInsertData::$USERS[1];
        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->isCheckedCurrentYearMonth(array(
                'userId'=>$user->getId()));

        $this->assertFalse($actual);

        $user = LoadInsertData::$USERS[2];
        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->isCheckedCurrentYearMonth(array(
                'userId'=>$user->getId()));

        $this->assertFalse($actual);
    }

    /**
     * @group issue_618
     */
    public function testInsert()
    {
        $em = $this->em;
        $user = LoadInsertData::$USERS[1];
        $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->insert(array('orderIdentity'=> '123454321','userId'=>$user->getId()));

        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->findOneBy(array('orderIdentity'=>'123454321'));
        
        $this->assertNotNull($actual);
        $this->assertInstanceOf('Jili\ApiBundle\Entity\ActivityGatheringTaobaoOrder',$actual);
        $this->assertEquals($user->getId(), $actual->getUser()->getId());
    }

    /**
     * @expectedException  \Doctrine\DBAL\DBALException
     * @group issue_618
     */
    public function testInsertException()
    {
        $em = $this->em;
        $user = LoadInsertData::$USERS[2];
        $order = LoadInsertData::$ORDERS[1];
        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->insert(array('orderIdentity'=>$order->getOrderIdentity(),'userId'=>$user->getId()));
    }
}
