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
        $em = $this->getEntityManager();
        $entity =  $this->findOneBy(array(
            'user'=> $em->getReference('Jili\\ApiBundle\\Entity\\User', $params['userId']),
        ));
        return ( is_null($entity)) ? false: true;
    }

}

