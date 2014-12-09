<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;

class GameEggsBreakerTaobaoOrderRepository extends EntityRepository 
{

    /**
     * @param array userId, order
     * @return GameEggsBreakerTaobaoOrder
     */
    public function insertUserPost($params)
    {
        $em = $this->getEntityManager();
        $entity  = new GameEggsBreakerTaobaoOrder();
        $entity->setUserId($params['userId'])
            ->setOrderPaid($params['orderPaid'])
            ->setOrderId($params['orderId']);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    /**
     *    updateOne for audit
     */
    public function updateOneOnAudit($params)
    {
        $em = $this->getEntityManager();
    }


    /**
     * updateOne when breaking an egg
     */
    public function updateOneOnBreakEgg($params)
    {

        $em = $this->getEntityManager();
    }

} 
