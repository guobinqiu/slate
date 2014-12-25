<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository\GameSeekerDaily;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameSeekerDaily;

/**
 * 
 **/
class LoadGetInfoByUserData extends AbstractFixture implements  FixtureInterface
{
    
    public static $GAMESEEKLOGS;

    public function __construct() {
        self :: $GAMESEEKLOGS = array ();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
        $yesterday = new \Datetime();
        $yesterday->setTimestamp(time() - 24 * 60 * 60); 
        $update_at = new \Datetime();
        $update_at->setTimestamp(time() - 24 * 60 * 60  - 120); 

        $gameSeekerDaily = new GameSeekerDaily();
        $gameSeekerDaily->setUserId(1);
        $gameSeekerDaily->setPoints(-1);
        $gameSeekerDaily->setClickedDay( $yesterday );
        $gameSeekerDaily->setToken('0ce584a7a8c13e1c74f25637ecd8f702');
        $gameSeekerDaily->setTokenUpdatedAt($update_at );
        $manager->persist($gameSeekerDaily);
        $manager->flush();
        self::$GAMESEEKLOGS[] = $gameSeekerDaily;

        $today = new \DateTime();
        $update_at = new \Datetime();
        $update_at->setTimestamp(time()  - 120); 

        $gameSeekerDaily = new GameSeekerDaily();
        $gameSeekerDaily->setUserId(10);
        $gameSeekerDaily->setPoints(-1);
        $gameSeekerDaily->setClickedDay( $today );
        $gameSeekerDaily->setToken('0ce584a7a8c13e1c74f25637ecd8f701');
        $gameSeekerDaily->setTokenUpdatedAt($update_at );
        $manager->persist($gameSeekerDaily);
        $manager->flush();
        self :: $GAMESEEKLOGS[] = $gameSeekerDaily;

        $gameSeekerDaily = new GameSeekerDaily();
        $gameSeekerDaily->setUserId(21);
        $gameSeekerDaily->setPoints(0);
        $gameSeekerDaily->setClickedDay( $today );
        $gameSeekerDaily->setToken('0ce584a7a8c13e1c74f25637ecd8f703');
        $gameSeekerDaily->setTokenUpdatedAt($update_at );
        $manager->persist($gameSeekerDaily);
        $manager->flush();
        self :: $GAMESEEKLOGS[] = $gameSeekerDaily;

    }
}

