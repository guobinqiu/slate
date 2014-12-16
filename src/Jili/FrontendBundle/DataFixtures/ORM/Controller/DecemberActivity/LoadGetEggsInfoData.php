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
            ->setTotalPaid(180.03)
            ->setNumOfCommon(4)
            ->setNumOfConsolation(3)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[0] =  $entity;

        //user 1 with 1 common egg
        $user = new User();
        $user->setNick('bob32');
        $user->setEmail('bob32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[1] = $user;

        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(10.03)
            ->setTotalPaid(180.03)
            ->setNumOfCommon(1)
            ->setNumOfConsolation(0)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[1] =  $entity;

        //use 2 with 1 consolation  egg
        $user = new User();
        $user->setNick('carl');
        $user->setEmail('carl32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[2] = $user;

        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(10.03)
            ->setTotalPaid(180.03)
            ->setNumOfCommon(0)
            ->setNumOfConsolation(1)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[2] =  $entity;

        // user3 with no eggs
        $user = new User();
        $user->setNick('daisy32');
        $user->setEmail('daisy32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[3] = $user;

        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(10.03)
            ->setTotalPaid(180.03)
            ->setNumOfCommon(0)
            ->setNumOfConsolation(0)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[3] =  $entity;

        // user4 with no eggs
        $user = new User();
        $user->setNick('emy32');
        $user->setEmail('emy32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[4] = $user;

        /// the ad_category  for game_seeker
        $sql =<<<EOD
INSERT INTO `ad_category` ( `id` , `category_name` , `asp` , `display_name` ) VALUES (31 , 'game', '91jili', '游戏砸金蛋');
EOD;
        $manager->getConnection()->executeUpdate($sql);

    }
}

