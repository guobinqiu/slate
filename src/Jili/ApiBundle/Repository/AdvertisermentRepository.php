<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdvertisermentRepository extends EntityRepository
{
	public function getAdvertiserment($id=null)
	{
		$query = $this->createQueryBuilder('a');
        $query = $query->select('a.id,a.title,a.content,a.imageurl,a.info,ap.type,ap.position');
        $query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
        $query = $query->orderBy('a.id', 'DESC');
        if($id){
        	$query = $query->Where('a.id = :id');
        	$query = $query->setParameter('id',$id);
        }
        $query =  $query->getQuery();
		return $query->getResult();
		
	}
	
	public function getAdvertiserList()
	{
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.content,a.imageurl,a.info,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 1');
		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(9);
		$query =  $query->getQuery();
		return $query->getResult();
	
	}
}