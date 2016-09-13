<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Services;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Wenwen\FrontendBundle\Entity\User;

class LoadUserBindData extends AbstractFixture implements FixtureInterface {

    public static $USERS;

    public function __construct() {
        self :: $USERS = array ();
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $user = new User();
        $user->setNick('alic32');
        $user->setEmail('alice.nima@voyagegroup.com.cn');
        $user->setIsEmailConfirmed(1);
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        self :: $USERS[] = $user;
    }
}
