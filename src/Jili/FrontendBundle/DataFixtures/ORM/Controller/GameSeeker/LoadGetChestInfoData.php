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
        self::$USERS = array ();
        self::$GAMESEEKLOGS = array ();
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

        $today = new \DateTime();

        $gameSeekDaily = new GameSeekerDaily();
        $gameSeekDaily->setUserId($user->getId());
        $gameSeekDaily->setPoints(0);
        $gameSeekDaily->setToken('0ce584a7a8c13e1c74f25637ecd8f702');
        $gameSeekDaily->setTokenUpdatedAt($today );
        $manager->persist($gameSeekDaily);
        $manager->flush();
        self :: $GAMESEEKLOGS[] = $gameSeekDaily;

        // user has completed
        $user = new User();
        $user->setNick('carl32');
        $user->setEmail('carl32@gmail.com');
        $user->setPoints(78);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[] = $user;

        $point_history_class  =   'Jili\\ApiBundle\\Entity\\PointHistory0'.( $user->getId() % 10) ;
        $pointHistory =  new $point_history_class();
        $pointHistory->setUserId($user->getId())
            ->setPointChangeNum(1)
            ->setReason(30);
        $manager->persist($pointHistory);
        $manager->flush();

        $sql =<<<EOD
INSERT INTO `ad_category` ( `id` , `category_name` , `asp` , `display_name` ) VALUES (30 , 'game', '91jili', '游戏寻宝箱')
EOD;
        $manager->getConnection()->executeUpdate($sql);
    }
}
