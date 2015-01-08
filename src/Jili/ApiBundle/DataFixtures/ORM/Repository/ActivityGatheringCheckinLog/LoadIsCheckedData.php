<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Repository\ActivityGatheringCheckinLog;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\ActivityGatheringCheckinLog;

class LoadIsCheckedData extends AbstractFixture implements  FixtureInterface {

    public static $USERS;
    public static $LOGS;

    public function __construct() {
        self :: $USERS = array ();
        self :: $LOGS= array ();
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
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $log= new ActivityGatheringCheckinLog();
        $log->setUser($user)
            ->setCheckinAt(new \DateTime());
        $manager->persist($log);
        $manager->flush();
        self :: $LOGS[] = $log;

        $user = new User();
        $user->setNick('cccc');
        $user->setEmail('cccr@voyagegroup.com.cn');
        $user->setPoints(101);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;
    }
}

