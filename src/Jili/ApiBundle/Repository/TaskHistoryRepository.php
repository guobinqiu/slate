<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class TaskHistoryRepository extends EntityRepository
{
	public function getTaskPercent($orderId){
		$query = $this->createQueryBuilder('to');
		$query = $query->select('to.rewardPercent');
		$query = $query->Where('to.orderId = :orderId');
		$query = $query->andWhere('to.taskType = 1');
		$query = $query->setParameter('orderId',$orderId);
		$query = $query->getQuery();
		return $query->getResult();
	}
	public function getUseradtaste($id,$option=array())
	{
		$query = $this->createQueryBuilder('to');
		$query = $query->select('to.userId,to.orderId,to.date as createTime,to.taskType as type,to.status as orderStatus,to.categoryType as incentiveType,to.point as incentive,to.taskName as title,adc.displayName');
		$query = $query->innerJoin('JiliApiBundle:AdCategory', 'adc', 'WITH', 'to.categoryType = adc.id');
		$query = $query->Where('to.userId = :id');
		if(isset($option['status']) && $option['status']){
			switch($option['status']){
			    case 0:
			    	break;	
			    case 1:
			    	$query = $query->andWhere('(to.status = 2  and to.taskType = 1) or to.status = 0');
			        break;
			    case 2:
			    	$query = $query->andWhere('to.status = 3 or (to.status = 1 and to.taskType > 1)');
			    	break;    
			    case 3:
			    	$query = $query->andWhere('to.status = 4 or (to.status = 2 and to.taskType > 1)');
			    	break;
			}
		}
	 	$query = $query->setParameter('id',$id);
		$query = $query->orderBy('to.date', 'DESC');
		if(isset($option['offset']) && $option['offset'] && isset($option['limit']) && $option['limit']){
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
		if($taskType==1)
			$query = $query->innerJoin('JiliApiBundle:AdwOrder', 'ao', 'WITH', 'to.orderId = ao.id');
		if($taskType==2)
			$query = $query->innerJoin('JiliApiBundle:PagOrder', 'po', 'WITH', 'to.orderId = po.id');
		$query = $query->Where('to.orderId = :orderId');
		$query = $query->andWhere('to.taskType = :taskType');
		$query = $query->setParameters(array('orderId'=>$orderId,'taskType'=>$taskType));
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getConfirmPoints($userid)
	{
		$query = $this->createQueryBuilder('to');
		$query = $query->select('sum(to.point)');
		$query = $query->Where('to.userId = :userId');
		$query = $query->andWhere('to.categoryType in (1,2,17, 19)');
		$query = $query->andWhere('to.status = 2');
		$query = $query->setParameter('userId',$userid);
		$query = $query->getQuery();
		return $query->getSingleScalarResult()?$query->getSingleScalarResult():0;
	}

}
