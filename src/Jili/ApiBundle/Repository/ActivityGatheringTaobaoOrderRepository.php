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
    public function isCheckedCurrentYearMonth($params)
    {
        $em = $this->getEntityManager();
        $q = $em->createQuery('select count(o) from JiliApiBundle:ActivityGatheringTaobaoOrder o 
            Where IDENTITY(o.user) = :user_id and o.createdAt >= :start_at and o.createdAt < :end_at')
            ->setParameters(array('user_id'=> $params['userId'],
                'start_at'=>date('2015-02-01 00:00:00'),
            'end_at'=> date('2015-03-01 00:00:00')));

        $result =  (int) $q->getSingleScalarResult();
        return ( 0 === $result) ? false : true;
    }

}

