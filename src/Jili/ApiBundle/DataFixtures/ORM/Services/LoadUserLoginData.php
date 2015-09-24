<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Services;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserWenwenLogin;


class LoadUserLoginData extends AbstractFixture implements FixtureInterface 
{

   
    public static $USERS;

    public function __construct() 
    {
        self::$USERS = array();
    }


    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) 
    {
        //load data for testing .
        $user = new User();
        $user->setNick('alic32');
        $user->setEmail('alice.nima@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');

        $manager->persist($user);
        $manager->flush();
        self::$USERS[] = $user;

        //load data for testing .
        $user = new User();
        $user->setNick('bob32');
        $user->setEmail('bob.inch@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setOriginFlag(User::ORIGIN_FLAG_WENWEN);

        $manager->persist($user);
        $manager->flush();

        self::$USERS[] = $user;
        $login = new UserWenwenLogin();
        $login->setUser($user)
            ->setLoginPassword('aPaR9Ucsu4U=') // 123123 dZcCU45B0rk=
            ->setLoginPasswordCryptType('blowfish')
            ->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');
        $manager->persist($login);
        $manager->flush();
    }


}

