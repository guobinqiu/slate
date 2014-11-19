<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Controller\GameSeeker;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameSeekerDaily;

class LoadGetChestInfoData extends AbstractFixture implements  FixtureInterface
{
    public static $USERS;
    public static $GAMESEEKLOGS;

    public function __construct() {
        self :: $USERS = array ();
        self :: $GAMESEEKLOGS = array ();
    }



    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice32@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $user = new User();
        $user->setNick('bob32');
        $user->setEmail('bob32@gmail.com');
        $user->setPoints(87);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $gameSeekDaily = new GameSeekerDaily();
        $gameSeekDaily->setUserId($user->getId());
        $gameSeekDaily->setPoints(0);
        $gameSeekDaily->setClickedDay( new \DateTime('2014-11-18 19:00:19') );
        $gameSeekDaily->setToken('0ce584a7a8c13e1c74f25637ecd8f702');
        $gameSeekDaily->setTokenUpdatedAt(new \DateTime('2014-11-18 18:40:19') );
        $manager->persist($gameSeekDaily);
        $manager->flush();
        self :: $GAMESEEKLOGS[] = $gameSeekDaily;


// id              
// user_id         
// points          
// created_day     
// token           
// token_updated_at
// 
    }
}
