<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Entity\ActivityGatheringTaobaoOrder;

/**
 * 
 **/
class ActivityGatheringTaobaoOrderRepository extends EntityRepository
{
    
    /**
     * 写入新的订单
     * @param array $params
     */
    public function insert($params)
    {
        $em = $this->getEntityManager();
        $entity = new ActivityGatheringTaobaoOrder();
        $entity->setOrderIdentity($params['orderIdentity'] )
            ->setUser($em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']));
        $em->persist($entity);
        $em->flush();
    }

    /**
     * 判断用户是否提交了订单
     * @param array $params
     * @return true when exists, false when not found
     */
    public function isChecked($params)
    {
        $created_end  = new \DateTime();
        $created_end->add(new \DateInterval('P1M'));

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('o');

        $qb->select($qb->expr()->count('o.id'));
//        $qb->where( $qb->expr()->eq('o.user', $em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']) ))  ;
        $qb->Where( $qb->expr()->eq('o.user', $em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']) )  ;
        $qb->andWhere( $qb->expr()->lt('o.createdAt', ':endAt')  );
        $qb->andWhere( $qb->expr()->gte( 'o.createdAt' , ':startAt')  );
        $qb->setParameter('startAt',date('Y-m-1 00:00:00')  ) ;
        $qb->setParameter('endAt',$created_end->format('Y-m-1 00:00:00')  ) ;

        $result = $qb->getQuery()->getSingleResult();

        return ( empty($result)) ? false: true;
    }

}

