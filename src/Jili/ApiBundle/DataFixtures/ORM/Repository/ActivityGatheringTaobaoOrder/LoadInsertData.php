<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Repository\ActivityGatheringTaobaoOrder;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\ActivityGatheringTaobaoOrder;

class LoadInsertData extends AbstractFixture implements  FixtureInterface {

    public static $USERS;
    public static $ORDERS;

    public function __construct() {
        self :: $USERS = array ();
        self :: $ORDERS= array ();
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
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[0] = $user;

        $order= new ActivityGatheringTaobaoOrder();
        $order->setUser($user)
            ->setOrderIdentity('123456789012345')
            ->setCreatedAt( \DateTime::createFromFormat('Y-m-d H:i:s', '2015-02-07 00:00:00'));
        
        $manager->persist($order);
        $manager->flush();
        self :: $ORDERS[0] = $order;

        $user = new User();
        $user->setNick('cccc');
        $user->setEmail('cccr@voyagegroup.com.cn');
        $user->setPoints(101);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[1] = $user;

        $user = new User();
        $user->setNick('dddd');
        $user->setEmail('dddd@voyagegroup.com.cn');
        $user->setPoints(101);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[2] = $user;

        $at = \DateTime::createFromFormat('Y-m-d H:i:s', '2015-03-07 00:00:00');

        # 1 month ago.
        $order= new ActivityGatheringTaobaoOrder();
        $order->setUser($user)
            ->setOrderIdentity('123456789012346')
            ->setCreatedAt( $at );
        $manager->persist($order);
        $manager->flush();
        self :: $ORDERS[1] = $order;
    }
}

