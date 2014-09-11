<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserEdmUnsubscribe;

class LoadUserEdmUnsubscribeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct() {

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
        $user->setNick('bb');
        $user->setEmail('zhangmm@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $userEdmUnsubscribe = new UserEdmUnsubscribe();
        $userEdmUnsubscribe->setUserId($user->getId());
        $userEdmUnsubscribe->setCreatedTime(date_create());
        $manager->persist($userEdmUnsubscribe);
        $manager->flush();

        $user = new User();
        $user->setNick('aa');
        $user->setEmail('zhangmm1@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $userEdmUnsubscribe = new UserEdmUnsubscribe();
        $userEdmUnsubscribe->setUserId($user->getId());
        $userEdmUnsubscribe->setCreatedTime(date_create());
        $manager->persist($userEdmUnsubscribe);
        $manager->flush();

        $user = new User();
        $user->setNick('cc');
        $user->setEmail('zhangmm2@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
    }
}