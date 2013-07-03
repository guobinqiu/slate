<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdwOrderRepository extends EntityRepository
{
	public function getOrderNum($aid){
		$query = $this->createQueryBuilder('ao');
		$query = $query->select('ao.id,ao.adid,ao.orderStatus,ao.confirmTime');
		$query = $query->Where('ao.adid = :aid');
		$query = $query->setParameter('aid',$aid);
		$query = $query->getQuery();
		return count($query->getResult());
	}
	
	public function getOrderStatus($uid,$aid){
		$parameters = array();
		$query = $this->createQueryBuilder('ao');
		$query = $query->select('ao.id,ao.orderStatus,ao.confirmTime');
		$query = $query->Where('ao.userid = :id');
		$query = $query->andWhere('ao.adid = :adid');
		$query = $query->andWhere("ao.orderStatus in (3,4)");
		$parameters = array('id'=>$uid,'adid'=>$aid);
		$query = $query->setParameters($parameters);
		$query = $query->getQuery();
		return $query->getResult();
		
	}
	
	public function getOrderInfo($userid,$adid,$status=null)
	{
		$parameters = array();
		$query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.orderStatus,ao.confirmTime');
        $query = $query->Where('ao.userid = :id');
        $query = $query->andWhere('ao.adid = :adid');
        $parameters = array('id'=>$userid,'adid'=>$adid);
        if($status){
        	$query = $query->andWhere('ao.orderStatus = :status');
        	$parameters['status'] = $status;
        }
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
		return $query->getResult();
		
	}
	
	public function getUseradtaste($id,$option=array())
	{
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$monthdate =  date("Y-m-d H:i:s", strtotime(' -6 month'));
		$yeardate =  date("Y-m-d H:i:s", strtotime(' -1 year'));
		$query = $this->createQueryBuilder('ao');
		$query = $query->select('ao.adid,ao.createTime,ao.orderStatus,a.incentiveRate,a.title,a.incentiveType,a.incentive,a.category,ad.displayName');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ao.adid = a.id');
		$query = $query->innerJoin('JiliApiBundle:AdCategory', 'ad', 'WITH', 'a.category = ad.id');
		$query = $query->Where('ao.userid = :id');
		if($option['daytype']){
			switch($option['daytype']){
			    case 0:
			    	break;
			    case 1:
			    	$query = $query->andWhere('ao.createTime > :daydate');
			    	$query = $query->setParameter('daydate',$daydate);
			        break;
			    case 2:
			    	$query = $query->andWhere('ao.createTime > :monthdate');
			    	$query = $query->setParameter('monthdate',$monthdate);
			    	break;    
			    case 3:
			    	$query = $query->andWhere('ao.createTime > :yeardate');
			    	$query = $query->setParameter('yeardate',$yeardate);
			    	break;    
			}
		}
		$query = $query->setParameter('id',$id);
		$query = $query->orderBy('ao.createTime', 'DESC');
		if($option['offset'] && $option['limit']){
			$query = $query->setFirstResult(0);
			$query = $query->setMaxResults(10);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}
	
	
}