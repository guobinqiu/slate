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
class LoadTaobaoOrderFinishAuditData  extends AbstractFixture implements  FixtureInterface
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

        // user with 15 ( 5 valid , 3 invalid, 7 uncertain  pending for finish audit
        $day = new \Datetime();
        $day->sub(new \DateInterval('P10D')); // pending for 10 days
        for($i =0 ;$i< 5 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user_id)
                ->setOrderId('1234567890123'. ( 45 + $i)   )
                ->setOrderPaid( 140.01 )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_VALID)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[$i] = $entity;
        }
        // index after : 0 ~ 4,  
        // points = 5 * 140.1

        for($i =0 ;$i< 3 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user_id)
                ->setOrderId('1234567890123'. ( 50 + $i)   )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_INVALID)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[4+$i] = $entity;
        }
        // 0 ~ 7
        // com= 5 * 140.1 , 
        
        for($i =0 ;$i< 7 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user_id)
                ->setOrderId('1234567890123'. ( 53 + $i)   )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_UNCERTAIN)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[7+$i] = $entity;
        }
        // 0 ~ 14

        // completed  rows wont audit again.
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('123456789012360'   )
            ->setOrderPaid( 160.91 )
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_COMPLETED)
            ->setAuditPendedAt($day)
            ->setIsValid($entity::ORDER_VALID)
            ->setIsEgged($entity::IS_EGGED_INIT) // should be completed
            ->setCreatedAt($created);

        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[15] = $entity;
        
        // init rows wont audit after be set pending.
        $day = new \Datetime();
        $day->sub(new \DateInterval('P10D'));
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('123456789012361'  )
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_INIT)
            ->setAuditPendedAt($day)
            ->setIsVAlid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[16] = $entity;
       // 0 ~16 
        $user = new User();
        $user->setNick('bob');
        $user->setEmail('bob@gmail.com');
        $user->setPoints(120);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[1] = $user;
        for($i =0 ;$i< 10 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user->getId() )
                ->setOrderId('1234567890123'. ( 62 + $i)   )
                ->setOrderPaid(17.8)
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_VALID)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[17+$i] = $entity;
        }
       // 0~26 
        $user = new User();
        $user->setNick('carl');
        $user->setEmail('carl@gmail.com');
        $user->setPoints(4020);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
        self :: $USERS[2] = $user;
        for($i =0 ;$i< 17 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user->getId() )
                ->setOrderId('1234567890123'. ( 72 + $i)   )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_UNCERTAIN)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[27+$i] = $entity;
        }

        // 0 ~ 43
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
        for($i =0 ;$i< 5 ; $i++ ) {
            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' )); // 
            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user->getId() )
                ->setOrderId('123456789012'. ( 389 + $i)   )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_UNCERTAIN)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[44+$i] = $entity;
        }

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
        // 0 ~ 47
    }
}
