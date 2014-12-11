<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

/**
 * 
 **/
class LoadAuditOrderData extends AbstractFixture implements  FixtureInterface
{

    public static $USERS;
    public static $ORDERS;
    public static $INFOS;

    public function __construct() {
        self::$USERS = array ();
        self::$ORDERS = array ();
        self::$INFOS = array ();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
        //INIT -> COMPLETED
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
        $user_id = $user->getId();
        // user with 1 row built eggs 
        $orderAt= new \Datetime();
        $orderAt->setTime(0,0);
        $orderAt->sub(new \DateInterval('P20D'));

        $day = new \Datetime();
        $day->sub(new \DateInterval('P10D')); // pending for 10 days
        $created = new \Datetime();
        $created->sub(new \DateInterval('P12DT1M' )); // 

        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder001')
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_INIT)
            ->setAuditPendedAt($day)
            ->setIsValid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[0] = $entity;
// user 1
        $user = new User();
        $user->setNick('bob');
        $user->setEmail('bob@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[1] = $user;
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user->getId() )
            ->setOrderId('testorder002')
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_INIT)
            ->setAuditPendedAt($day)
            ->setIsValid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[1] = $entity;
// user 2        
        $user = new User();
        $user->setNick('carl');
        $user->setEmail('carl@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[2] = $user;
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user->getId() )
            ->setOrderId('testorder002')
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_INIT)
            ->setAuditPendedAt($day)
            ->setIsValid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[2] = $entity;
        
// user 3        

        $user = new User();
        $user->setNick('dart');
        $user->setEmail('dart@gmail.com');
        $user->setPoints(2340);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[3] = $user;

        $created = new \Datetime();
        $created->sub(new \DateInterval('P12DT1M' )); // 
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user->getId() )
            ->setOrderId('testorder300'  )
            ->setOrderAt($orderAt)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[3] = $entity;
        
        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(49.03)
            ->setNumOfCommon(4)
            ->setNumOfConsolation(3)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[0] =  $entity;
//user 4
        $user = new User();
        $user->setNick('emy');
        $user->setEmail('emy@gmail.com');
        $user->setPoints(2340);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[4] = $user;

        $created = new \Datetime();
        $created->sub(new \DateInterval('P12DT1M' )); // 
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user->getId() )
            ->setOrderId('testorder400'  )
            ->setOrderAt($orderAt)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[4] = $entity;
        
        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(49.03)
            ->setNumOfCommon(4)
            ->setNumOfConsolation(3)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[1] =  $entity;

        // user 5
        $user = new User();
        $user->setNick('frank');
        $user->setEmail('frank@gmail.com');
        $user->setPoints(2340);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[5] = $user;

        $created = new \Datetime();
        $created->sub(new \DateInterval('P12DT1M' ));

        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user->getId() )
            ->setOrderId('testorder500'  )
            ->setOrderAt($orderAt)
            ->setCreatedAt($created);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[5] = $entity;
        
        $entity = new GameEggsBreakerEggsInfo();
        $entity->setUserId($user->getId() )
            ->setOffcutForNext(49.03)
            ->setNumOfCommon(4)
            ->setNumOfConsolation(3)
            ->setTokenUpdatedAt(new \Datetime()) ;
        $entity->refreshToken();
        $manager->persist($entity);
        $manager->flush();
        self::$INFOS[2] =  $entity;
    }
}
