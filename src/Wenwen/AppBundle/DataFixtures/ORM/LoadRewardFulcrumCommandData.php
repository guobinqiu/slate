<?php

namespace Wenwen\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;


class LoadRewardFulcrumCommandData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface
{
    public static $USERS;
    public static $SOP_RESPONDENT;

    public function __construct()
    {
        self::$USERS = array ();
        self::$SOP_RESPONDENT = array ();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('aaa');
        $user->setEmail('rpa-sys+aaa@d8aspring.com');
        $user->setPoints(11);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();
        self::$USERS[0] = $user;

        //load data for testing .
        $user = new User();
        $user->setNick('bbb');
        $user->setEmail('rpa-sys+aaab@d8aspring.com');
        $user->setPoints(23);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();
        self::$USERS[1] = $user;


        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId(self::$USERS[0]->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
        self::$SOP_RESPONDENT[0] = $sop_respondent;

        //inactive
        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId(self::$USERS[1]->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
        self::$SOP_RESPONDENT[1] = $sop_respondent;
    }
}

