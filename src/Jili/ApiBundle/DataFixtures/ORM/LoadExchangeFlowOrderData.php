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
use Jili\ApiBundle\Entity\ExchangeFlowOrder;

class LoadExchangeFlowOrderData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface {

    public static $USERS;
    public static $EXCHANGE_FLOW_ORDER;
    public static $POINTS_CHANGE;

    public function __construct() {
        self :: $USERS = array ();
        self :: $EXCHANGE_FLOW_ORDER = array ();
        self :: $POINTS_CHANGE = array ();
    }

    /**
    * {@inheritDoc}
    */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(2000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $pointschange  = new PointsExchange();
        $pointschange->setUserId($user->getId());
        $pointschange->setType(5);
        $pointschange->setTargetAccount('13761756201');
        $pointschange->setSourcePoint(2000);
        $pointschange->setTargetPoint(500);
        $pointschange->setExchangeItemNumber(40);
        $pointschange->setIp('127.1.1.1');
        $manager->persist($pointschange);
        $manager->flush();
        self :: $POINTS_CHANGE[] = $pointschange;

        $exchangeFlowOrder = new ExchangeFlowOrder();
        $exchangeFlowOrder->setUserId($user->getId());
        $exchangeFlowOrder->setExchangeId($pointschange->getId());
        $exchangeFlowOrder->setProvider('移动');
        $exchangeFlowOrder->setProvince('上海');
        $exchangeFlowOrder->setCustomProductId('20150');
        $exchangeFlowOrder->setPackagesize(150);
        $exchangeFlowOrder->setCustomPrise('15.500');
        $manager->persist($exchangeFlowOrder);
        $manager->flush();
        self :: $EXCHANGE_FLOW_ORDER[] = $exchangeFlowOrder;
    }
}