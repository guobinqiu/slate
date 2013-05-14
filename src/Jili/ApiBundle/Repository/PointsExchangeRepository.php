<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class PointsExchangeRepository extends EntityRepository
{
	public function getUserExchange($id)
	{
		$query = $this->createQueryBuilder('p')
            	->select('p.account,p.userId,p.point,p.exchangedPoint,p.exchangeDate,pe.type')
            	->innerJoin('JiliApiBundle:PointsExchangeType', 'pe', 'WITH', 'p.type = pe.id')
            	->Where('p.userId = :id')
            	->setParameter('id',$id)
            	->getQuery();
		return $query->getResult();
		
	}
}