<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadPrizeItemData;

class PrizeItemRepositoryTest extends KernelTestCase
{
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadPrizeItemData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testGetPrizeItems()
    {
        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('大奖池', 300000);
        $this->assertEquals(6, count($prizeItems));
        $this->assertEquals(10000, $prizeItems[0]->getMax());

        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('大奖池', 500);
        $this->assertEquals(5, count($prizeItems));
        $this->assertEquals(9999, $prizeItems[0]->getMax());

        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('大奖池', 499);
        $this->assertEquals(4, count($prizeItems));
        $this->assertEquals(9990, $prizeItems[0]->getMax());

        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('大奖池', 99);
        $this->assertEquals(3, count($prizeItems));
        $this->assertEquals(9600, $prizeItems[0]->getMax());

        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('小奖池', 0);
        $this->assertEquals(1, count($prizeItems));
        $this->assertEquals(10, $prizeItems[0]->getMax());

        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems('小奖池', 1);
        $this->assertEquals(2, count($prizeItems));
        $this->assertEquals(100, $prizeItems[0]->getMax());
    }
}