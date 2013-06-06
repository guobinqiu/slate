<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdvertisermentRepository extends EntityRepository
{
	
	public function getAdwAdverList($incentiveType,$id)
	{
		$query = $this->createQueryBuilder('a');
        if($incentiveType==1){
        	$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.info,la.incentive');
        	$query = $query->innerJoin('JiliApiBundle:LimitAd', 'la', 'WITH', 'a.id = la.adId');
        }
        if($incentiveType==2){
        	$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.info,ra.incentiveRate');
        	$query = $query->innerJoin('JiliApiBundle:RateAd', 'ra', 'WITH', 'a.id = ra.adId');
        }
        $query = $query->Where('a.incentiveType = :incentiveType');
        $query = $query->Andwhere('a.id = :id');
        $query = $query->setParameters(array('id'=>$id,'incentiveType'=>$incentiveType));
		$query =  $query->getQuery();
		
		return $query->getResult();
	}
	
// 	public function getCpaAdverList()
// 	{
// 		$query = $this->createQueryBuilder('a');
// 		$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.info,la.incentive');
// 		$query = $query->innerJoin('JiliApiBundle:LimitAd', 'la', 'WITH', 'a.id = la.adId');
		
// 		$query =  $query->getQuery();
// 		return $query->getResult();
// 	}
	

// 	public function getCpsAdverList()
// 	{
// 		$query = $this->createQueryBuilder('a');
// 		$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.info,ra.incentiveRate as incentive');
//         $query = $query->innerJoin('JiliApiBundle:RateAd', 'ra', 'WITH', 'a.id = ra.adId');
// 		$query =  $query->getQuery();
	    
// 		return $query->getResult();
// 	}

	public function getAdvertisermentList()
	{
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.incentiveRate,a.incentive,a.info');
		$query = $query->where('a.deleteFlag = 0');
		$query = $query->orderBy('a.id', 'DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	
	}
	
	
	public function getAdvertiserment($id=null)
	{
		$query = $this->createQueryBuilder('a');
        $query = $query->select('a.id,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.incentiveRate,a.incentive,a.info,ap.type,ap.position');
        $query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
        $query = $query->orderBy('a.id', 'DESC');
        if($id){
        	$query = $query->Where('a.id = :id');
        	$query = $query->setParameter('id',$id);
        }
        $query =  $query->getQuery();
		return $query->getResult();
		
	}
	public function getAdverRecommandList()
	{
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveRate,a.incentive,a.info,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 2');
		$query = $query->orderBy('ap.position');
		$query =  $query->getQuery();
		return $query->getResult();
	
	}
	
	public function getAdvertiserList()
	{
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.content,a.imageurl,a.iconImage,a.listImage,a.incentive,a.incentiveType,a.info,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 1');
		$query = $query->orderBy('ap.position');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(9);
		$query =  $query->getQuery();
		return $query->getResult();
	
	}
}