<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameEggsBrokenLog;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

/**
 * 
 **/
class LoadLogsData extends AbstractFixture implements  FixtureInterface
{

    public static $LOGS;
    public static $USERS;

    public function __construct() {
        self::$LOGS = array ();
        self::$USERS= array ();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
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
        $user_id = $user->getId();
        $createdAt = new \DateTime();
        $createdAt->sub(new \DateInterval('PT3M'));
        // a log of COMMON egg
        $entity = new GameEggsBrokenLog();
        $entity->setUserId($user->getId() ) 
            ->setEggType(GameEggsBreakerEggsInfo::EGG_TYPE_COMMON )
            ->setCreatedAt($createdAt)
            ->setPointsAcquired(13);
        $manager->persist($entity);
        $manager->flush();
        self::$LOGS[0] = $entity;
 
        // a log of consolation
        $entity = new GameEggsBrokenLog();
        $entity->setUserId($user->getId() ) 
            ->setEggType(GameEggsBreakerEggsInfo::EGG_TYPE_CONSOLATION)
            ->setPointsAcquired(1);
        $manager->persist($entity);
        $manager->flush();

        self::$LOGS[1] = $entity;

        $egg_types = array(
            GameEggsBreakerEggsInfo::EGG_TYPE_CONSOLATION,
            GameEggsBreakerEggsInfo::EGG_TYPE_COMMON
        );

        $egg_type_key_fixture = array(
            1,0,0,1,1,1,1,0,0,1
        );

        $points_fixture = array(
            80,1,0,8,100,8888,888,0,1,18
        );

        // user with 30  row audit for building eggs 
        for($i = 0 ;$i< 10 ; $i++ ) {
            $createdAt = new \DateTime();
            $createdAt->sub(new \DateInterval('PT'.(($i + 1) * 5 ).'H') );

            $user = new User();
            $user->setNick('bob'. $i);
            $user->setEmail('bob'.$i.'@gmail.com');
            $user->setPoints(100);
            $user->setIsInfoSet(0);
            $user->setRewardMultiple(1);
            $user->setPwd('111111');
            $manager->persist($user);
            $manager->flush();

            self :: $USERS[$i+1] = $user;
$user_id = $user->getId();
            $entity = new GameEggsBrokenLog();
            $entity->setUserId($user->getId() ) 
                ->setEggType( $egg_types[$egg_type_key_fixture[$i]]  )
                ->setCreatedAt($createdAt )
                ->setPointsAcquired( $points_fixture[$i] );
            $manager->persist($entity);
            $manager->flush();

            self::$LOGS[2+$i] = $entity;
            $manager->persist($entity);
            $manager->flush();

        }

// 1-4
        for ($i = 0; $i < 4; $i++) {
            $user = new User();
            $nick = 'alice'. (33 + $i );
            $user->setNick( $nick);
            $user->setEmail($nick.'@gmail.com');
            $user->setPoints(100);
            $user->setIsInfoSet(0);
            $user->setRewardMultiple(1);
            $user->setPwd('111111');
            $manager->persist($user);
            $manager->flush();

            self :: $USERS[11+$i] = $user;
            $user_id = $user->getId();

            $createdAt = new \DateTime();
            $createdAt->sub(new \DateInterval('PT'.(($i + 1 ) * 7  ).'H'));

            // a log of COMMON egg
            $entity = new GameEggsBrokenLog();
            $entity->setUserId($user->getId() ) 
                ->setEggType(GameEggsBreakerEggsInfo::EGG_TYPE_COMMON )
                ->setCreatedAt($createdAt)
                ->setPointsAcquired(88+$i);
            $manager->persist($entity);

            $manager->flush();
            self::$LOGS[12+$i] = $entity;
        }
    }
}
