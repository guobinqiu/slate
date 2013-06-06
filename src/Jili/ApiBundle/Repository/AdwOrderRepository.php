<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdwOrderRepository extends EntityRepository
{
	public function getOrderInfo($userid,$adid,$status=null)
	{
		$parameters = array();
		$query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.orderStatus');
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
		$query = $query->select('ao.createTime,ao.orderStatus,ao.incentiveRate,a.title,a.incentiveType,a.incentive');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ao.adid = a.id');
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
			$query = $query->setFirstResult($option['offset']);
			$query = $query->setMaxResults($option['limit']);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}
	
	
}