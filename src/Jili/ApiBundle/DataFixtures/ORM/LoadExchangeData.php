<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;

class LoadExchangeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $POINTS_EXCHANGES;

    public function __construct() {
        self :: $POINTS_EXCHANGES = array ();
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

        $user = new User();
        $user->setNick('bb');
        $user->setEmail('zhangmm@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        for ($i = 0; $i < 3; $i++) {
            $change_point = 2010;
            $user->setPoints($user->getPoints() - intval($change_point));
            $manager->persist($user);
            $manager->flush();

            $pointschange = new PointsExchange();
            $pointschange->setUserId($user->getId());
            $pointschange->setType(4);
            $pointschange->setSourcePoint($user->getPoints() - intval($change_point));
            $pointschange->setTargetPoint(intval($change_point));
            $pointschange->setTargetAccount('13761756201');
            $pointschange->setExchangeItemNumber(20);
            $pointschange->setIp('192.168.1.28');
            $manager->persist($pointschange);
            $manager->flush();

            self :: $POINTS_EXCHANGES[] = $pointschange;
        }


        $user = new User();
        $user->setNick('bb1');
        $user->setEmail('zhangmm1@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setNick('bb2');
        $user->setEmail('zhangmm2@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }
}
