<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class TaskHistoryRepository extends EntityRepository
{

	public function getUseradtaste($id,$option=array())
	{
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$monthdate =  date("Y-m-d H:i:s", strtotime(' -6 month'));
		$yeardate =  date("Y-m-d H:i:s", strtotime(' -1 year'));
		$query = $this->createQueryBuilder('to');
		$query = $query->select('to.userId,to.orderId,to.date as createTime,to.taskType as type,to.status as orderStatus,to.categoryType as incentiveType,to.point as incentive,to.taskName as title,adc.displayName');
		$query = $query->innerJoin('JiliApiBundle:AdCategory', 'adc', 'WITH', 'to.categoryType = adc.id');
		$query = $query->Where('to.userId = :id');
		if($option['daytype']){
			switch($option['daytype']){
			    case 0:
			    	break;
			    case 1:
			    	$query = $query->andWhere('to.date > :daydate');
			    	$query = $query->setParameter('daydate',$daydate);
			        break;
			    case 2:
			    	$query = $query->andWhere('to.date > :monthdate');
			    	$query = $query->setParameter('monthdate',$monthdate);
			    	break;    
			    case 3:
			    	$query = $query->andWhere('to.date > :yeardate');
			    	$query = $query->setParameter('yeardate',$yeardate);
			    	break;    
			}
		}
		$query = $query->setParameter('id',$id);
		$query = $query->orderBy('to.date', 'DESC');
		if($option['offset'] && $option['limit']){
			$query = $query->setFirstResult(0);
			$query = $query->setMaxResults(10);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}
	public function getUserAdwId($orderId)
	{
		$query = $this->createQueryBuilder('to');
		$query = $query->select('ao.adid');
		$query = $query->innerJoin('JiliApiBundle:AdwOrder', 'ao', 'WITH', 'to.orderId = ao.id');
		$query = $query->Where('to.orderId = :orderId');
		$query = $query->setParameter('orderId',$orderId);
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function getFindOrderId($orderId,$taskType)
	{
		$query = $this->createQueryBuilder('to');
		$query = $query->select('to.id,to.status,to.date');
		$query = $query->innerJoin('JiliApiBundle:AdwOrder', 'ao', 'WITH', 'to.orderId = ao.id');
		$query = $query->Where('to.orderId = :orderId');
		$query = $query->andWhere('to.taskType = :taskType');
		$query = $query->setParameters(array('orderId'=>$orderId,'taskType'=>$taskType));
		$query = $query->getQuery();
		return $query->getResult();

	}

}