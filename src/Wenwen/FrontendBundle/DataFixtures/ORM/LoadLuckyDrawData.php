<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\LuckyDraw;

class LoadLuckyDrawData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $luckyDraw = new LuckyDraw();
        $luckyDraw->setPoints(0)->setPercent('1%')->setMin(1)->setMax(100)->setType('大奖池');
        $luckyDraw->setPoints(1)->setPercent('80%')->setMin(101)->setMax(8100)->setType('大奖池');
        $luckyDraw->setPoints(10)->setPercent('15%')->setMin(8101)->setMax(9600)->setType('大奖池');
        $luckyDraw->setPoints(100)->setPercent('3.9%')->setMin(9601)->setMax(9990)->setType('大奖池');
        $luckyDraw->setPoints(500)->setPercent('0.09%')->setMin(9991)->setMax(9999)->setType('大奖池');
        $luckyDraw->setPoints(300000)->setPercent('0.01%')->setMin(10000)->setMax(10000)->setType('大奖池');
        $manager->persist($luckyDraw);
        $manager->flush();
    }
}