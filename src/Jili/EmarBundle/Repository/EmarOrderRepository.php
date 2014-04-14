<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 *
 * only cps order in emar_order 
 */
class EmarOrderRepository extends EntityRepository
{
    /**
     * @abstract: 取得点击过(clicked)的emar_order
     * @param: $params array( user_id , ad_id )
     */
	public function findOneCpsOrderInit($params){

        $parameters = array('user_id'=>$params['user_id'],
            'ad_id'=>$params['ad_id'],
            'ad_type'=> $params['ad_type'],
            'delete_flag'=> $params['delete_flag'],
            'status'=> $params['status']);

        $query = $this->createQueryBuilder('ao')
            ->select('ao')
            ->where('ao.adId = :ad_id')
            ->andWhere('ao.adType = :ad_type')
            ->andWhere('ao.ocd IS NULL')
            ->andWhere('ao.deleteFlag = :delete_flag')
            ->andWhere('ao.status= :status')
            ->andWhere('ao.userId = :user_id')
            ->setParameters($parameters)
            ->getQuery();

		return $query->getOneOrNullResult();
    }

    /**
     * @abstract: 取得参加过(init)的emar_order
     * @param: $params array( user_id , ad_id )
     */
	public function findOneCpsOrderJoined($params){
        $parameters = array('user_id'=>$params['user_id'],
            'ad_id'=>$params['ad_id'],
            'ad_type'=>$params['ad_type'],
            'ocd'=> $params['ocd'],
            'status'=> $params['status'],
            'delete_flag'=> $params['delete_flag']);

        $query = $this->createQueryBuilder('ao')
            ->select('ao')
            ->where('ao.adId = :ad_id')
            ->andWhere('ao.adType = :ad_type')
            ->andWhere('ao.ocd = :ocd')
            ->andWhere('ao.deleteFlag = :delete_flag')
            ->andWhere('ao.userId = :user_id')
            ->andWhere('ao.status= :status')
            ->setParameters($parameters)
            ->getQuery();

		return $query->getOneOrNullResult();
    }

}
