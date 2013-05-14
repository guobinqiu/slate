<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdwAccessRecordRepository extends EntityRepository
{
	public function getUseradtaste($id,$offset=0,$limit=10)
	{
		$query = $this->createQueryBuilder('ad')
        ->select('count(ad.id) as num,ad.action,ad.time,a.title')
        ->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ad.adid = a.id')
        ->Where('ad.userid = :id')
        ->setFirstResult($offset)
		->setMaxResults($limit)
        ->setParameter('id',$id)
        ->getQuery();
		return $query->getResult();
		
	}
	
}