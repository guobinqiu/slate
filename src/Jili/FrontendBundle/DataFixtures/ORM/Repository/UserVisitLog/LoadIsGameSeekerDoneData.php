<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository\UserVisitLog;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
// use Symfony\Component\DependencyInjection\ContainerAwareInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\UserVisitLog;

/**
 * 
 **/
class LoadIsGameSeekerDoneData extends AbstractFixture implements  FixtureInterface
{

    public static $ROWS;

    public function __construct()
    {
        self::$ROWS =array();
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
        $today = new \Datetime();

        $yesterday = new \Datetime();
        $yesterday->setTimestamp(time() - 24 * 60 * 60); 

// ( userId, TargetFlag, visitDate) 
//        000
//        001 01 
//        010 
//        011 
//        100 
//        101 
//        110 
//        111 
// userId 0:1, 1:11  
// targetFlag 0:3, 1:4
// visitDate 0:today: 1
//       (1, 3, now) 
//       (1, 3, yester) 
//       (1, 4, now) 
//       (1, 4, ye) 
        
        $entity = new UserVisitLog();
        $entity->setUserId(1)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($today);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[] = $entity;

        $entity = new UserVisitLog();
        $entity->setUserId(11)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($today);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[] = $entity;

//  
        $entity = new UserVisitLog();
        $entity->setUserId(1)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($yesterday);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[] = $entity;

        $entity = new UserVisitLog();
        $entity->setUserId(1)
            ->setTargetFlag(99)
            ->setVisitDate($today);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[] = $entity;
        
    }
}
    
