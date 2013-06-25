<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class PointsExchangeRepository extends EntityRepository
{
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
    		$query = $query->setFirstResult($option['offset']);
    		$query = $query->setMaxResults($option['limit']);
    	}
    	$query = $query->getQuery();
		return $query->getResult();
		
	}
}