<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Repository\PointHistory;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\User;

class LoadIssetInsertData extends AbstractFixture implements  FixtureInterface
{
    public static $USERS;

    public function __construct(){
        self::$USERS = array();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        // no related record 
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice32@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        // with  related record  but not in query range
        $user = new User();
        $user->setNick('bob32');
        $user->setEmail('bob32@voyagegroup.com.cn');
        $user->setPoints(70);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;


        $yesterday_date = new \DateTime();
        $yesterday_date->setTimestamp( time() - 24*60*60 );

        $pointHistoryClass = 'Jili\\ApiBundle\\Entity\\PointHistory0' . ($user->getId() % 10); 
        $pointHistory = new $pointHistoryClass(); 
        $pointHistory->setUserId($user->getId());
        $pointHistory->setPointChangeNum( 1);
        $pointHistory->setReason( 30);
        $pointHistory->setCreateTime( $yesterday_date); //new \DateTime('2014-11-18 21:10:09') );
        $manager->persist($pointHistory);
        $manager->flush();

        $today_date= new \DateTime();
        $today_date->setTimestamp( time());

        $pointHistoryClass = 'Jili\\ApiBundle\\Entity\\PointHistory0' . ($user->getId() % 10); 
        $pointHistory = new $pointHistoryClass(); 
        $pointHistory->setUserId($user->getId());
        $pointHistory->setPointChangeNum( 1);
        $pointHistory->setReason( 16);
        $pointHistory->setCreateTime( $today_date); //new \DateTime('2014-11-18 21:10:09') );
        $manager->persist($pointHistory);
        $manager->flush();

        // 3. the normal sample
        $user = new User();
        $user->setNick('carl32');
        $user->setEmail('carl32@voyagegroup.com.cn');
        $user->setPoints(70);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;


        $yesterday_date = new \DateTime();
        $yesterday_date->setTimestamp( time() );

        $pointHistoryClass = 'Jili\\ApiBundle\\Entity\\PointHistory0' . ($user->getId() % 10); 
        $pointHistory = new $pointHistoryClass(); 
        $pointHistory->setUserId($user->getId());
        $pointHistory->setPointChangeNum( 1);
        $pointHistory->setReason( 30);
        $pointHistory->setCreateTime( $yesterday_date); //new \DateTime('2014-11-18 21:10:09') );
        $manager->persist($pointHistory);
        $manager->flush();
    }
}
