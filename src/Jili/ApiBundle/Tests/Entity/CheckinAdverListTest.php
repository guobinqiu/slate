<?php
namespace Jili\ApiBundle\Tests\Entity;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\Entity\CheckinAdverList;

class CheckinAdverListTest extends KernelTestCase
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

        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->container = $container;
        $this->em = $em;
    }

    public function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     */
    public function testConst()
    {
        $this->assertEquals(0, CheckinAdverList::ANY_OP_METHOD);
        $this->assertEquals(3, CheckinAdverList::MANUAL_OP_METHOD);
        $this->assertEquals(5, CheckinAdverList::AUTO_OP_METHOD);

    }
}

