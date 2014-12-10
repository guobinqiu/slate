<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder;

use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;

/**
 * 
 **/
class LoadTaobaoOrdersData  extends AbstractFixture implements  FixtureInterface
{

    public static $ORDERS;

    public function __construct() {
        self::$ORDERS = array ();
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) 
    {
        $orderAt= new \Datetime();
        $orderAt->setTime(0,0);
        $orderAt->sub(new \DateInterval('P20D'));
        $user_id = 1;
        // user with 1 row built eggs 
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder001'  )
            ->setOrderPaid(102.01)
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_COMPLETED)
            ->setIsEgged($entity::IS_EGGED_COMPLETED )
            ->setIsVAlid($entity::ORDER_VALID);

        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[0] = $entity;

        // user with 1 row ng 
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder002'  )
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_COMPLETED)
            ->setIsVAlid($entity::ORDER_INVALID)
            ->setIsEgged($entity::IS_EGGED_COMPLETED );
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[1] = $entity;

        // user with 1 row init 
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder003'  )
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_INIT)
            ->setIsVAlid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[2] = $entity;

        // user with 3 row pending during 7 days 
        $day = new \Datetime();
        $day->sub(new \DateInterval('P5D'));
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder004'  )
            ->setOrderPaid(40.01)
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
            ->setAuditPendedAt($day)
            ->setIsVAlid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[3] = $entity;

        // user with 3 row init after 7 days 
        $day = new \Datetime();
        $day->sub(new \DateInterval('P10D'));
        $entity = new GameEggsBreakerTaobaoOrder(); 
        $entity->setUserId($user_id)
            ->setOrderId('testorder005'  )
            ->setOrderPaid(50.01)
            ->setOrderAt($orderAt)
            ->setAuditStatus($entity::AUDIT_STATUS_PENDING)
            ->setAuditPendedAt($day)
            ->setIsVAlid($entity::ORDER_INIT)
            ->setIsEgged($entity::IS_EGGED_INIT);
        $manager->persist($entity);
        $manager->flush();
        self::$ORDERS[4] = $entity;

        // user with 30  row audit for building eggs 
        for($i =0 ;$i<30 ; $i++ ) {
            $day = new \Datetime();
            $day->sub(new \DateInterval('P10D'));

            $created = new \Datetime();
            $created->sub(new \DateInterval('P12DT'. (1+$i).'M' ));

            $entity = new GameEggsBreakerTaobaoOrder(); 
            $entity->setUserId($user_id)
                ->setOrderId('testorder00'. ( 6 + $i)   )
                ->setOrderPaid( 150.01 )
                ->setOrderAt($orderAt)
                ->setAuditStatus($entity::AUDIT_STATUS_COMPLETED)
                ->setAuditPendedAt($day)
                ->setIsValid($entity::ORDER_VALID)
                ->setIsEgged($entity::IS_EGGED_INIT)
                ->setCreatedAt($created);
            
            $manager->persist($entity);
            $manager->flush();
            self::$ORDERS[5+$i] = $entity;
        }
        // 5 + 29 = 34

    }
}
