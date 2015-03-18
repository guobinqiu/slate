<?php
namespace Jili\ApiBundle\Tests\Entity;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\Entity\TaskHistory00;

class TaskHistorryTest extends KernelTestCase
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
     * @group issue_537
     */
    public function testConst()
    {
        $this->assertEquals(6, TaskHistory00::TASK_TYPE_GAME_SEEKER );
        $this->assertEquals(7, TaskHistory00::TASK_TYPE_GAME_EGGS_BREAKER);

    }
}

