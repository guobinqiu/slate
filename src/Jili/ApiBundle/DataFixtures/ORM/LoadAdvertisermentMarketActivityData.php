<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\MarketActivity;

class LoadAdvertisermentMarketActivityData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $MARKET_ACTIVITY;

    public function __construct() {
    }
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {

        $ad = new Advertiserment();
        $ad->setTitle("当当网");
        $ad->setType(0);
        $ad->setDecription("");
        $ad->setContent("");
        $ad->setImageurl("");
        $ad->setIconImage("");
        $ad->setListImage("");
        $ad->setIncentiveType(1);
        $ad->setIncentiveRate(30);
        $ad->setRewardRate(30);
        $ad->setCategory(1);
        $ad->setInfo("test");
        $ad->setIncentive(10);
        $ad->setDeleteFlag(0);
        $manager->persist($ad);
        $manager->flush();

        $ma = new MarketActivity();

        $ma->setAid($ad->getId());
        $ma->setBusinessName("当当网 年中庆第3季反季清仓");
        $ma->setActivityDescription("当当网 商家活动文字说明");
        $ma->setCategoryId(1);
        $ma->setActivityUrl("");
        $ma->setActivityImage("");
        $ma->setStartTime(date_create("2013-06-05 00:00:00"));
        $ma->setEndTime(date_create("2099-06-05 00:00:00"));
        $ma->setDeleteFlag(0);
        $manager->persist($ma);
        $manager->flush();

        self :: $MARKET_ACTIVITY = $ma;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }
}