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
            ->setOrderAt($params['orderAt'])
            ->setOrderId($params['orderId']);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }


    public function findOneForAudit($id)
    {
        return $this->findOneBy(array(
            'id'=> $id,
            'auditStatus' => GameEggsBreakerTaobaoOrder::AUDIT_STATUS_INIT 
        ));
    }
   
    /**
     *    updateOne for audit
     */
    public function updateOneOnAudit($params)
    {

        $em = $this->getEntityManager();
    }

    /**
     * @param $integer $duration number of day have been pending
     */
    public function fetchPendingOnCron($duration)
    {
        $day = new \DateTime();
        $day->setTime(0,0);
        if( $duration >= 1 ) {
            $day->sub( new \DateInterval('P'. ($duration -1 ).'D'));
        } else {
            $day->add( new \DateInterval('P1D'));
        }

        $qb = $this->createQueryBuilder('o');
        $q = $qb//->select('o.id, o.isValid, o.orderPaid, o.userId')
            ->where('o.auditStatus = :status')
            ->andWhere( $qb->expr()->lt('o.auditPendedAt', ':pendAt') )
            ->orderBy('o.auditPendedAt',' ASC')
            ->setParameter('status', GameEggsBreakerTaobaoOrder::AUDIT_STATUS_PENDING )
            ->setParameter('pendAt',$day )
            ->getQuery();
        $entities = $q->getResult();
        $eggs_info_by_user = array();
        foreach ($entities as $key =>$entity) {
            $user_id = $entity->getUserId();

            if(! isset( $eggs_info_by_user[ $user_id]) ) {
                $eggs_info_by_user[$user_id] = array(
                    'total_paid'=> 0 ,
                    'count_of_uncertain'=> 0,
                    'entities_to_update'=> array() );
            }

            if($entity->isValid()) {
                $eggs_info_by_user[$user_id] ['total_paid'] += $entity->getOrderPaid();
            } elseif( $entity->isUncertain()) {
                $eggs_info_by_user[$user_id] ['count_of_uncertain'] ++;
            }
            $eggs_info_by_user[$user_id] ['entities_to_update'] [] = $entity ;
        }
        unset($entities);
        return $eggs_info_by_user;
    }

    /**
     *    updateOne for audit
     */
    public function updateOneOnCron($id)
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
