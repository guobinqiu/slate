<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Controller\DecemberActivity;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

/**
 * 
 **/
class LoadGetEggsInfoData extends AbstractFixture implements  FixtureInterface
{

    public static $INFOS;
    public static $USERS;

    public function __construct() {
        self::$INFOS = array ();
        self::$USERS = array ();
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
        // user 0 . not open the page
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[0] = $user;

        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(10.03)
            ->setNumOfCommon(4)
            ->setNumOfConsolation(3)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[0] =  $entity;
    }
}

