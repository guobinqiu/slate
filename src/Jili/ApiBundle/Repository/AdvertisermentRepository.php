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
        if($id){
        	$query = $query->Where('a.id = :id');
        	$query = $query->setParameter('id',$id);
        }
        $query =  $query->getQuery();
		return $query->getResult();
		
	}
}