<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\WeiBoUser;

class LoadWeiBoUserCallbackData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $USERS;
    public static $WEIBOUSERS;

    public function __construct() {
        self :: $USERS = array ();
        self :: $WEIBOUSERS = array ();
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
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $weiboUser  = new WeiBoUser();
        $weiboUser->setUserId($user->getId());
        $weiboUser->setOpenId('973F697E97A60289C8C455B1D65FF5F0');
        $manager->persist($weiboUser);
        $manager->flush();
        self :: $WEIBOUSERS[] = $weiboUser;

        // weibo_user  without jili_user
        $weiboUser  = new WeiBoUser();
        $weiboUser->setUserId(99);
        $weiboUser->setOpenId('973E697D97F60289B8B455A1C65CC5E1');
        $manager->persist($weiboUser);
        $manager->flush();
        self :: $WEIBOUSERS[] = $weiboUser;
    }
}
