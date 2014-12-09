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
     *    updateOne for audit
     */
    public function updateOneOnCron($params)
    {
    }

    /**
     * updateOne when breaking an egg
     */
    public function updateOneOnBreakEgg($params)
    {

        $em = $this->getEntityManager();
    }

    /**
     * TODO: add some filter
     * @param integer $page_no
     * @param integer $page_size
     * @return array array( 'total'=>, 'data') 
     */
    public function fetchByRange( $page_no = 1, $page_size= 10 )
    {

        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT count(o.id) as total  FROM \Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder o');
        $total = $query->getSingleScalarResult();

        $limit = $page_size; 
        $offset = $page_size * ($page_no - 1); 

        $query = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt DESC, o.id', 'DESC')
//            ->orderBy('o.id', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit )
            ->getQuery();
        $rows = $query->getResult();
        return array('total'=> $total, 'data'=> $rows);
    }

} 
