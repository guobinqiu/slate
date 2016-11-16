<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadPrizeItemData;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\PrizeItem;

class PrizeServiceTest extends WebTestCase
{
    private $em;
    private $prizeService;

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
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadPrizeItemData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->prizeService = $container->get('app.prize_service');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testPointBalance()
    {
        $this->prizeService->setPointBalance(10);
        $this->assertEquals(10, $this->prizeService->getPointBalance());

        $this->prizeService->resetPointBalance();
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE, $this->prizeService->getPointBalance());

        $this->prizeService->addPointBalance(10);
        $this->prizeService->addPointBalance(10);
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE + 20, $this->prizeService->getPointBalance());

        $this->prizeService->minusPointBalance(1);
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE + 19, $this->prizeService->getPointBalance());
    }

    public function testGetPrizeItem()
    {
        for ($i=0; $i<100; $i++) {
            $prizedItem = $this->prizeService->getPrizedItem(PrizeItem::TYPE_BIG, 0);
            $this->assertEquals(0, $prizedItem->getPoints());
            $this->assertLessThanOrEqual(100, $prizedItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizedItem = $this->prizeService->getPrizedItem(PrizeItem::TYPE_BIG, 1);
            $this->assertLessThanOrEqual(1, $prizedItem->getPoints());
            $this->assertLessThanOrEqual(8100, $prizedItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizedItem = $this->prizeService->getPrizedItem(PrizeItem::TYPE_BIG, 10);
            $this->assertLessThanOrEqual(10, $prizedItem->getPoints());
            $this->assertLessThanOrEqual(9600, $prizedItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizedItem = $this->prizeService->getPrizedItem(PrizeItem::TYPE_BIG, 100);
            $this->assertLessThanOrEqual(100, $prizedItem->getPoints());
            $this->assertLessThanOrEqual(9990, $prizedItem->getMax());
        }

        for ($i=0; $i<1000; $i++) {
            $prizedItem = $this->prizeService->getPrizedItem(PrizeItem::TYPE_BIG, 500);
            $this->assertLessThanOrEqual(500, $prizedItem->getPoints());
            $this->assertLessThanOrEqual(9999, $prizedItem->getMax());
        }
    }

    public function testDrawPrize()
    {
        $this->prizeService->setPointBalance(99999999);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $before = $this->prizeService->getPointBalance();
        $points = 0;
        for ($i=0; $i<100; $i++) {
            $points += $this->prizeService->drawPrize($user);
        }
        $after = $this->prizeService->getPointBalance();
        $this->assertEquals($before - $after, $points);
    }
}
