<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserWenwenCross;
use Jili\ApiBundle\Entity\ExchangeFromWenwen;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class LoadHandleExchangeWenData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $USERS;
    public static $USER_WENWEN_CROSS;
    public static $EXCHANGE_FROM_WENWEN;
    public static $TASK_HISTORY;
    public static $POINTS_EXCHANGES;
    public static $SEND_MESSAGE;

    public function __construct() {
        self :: $USERS = array ();
        self :: $USER_WENWEN_CROSS = array ();
        self :: $EXCHANGE_FROM_WENWEN = array ();
        self :: $TASK_HISTORY = array ();
        self :: $POINTS_EXCHANGES = array ();
        self :: $SEND_MESSAGE = array ();
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

        // user table
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

        // cross table
        foreach (self :: $USERS as $user) {
            $cross = new UserWenwenCross();
            $cross->setUserId($user->getId());
            $manager->persist($cross);
            $manager->flush();
            self :: $USER_WENWEN_CROSS[] = $cross;
        }

        //PointsExchange=>HandleExchange
        for ($i = 0; $i < 3; $i++) {
            $change_point = 2010;
            self :: $USERS[0]->setPoints(self :: $USERS[0]->getPoints() - intval($change_point));
            $manager->persist(self :: $USERS[0]);
            $manager->flush();

            $pointschange = new PointsExchange();
            $pointschange->setUserId(self :: $USERS[0]->getId());
            $pointschange->setType(4);
            $pointschange->setSourcePoint(self :: $USERS[0]->getPoints() - intval($change_point));
            $pointschange->setTargetPoint(intval($change_point));
            $pointschange->setTargetAccount('13761756201');
            $pointschange->setExchangeItemNumber(20);
            $pointschange->setIp('192.168.1.28');
            $manager->persist($pointschange);
            $manager->flush();

            self :: $POINTS_EXCHANGES[] = $pointschange;
        }

        //ExchangeFromWenwen
        $exchangeFromWenwen = new ExchangeFromWenwen();
        $exchangeFromWenwen->setWenwenExchangeId('123456');
        $exchangeFromWenwen->setEmail(self :: $USERS[0]->getEmail());
        $exchangeFromWenwen->setUserWenwenCrossId(self :: $USER_WENWEN_CROSS[0]->getId());
        $exchangeFromWenwen->setPaymentPoint(3000);
        $manager->persist($exchangeFromWenwen);
        $manager->flush();
        self :: $EXCHANGE_FROM_WENWEN = $exchangeFromWenwen;

        //TaskHistory
        $po = SequenseEntityClassFactory :: createInstance('TaskHistory', self :: $USERS[0]->getId());
        $po->setOrderId(1);
        $po->setUserId(self :: $USERS[0]->getId());
        $po->setTaskType(3);
        $po->setCategoryType(1);
        $po->setTaskName('广告体验');
        $po->setRewardPercent(0);
        $po->setPoint(500);
        $po->setDate(date_create());
        $po->setOcdCreatedDate(date_create());
        $po->setStatus(1);
        $manager->persist($po);
        $manager->flush();
        self :: $TASK_HISTORY[] = $po;

        $po = SequenseEntityClassFactory :: createInstance('TaskHistory', self :: $USERS[0]->getId());
        $po->setOrderId(1);
        $po->setUserId(self :: $USERS[0]->getId());
        $po->setTaskType(1);
        $po->setCategoryType(2);
        $po->setTaskName('广告体验2');
        $po->setRewardPercent(0);
        $po->setPoint(500);
        $po->setDate(date_create());
        $po->setOcdCreatedDate(date_create());
        $po->setStatus(1);
        $manager->persist($po);
        $manager->flush();
        self :: $TASK_HISTORY[] = $po;

        //SendMessage
        $sm = SequenseEntityClassFactory :: createInstance('SendMessage', self :: $USERS[0]->getId());
        $sm->setSendFrom(0);
        $sm->setSendTo(self :: $USERS[0]->getId());
        $sm->setTitle("title");
        $sm->setContent("content");
        $sm->setReadFlag(0);
        $sm->setDeleteFlag(0);
        $manager->persist($sm);
        $manager->flush();
        self :: $SEND_MESSAGE[] = $sm;

        //AdwOrder
        $cpsOrder = new AdwOrder();
        $cpsOrder->setUserId(self :: $USERS[0]->getId());
        $cpsOrder->setAdId(1);
        $cpsOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
        $cpsOrder->setHappenTime(date_create(date('Y-m-d H:i:s')));
        $cpsOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
        $cpsOrder->setIncentiveType(1);
        $cpsOrder->setIncentiveRate(1);
        $cpsOrder->setIncentive(1);
        $cpsOrder->setOcd(1);
        $cpsOrder->setComm(1);
        $cpsOrder->setOrderPrice(1);
        $cpsOrder->setOrderStatus(2);
        $cpsOrder->setDeleteFlag(0);
        $manager->persist($cpsOrder);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }
}