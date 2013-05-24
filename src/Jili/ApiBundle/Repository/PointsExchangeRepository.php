<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class PointsExchangeRepository extends EntityRepository
{
	public function getUserExchange($id,$offset=null,$limit=null)
	{
		$query = $this->createQueryBuilder('p');
        $query = $query->select('p.targetAccount,p.userId,p.sourcePoint,p.targetPoint,p.exchangeDate,pe.type');
    	$query = $query->innerJoin('JiliApiBundle:PointsExchangeType', 'pe', 'WITH', 'p.type = pe.id');
    	$query = $query->Where('p.userId = :id');
    	if($offset && $limit){
    		$query = $query->setFirstResult($offset);
    		$query = $query->setMaxResults($limit);
    	}
    	$query = $query->setParameter('id',$id);
    	$query = $query->getQuery();
		return $query->getResult();
		
	}
}