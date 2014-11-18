<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Repository\PointRepository;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\Users;

class LoadIssetInsertData extends AbstractFixture implements  FixtureInterface
{


    public $USERS;

    public function __construct(){
        self::$USERS = array();
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        //load data for testing .
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

        // id               
        // user_id          
        // point_change_num 
        // reason           
        // create_time      
        $pointHistoryClass = 'Jili\\ApiBundle\\Entity\\PointHistory0' . ($user->getId() % 10); 
        $pointHistory = new $pointHistoryClass(); 
        $pointHistory->setUserId($user->getId());
        $pointHistory->setPointChangeNum( 1);
        $pointHistory->setReason( 30);
        $pointHistory->setCreateTime( new \DataTime('2014-11-18 21:10:09') );
        $manager->persist($pointHistory);
        $manager->flush();

    }
}
