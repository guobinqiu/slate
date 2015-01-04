<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\TaoBaoUser;

class LoadTaoBaoUserCallbackData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $USERS;
    public static $TaoBaoUSERS;

    public function __construct() {
        self :: $USERS = array ();
        self :: $TaoBaoUSERS = array ();
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

        $TaoBaoUser  = new TaoBaoUser();
        $TaoBaoUser->setUserId($user->getId());
        $TaoBaoUser->setOpenId('973F697E97A60289C8C455B1D65FF5F0');
        $manager->persist($TaoBaoUser);
        $manager->flush();
        self :: $TaoBaoUSERS[] = $TaoBaoUser;

        // TaoBao_user  without jili_user
        $TaoBaoUser  = new TaoBaoUser();
        $TaoBaoUser->setUserId(99);
        $TaoBaoUser->setOpenId('973E697D97F60289B8B455A1C65CC5E0');
        $manager->persist($TaoBaoUser);
        $manager->flush();
        self :: $TaoBaoUSERS[] = $TaoBaoUser;
    }
}
