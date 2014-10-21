<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Entity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\MarketActivity;

class LoadAdvertisermentCodeData extends AbstractFixture implements  FixtureInterface  {

    public static $ROWS;

    public function __construct() {
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
        $ad->setImageurl('http://count.chanet.com.cn/click.cgi?a=480534&d=9340&u=&e=');
        $ad->setIconImage("");
        $ad->setListImage("");
        $ad->setIncentiveType(2);
        $ad->setIncentive(30);
        $ad->setIncentiveRate(30);
        $ad->setRewardRate(30);
        $ad->setCategory(1);
        $ad->setInfo("test");
        $ad->setIncentive(10);
        $ad->setDeleteFlag(0);
        $manager->persist($ad);
        $manager->flush();
        self :: $ROWS[] = $ad;


        $ad = new Advertiserment();
        $ad->setTitle('dgh');
        $ad->setType(0);
        $ad->setDecription("");
        $ad->setContent("");
        $ad->setImageurl('sdf');
        $ad->setIconImage("");
        $ad->setListImage("");
        $ad->setIncentiveType(2);
        $ad->setIncentive(30);
        $ad->setIncentiveRate(30);
        $ad->setRewardRate(30);
        $ad->setCategory(1);
        $ad->setInfo("test");
        $ad->setIncentive(10);
        $ad->setDeleteFlag(0);
        $manager->persist($ad);
        $manager->flush();
        self :: $ROWS[] = $ad;


        $ad = new Advertiserment();
        $ad->setTitle('dgh');
        $ad->setType(0);
        $ad->setDecription("");
        $ad->setContent("");
        $ad->setImageurl('');
        $ad->setIconImage("");
        $ad->setListImage("");
        $ad->setIncentiveType(2);
        $ad->setIncentive(30);
        $ad->setIncentiveRate(30);
        $ad->setRewardRate(30);
        $ad->setCategory(1);
        $ad->setInfo("test");
        $ad->setIncentive(10);
        $ad->setDeleteFlag(0);
        $manager->persist($ad);
        $manager->flush();
        self :: $ROWS[] = $ad;
    }

}
