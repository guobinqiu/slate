<?php

namespace Wenwen\FrontendBundle\Tests\Services;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadPrizeItemData;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadUserData;
use Wenwen\FrontendBundle\Entity\PrizeItem;

class LotteryServiceTest extends WebTestCase
{
    private $em;
    private $lotteryService;

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
        $this->lotteryService = $container->get('app.lottery_service');
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
        $this->lotteryService->setPointBalance(10);
        $this->assertEquals(10, $this->lotteryService->getPointBalance());

        $this->lotteryService->resetPointBalance();
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE, $this->lotteryService->getPointBalance());

        $this->lotteryService->addPointBalance(10);
        $this->lotteryService->addPointBalance(10);
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE + 20, $this->lotteryService->getPointBalance());

        $this->lotteryService->minusPointBalance(1);
        $this->assertEquals(PrizeItem::POINT_BALANCE_BASE + 19, $this->lotteryService->getPointBalance());
    }

    public function testGetPrizeItem()
    {
        for ($i=0; $i<100; $i++) {
            $prizeItem = $this->lotteryService->getPrizeItem(PrizeItem::TYPE_BIG, 0);
            $this->assertEquals(0, $prizeItem->getPoints());
            $this->assertLessThanOrEqual(100, $prizeItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizeItem = $this->lotteryService->getPrizeItem(PrizeItem::TYPE_BIG, 1);
            $this->assertLessThanOrEqual(1, $prizeItem->getPoints());
            $this->assertLessThanOrEqual(8100, $prizeItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizeItem = $this->lotteryService->getPrizeItem(PrizeItem::TYPE_BIG, 10);
            $this->assertLessThanOrEqual(10, $prizeItem->getPoints());
            $this->assertLessThanOrEqual(9600, $prizeItem->getMax());
        }

        for ($i=0; $i<100; $i++) {
            $prizeItem = $this->lotteryService->getPrizeItem(PrizeItem::TYPE_BIG, 100);
            $this->assertLessThanOrEqual(100, $prizeItem->getPoints());
            $this->assertLessThanOrEqual(9990, $prizeItem->getMax());
        }

        for ($i=0; $i<1000; $i++) {
            $prizeItem = $this->lotteryService->getPrizeItem(PrizeItem::TYPE_BIG, 500);
            $this->assertLessThanOrEqual(500, $prizeItem->getPoints());
            $this->assertLessThanOrEqual(9999, $prizeItem->getMax());
        }
    }

    public function testAddPointsSmall()
    {
        $this->lotteryService->setPointBalance(99999999);
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
        $before = $this->lotteryService->getPointBalance();
        $points = 0;
        for ($i=0; $i<100; $i++) {
            $points += $this->lotteryService->addPointsSmall($user);
        }
        $after = $this->lotteryService->getPointBalance();
        $this->assertEquals($before - $after, $points);
    }

    public function testLotteryTicket()
    {
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');

        $this->lotteryService->createLotteryTicket($user, PrizeItem::TYPE_BIG, '大');
        $this->lotteryService->createLotteryTicket($user, PrizeItem::TYPE_SMALL, '小');
        $this->lotteryService->createLotteryTicket($user, PrizeItem::TYPE_SMALL);
        $this->assertEquals(3, $this->lotteryService->getLotteryTicketNumberLeft($user));

        $this->lotteryService->deleteLotteryTicket($user);
        $this->assertEquals(2, $this->lotteryService->getLotteryTicketNumberLeft($user));
    }

    // 由于时间比较长，测试通过后注释掉了，如果你要修改bigPrizeBox方法，可在本地把注释放开
//    public function testAddPointsBig()
//    {
//        $this->lotteryService->setPointBalance(99999999);
//        $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByNick('user1');
//        $firstPrizeItem = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')
//            ->findOneByPoints(PrizeItem::FIRST_PRIZE_POINTS);
//        echo PHP_EOL . 'before quantity=' . $firstPrizeItem->getQuantity();
//        $before = $this->lotteryService->getPointBalance();
//        $points = 0;
//        //万分之一的大奖概率但不等于说10000次内必中1次,20000的话命中率高点
//        for ($i=0; $i<20000; $i++) {
//            $points += $this->lotteryService->addPointsBig($user);
//        }
//        $after = $this->lotteryService->getPointBalance();
//        $this->assertEquals($before - $after, $points);
//        echo PHP_EOL . 'after quantity=' . $firstPrizeItem->getQuantity();
//    }
}
