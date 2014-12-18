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
            ->setOrderAt( $params['orderAt'] )
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
     * TODO: add some filter
     * @param integer $page_no
     * @param integer $page_size
     * @param array filters 
     * @return array array( 'total'=>, 'data') 
     */
    public function fetchByRange( $page_no = 1, $page_size= 10, $filters = array()  )
    {
        $em = $this->getEntityManager();

        $qb= $this->createQueryBuilder('o');
        $qb->select($qb->expr()->count('o') );

        if (isset( $filters['begin']) && isset($filters['finish']) ) {
            $qb->where($qb->expr()->andX(
                $qb->expr()->gte('o.createdAt',':beginAt') , 
                $qb->expr()->lte('o.createdAt',':finishAt') 
            ) )
            ->setParameter('beginAt', $filters['begin'])
            ->setParameter('finishAt', $filters['finish']);
        }

        $total = $qb->getQuery()->getSingleScalarResult();
        $limit = $page_size; 
        $offset = $page_size * ($page_no - 1); 
        $qb= $this->createQueryBuilder('o');
        $qb->select('u.email,o')
            ->leftJoin('JiliApiBundle:User', 'u', 'WITH', 'o.userId = u.id')
            ->orderBy('o.createdAt DESC, o.id', 'DESC');


        if (isset( $filters['begin']) && isset($filters['finish']) ) {
            $qb->where($qb->expr()->andX(
                $qb->expr()->gte('o.createdAt',':beginAt') , 
                $qb->expr()->lte('o.createdAt',':finishAt') 
            ) )
            ->setParameter('beginAt', $filters['begin'])
            ->setParameter('finishAt', $filters['finish']);
        }
        
        $qb->setFirstResult( $offset )
            ->setMaxResults( $limit );
        $rows = $qb->getQuery()->getResult();
        return array('total'=> $total, 'data'=> $rows);
    }

    /**
     * for eggs sent out
     */
    public function getLastestTimestampEgged()
    {
        $qb = $this->createQueryBuilder('o');
        $q = $qb->select($qb->expr()->max('o.updatedAt') )
            ->where('o.isEgged = :is_egged')
            ->setParameter('is_egged' , GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED )
            ->getQuery();
        return $q->getSingleScalarResult();
    }

    /**
     *
     * @param integer $limit
     */
    public function findLatestEggedNickList( $limit = 10 )
    {

        $qb = $this->createQueryBuilder('o');
        $q = $qb->select('o.userId, o.updatedAt')
            ->where('o.isEgged = :is_egged')
            ->groupBy('o.userId')
            ->orderBy('o.updatedAt','DESC')
            ->setParameter('is_egged', GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED)
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery();
        $result =  $q->getResult();

        if( empty($result) ) {
            return ;
        }

        $orders = array();
        $user_ids = array();
        foreach( $result as $k => $v ) {
           $user_ids [] = $v['userId'];
           $orders[$v['userId']] = $v;
        }
        unset($result);

        // sum of the orderPaid ? 
        $qb = $this->createQueryBuilder('o');
        $q = $qb->select('sum(o.orderPaid) as totalPaid, o.userId')  
               ->groupBy('o.userId')
               ->having($qb->expr()->in('o.userId', $user_ids))
               ->where('o.isEgged = :is_egged')
               ->setParameter('is_egged', GameEggsBreakerTaobaoOrder::IS_EGGED_COMPLETED)
               ->getQuery();
        $result = $q ->getResult();
        $paids = array();
        foreach($result as $k => $v) {
            $paids[$v['userId']] = $v['totalPaid'];
        }
        unset($result);

        // select sum(orderPaid) , user_id from order group by user_id having user_id in ();
        $em = $this->getEntityManager();
        // select nick from user where id in (); 
        $qb = $em->createQueryBuilder();
        $q = $qb->select('u.id, u.nick')
            ->from('JiliApiBundle:User', 'u')
            ->where($qb->expr()->in('u.id', $user_ids))
            ->getQuery();
        $result = $q->getResult();
        $nicks = array();
        foreach($result as $k => $v) {
            $nicks[$v['id']] = $v['nick'];
        }
        unset($result);

        // eggsInfo ? 
        // select * from info where user_id in ();
        $qb = $em->createQueryBuilder();
        $q = $qb->select('i.userId, i.numOfConsolation + i.numOfCommon  as numOfEggs')
            ->from('JiliFrontendBundle:GameEggsBreakerEggsInfo', 'i')
            ->where($qb->expr()->in('i.userId', $user_ids))
            ->getQuery();
        $result = $q->getResult();
        $eggs = array();
        foreach ($result as $k => $v) {
            $eggs[$v['userId']] = $v['numOfEggs'];
        }
        unset($result);

        // merge the results
        $results = array();
        foreach( $orders as $user_id => $order) {
            $results [] = array(
                'nick'=> isset($nicks[$user_id]) ? $nicks[$user_id]: '',
                'paid'=> isset($paids[$user_id]) ? $paids[$user_id]: 0,
                'countOfEggs'=> isset($eggs[$user_id]) ? $eggs[$user_id]: 0,
                'at'=> $order['updatedAt']
            ) ;
        }
        return $results; 
    }

} 
