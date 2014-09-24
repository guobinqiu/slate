<?php
namespace Jili\EmarBundle\Tests\Entity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\EmarBundle\DataFixtures\ORM\LoadEmarActivityCommissionRepositoryCodeData;

class EmarActivityCommissionRepositoryFunctionalTest extends WebTestCase
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
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $container  = static :: $kernel->getContainer();//->get('doctrine')->getManager();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadEmarActivityCommissionRepositoryCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->purge();
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group  emar_commission 
     */
    public function testGetCommissionListByMallName()
    {
        $mallName = '京东商城';
        $commissionList = $this->em->getRepository('JiliEmarBundle:EmarActivityCommission')->getCommissionListByMallName($mallName);
        $this->assertEquals(27, count($commissionList), 'the count of  '.$mallName. ' is wrong' );

        $mallName = '京东XX';
        $commissionList = $this->em->getRepository('JiliEmarBundle:EmarActivityCommission')->getCommissionListByMallName($mallName);
        $this->assertEquals(0, count($commissionList));
    }
}
