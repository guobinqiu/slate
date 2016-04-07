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

class LoadPanelRewardSopPointCommandData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $r = new SopRespondent();
        $r->setUserId($user->getId());
        $r->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $manager->persist($r);
        $manager->flush();

        $user = new User();
        $user->setNick('cc');
        $user->setEmail('test2@d8aspring.com');
        $user->setPoints(200);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $r = new SopRespondent();
        $r->setUserId($user->getId());
        $r->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $manager->persist($r);
        $manager->flush();
    }
}
