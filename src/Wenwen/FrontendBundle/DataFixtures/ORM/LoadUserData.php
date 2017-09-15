<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserTrack;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setNick('user1');
        $user1->setEmail('user@voyagegroup.com.cn');
        $user1->setPoints(100);
        $user1->setIconPath('test/test_icon.jpg');
        $user1->setRewardMultiple(1);
        $user1->setPwd('11111q');
        $user1->setIsEmailConfirmed(1);
        $user1->setRegisterDate(new \DateTime());
        $userTrack1 = new UserTrack();
        $userTrack1->setUser($user1);
        $user1->setUserTrack($userTrack1);
        $manager->persist($user1);
        $manager->flush();

        $user2 = new User();
        $user2->setNick('user2');
        $user2->setEmail('user2@voyagegroup.com.cn');
        $user2->setPoints(0);
        $user2->setIconPath('test/test_icon.jpg');
        $user2->setRewardMultiple(1);
        $user2->setPwd('11111q');
        $user2->setIsEmailConfirmed(1);
        $user2->setRegisterDate(new \DateTime());
        $user2->setInviteId($user1->getId());
        $userTrack2 = new UserTrack();
        $userTrack2->setUser($user2);
        $user2->setUserTrack($userTrack2);
        $manager->persist($user2);
        $manager->flush();

        $user3 = new User();
        $user3->setNick('user3');
        $user3->setEmail('user3@voyagegroup.com.cn');
        $user3->setPoints(0);
        $user3->setIconPath('test/test_icon.jpg');
        $user3->setRewardMultiple(1);
        $user3->setPwd('11111q');
        $user3->setIsEmailConfirmed(0);
        $user3->setRegisterDate(new \DateTime());
        $user3->setInviteId($user1->getId());
        $userTrack3 = new UserTrack();
        $userTrack3->setUser($user3);
        $user3->setUserTrack($userTrack3);
        $manager->persist($user3);
        $manager->flush();

        $user4 = new User();
        $user4->setNick('user4');
        $user4->setEmail(null);
        $user4->setPoints(0);
        $user4->setIconPath('test/test_icon.jpg');
        $user4->setRewardMultiple(1);
        $user4->setPwd('11111q');
        $user4->setIsEmailConfirmed(0);
        $user4->setRegisterDate(new \DateTime());
        $user4->setInviteId($user1->getId());
        $userTrack4 = new UserTrack();
        $userTrack4->setUser($user4);
        $user4->setUserTrack($userTrack4);
        $manager->persist($user4);
        $manager->flush();
    }
}