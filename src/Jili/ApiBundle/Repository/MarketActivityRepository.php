<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class MarketActivityRepository extends EntityRepository
{
	public function nowMall(){
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.aid,a.imageurl,a.title');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ma.aid = a.id');
		$query = $query->Where('ma.deleteFlag is null');
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->groupBy('ma.aid');
		$query = $query->setParameters(array('startTime'=>$date,'endTime'=>$date));
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function nowCate(){
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.categoryId');
		$query = $query->Where('ma.deleteFlag is null');
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->groupBy('ma.categoryId');
		$query = $query->setParameters(array('startTime'=>$date,'endTime'=>$date));
		$query =  $query->getQuery();
		return $query->getResult();
	}


	public function getAllBusinessList()
	{
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.id,ma.aid,ma.businessName,ma.categoryId,ma.activityUrl,ma.activityImage,ma.startTime,ma.endTime');
		$query = $query->Where('ma.deleteFlag is null');
		$query = $query->orderBy('ma.id','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	
	}
	
	public function nowActivity($aid=null){
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.id,ma.aid,ma.businessName,ma.categoryId,ma.activityUrl,ma.activityImage,ma.startTime,ma.endTime,a.imageurl,a.title');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ma.aid = a.id');
		$query = $query->Where('ma.deleteFlag is null');
		if($aid){
			$query = $query->andWhere('ma.aid = :aid');
			$query = $query->setParameter('aid',$aid);
		}
		$query = $query->orderBy('ma.id','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
}