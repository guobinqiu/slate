<?php

namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;
use Wenwen\AppBundle\Entity\SopProfilePoint;

class LoadUserSopData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface
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
        //load data for testing .
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();
        self::$USERS[] = $user;

        $sop_profile_point = new SopProfilePoint();
        $sop_profile_point->setUserId($user->getId());
        $sop_profile_point->setHash('duplicated');
        $sop_profile_point->setStatusFlag(1);
        $sop_profile_point->setPointValue(1);
        $manager->persist($sop_profile_point);
        $manager->flush();

        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId($user->getId());
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_ACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
        self::$SOP_RESPONDENT[] = $sop_respondent;

        //inactive
        $sop_respondent = new SopRespondent();
        $sop_respondent->setUserId($user->getId()+1);
        $sop_respondent->setStatusFlag($sop_respondent::STATUS_INACTIVE);
        $manager->persist($sop_respondent);
        $manager->flush();
        self::$SOP_RESPONDENT[] = $sop_respondent;
    }
}