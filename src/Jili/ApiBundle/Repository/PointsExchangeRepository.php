<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class PointsExchangeRepository extends EntityRepository
{
	public function exList(){
		$query = $this->createQueryBuilder('p');
		$query = $query->select('p.id,p.userId,u.nick,u.wenwenUser,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pt.type');
		$query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
		$query = $query->Where('p.status=1');
		$query = $query->orderBy('p.id','DESC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(10);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function exchangeInfo($start,$end){
		if($start)
			$start_time = $start.' 00:00:00';
		if($end)
			$end_time = $end.' 23:59:59';
		$query = $this->createQueryBuilder('p');
		$query = $query->select('p.id,p.userId,u.email,u.wenwenUser,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pt.type');
		$query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
		if($start && $end){
			$query = $query->Where('p.exchangeDate>=:start_time');
			$query = $query->andWhere('p.exchangeDate<=:end_time');
			$query = $query->setParameters(array('start_time'=>$start_time,'end_time'=>$end_time));
		}
		if($start && !$end){
			$query = $query->Where('p.exchangeDate>=:start_time');
			$query = $query->setParameter('start_time',$start_time);
		}
		if(!$start && $end){
			$query = $query->Where('p.exchangeDate<=:end_time');
			$query = $query->setParameter('end_time',$end_time);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getExDateInfo($start,$end){
		if($start)
			$start_time = $start.' 00:00:00';
		if($end)
			$end_time = $end.' 23:59:59';
		$query = $this->createQueryBuilder('p');
		$query = $query->select('p.id,p.userId,u.email,u.wenwenUser,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pt.type');
		$query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pt', 'WITH', 'p.type = pt.id');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
		if($start && $end){
			$query = $query->Where('p.exchangeDate>=:start_time');
			$query = $query->andWhere('p.exchangeDate<=:end_time');
			$query = $query->setParameters(array('start_time'=>$start_time,'end_time'=>$end_time));
		}
		if($start && !$end){
			$query = $query->Where('p.exchangeDate>=:start_time');
			$query = $query->setParameter('start_time',$start_time);
		}
		if(!$start && $end){
			$query = $query->Where('p.exchangeDate<=:end_time');
			$query = $query->setParameter('end_time',$end_time);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}
	
	public function existUserExchange($id)
	{
		$query = $this->createQueryBuilder('p');
		$query = $query->select('p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,p.finishDate,p.status');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
		$query = $query->Where('p.userId = :id');
		$query = $query->setParameter('id',$id);
		$query = $query->getQuery();
		return $query->getResult();
	}
	
	public function getExchangeStatus($id)
	{
		$query = $this->createQueryBuilder('p');
		$query = $query->select('p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,p.finishDate,p.status');
		$query = $query->innerJoin('JiliApiBundle:User', 'u', 'WITH', 'p.userId = u.id');
		$query = $query->Where('p.userId = :id');
		$query = $query->andWhere('p.status = 1');
		$query = $query->setParameter('id',$id);
		$query = $query->getQuery();
		return $query->getResult();
	}
	public function getUserExchange($id,$option=array())
	{
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$monthdate =  date("Y-m-d H:i:s", strtotime(' -6 month'));
		$yeardate =  date("Y-m-d H:i:s", strtotime(' -1 year'));
		$query = $this->createQueryBuilder('p');
        $query = $query->select('p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,p.finishDate,p.status,pe.type');
    	$query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pe', 'WITH', 'p.type = pe.id');
    	$query = $query->Where('p.userId = :id');
    	if($option['daytype']){
    		switch($option['daytype']){
    		    case 0:
    		    	break;
    		    case 1:
    		    	$query = $query->andWhere('p.exchangeDate > :daydate');
    		    	$query = $query->setParameter('daydate',$daydate);
    		    	break;
    		    case 2:
    		    	$query = $query->andWhere('p.exchangeDate > :monthdate');
    		    	$query = $query->setParameter('monthdate',$monthdate);
    		    	break;
    		    case 3:
    		    	$query = $query->andWhere('p.exchangeDate > :yeardate');
    		    	$query = $query->setParameter('yeardate',$yeardate);
    		    	break;
    		}
    	}
    	$query = $query->setParameter('id',$id);
    	$query = $query->orderBy('p.exchangeDate', 'DESC');
    	if($option['offset'] && $option['limit']){
    		$query = $query->setFirstResult(0);
			$query = $query->setMaxResults(10);
    	}
    	$query = $query->getQuery();
		return $query->getResult();
		
	}
}