<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdwAccessHistoryRepository extends EntityRepository
{
	public function getUseradtaste($id,$offset=null,$limit=null)
	{
		$query = $this->createQueryBuilder('ad');
        $query = $query->select('count(ad.id) as num,ad.accessTime,a.title');
        $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ad.adid = a.id');
        $query = $query->Where('ad.userid = :id');
        if($offset && $limit){
        	$query = $query->setFirstResult($offset);
        	$query = $query->setMaxResults($limit);
        }
        $query = $query->setParameter('id',$id);
        $query = $query->getQuery();
		return $query->getResult();
		
	}
	
	/*
	public function getAccessExist($uid,$adid)
	{
		$query = $this->createQueryBuilder('ad')
		->select('ad.id')
		->where('ad.userid = :userid')
		->andWhere('ad.adid = :adid')
		->andwhere('ad.flag = 1')
		->setParameters(array('userid'=>$uid,'adid'=>$adid,))
        ->getQuery();
		return $query->getResult();
		
	}
	*/
	
}