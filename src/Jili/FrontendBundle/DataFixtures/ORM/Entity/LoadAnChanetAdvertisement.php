<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Entity;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\FrontendBundle\Entity\ChanetAdvertisement;


class LoadAnChanetAdvertisement extends AbstractFixture implements  FixtureInterface
{

    public static $ENTITIES;

    public function __construct() {
        self::$ENTITIES = array ();
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) 
    {
        $entity = new  ChanetAdvertisement();
        $entity->setAdsName('速普商城CPS推广')
            ->setAdsId('2939')
            ->setCategory('商务/商店')
            ->setAdsUrlType('首页推广链接')
            ->setAdsUrl('http://www.supuy.com/')
            ->setMarketingUrl('http://count.chanet.com.cn/click.cgi?a=480534&d=383449&u=&e=&url=http%3A%2F%2Fwww.supuy.com%2F')
            ->setfixedhash('638afb052b04d1b3f7ce10f613bcc3f64009494577cd781924a57568791db1ad')
            ->setIsActivated(1);

        $manager->persist($entity);
        $manager->flush();

        self::$ENTITIES[] = $entity;
    }
}
