<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\ExchangeFromWenwen;

class LoadHandleExchangeWenData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $USERS;
    public static $EXCHANGE_FROM_WENWEN;

    public function __construct() {
        self :: $USERS = array ();
        self :: $EXCHANGE_FROM_WENWEN = array ();
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
        $user->setNick('aa');
        $user->setEmail('exchange1@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $user = new User();
        $user->setNick('bb');
        $user->setEmail('exchange2@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $user = new User();
        $user->setNick('cc');
        $user->setEmail('exchange3@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $exchangeFromWenwen = new ExchangeFromWenwen();
        $exchangeFromWenwen->setWenwenExchangeId('123456');
        $exchangeFromWenwen->setEmail(self :: $USERS[0]->getEmail());
        $exchangeFromWenwen->setPaymentPoint(3000);
        $manager->persist($exchangeFromWenwen);
        $manager->flush();
        self :: $EXCHANGE_FROM_WENWEN = $exchangeFromWenwen;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }
}
