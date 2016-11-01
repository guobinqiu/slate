<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\PrizeItem;

class LoadPrizeItemData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(0)
            ->setPercent('1%')
            ->setMin(1)
            ->setMax(100)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(1)
            ->setPercent('80%')
            ->setMin(101)
            ->setMax(8100)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(10)
            ->setPercent('15%')
            ->setMin(8101)
            ->setMax(9600)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(100)
            ->setPercent('3.9%')
            ->setMin(9601)
            ->setMax(9990)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(500)
            ->setPercent('0.09%')
            ->setMin(9991)
            ->setMax(9999)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(PrizeItem::FIRST_PRIZE_POINTS)
            ->setPercent('0.01%')
            ->setMin(10000)
            ->setMax(10000)
            ->setType(PrizeItem::TYPE_BIG)
            ->setQuantity(1);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(0)
            ->setPercent('10%')
            ->setMin(1)
            ->setMax(10)
            ->setType(PrizeItem::TYPE_SMALL)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $prizeItem = new PrizeItem();
        $prizeItem
            ->setPoints(1)
            ->setPercent('90%')
            ->setMin(11)
            ->setMax(100)
            ->setType(PrizeItem::TYPE_SMALL)
            ->setQuantity(9999999);
        $manager->persist($prizeItem);

        $manager->flush();
    }
}