<?php

namespace Wenwen\FrontendBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Entity\User;

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
        $manager->persist($user2);
        $manager->flush();
    }
}