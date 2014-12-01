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
        $yesterday->sub(new \DateInterval('P1D'));

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
        // visitDate 0:today: 1:yesterday
        //       (1, 3, today) + 这用户今天有记录
        //       (1, 3, yesterday) +这用户昨天有记录 
        //       (1, 4, today)+ 
        //       (1, 4, yesterday)+ 
        //       (11, 3, today) 这用户今天没记录 
        //       (11, 3, yesterday) + 
        //       (11, 4, today) + 
        //       (11, 4, yesterday)+ 


        //今天有记录
        $entity = new UserVisitLog();
        $entity->setUserId(1)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($today);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[0] = $entity;

        //昨天有记录
        $entity = new UserVisitLog();
        $entity->setUserId(1)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($yesterday);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[1] = $entity;

        // 今天有其它target的记录
        $entity = new UserVisitLog();
        $entity->setUserId(11)
            ->setTargetFlag(7)
            ->setVisitDate($today);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[2] = $entity;

        // 昨天有记录 
        $entity = new UserVisitLog();
        $entity->setUserId(11)
            ->setTargetFlag(UserVisitLog::TARGET_FLAG_GAME_SEEKER)
            ->setVisitDate($yesterday);
        $manager->persist($entity);
        $manager->flush();
        self::$ROWS[3] = $entity;
    }
}
    
