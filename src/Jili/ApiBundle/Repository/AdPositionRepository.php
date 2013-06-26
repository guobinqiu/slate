<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class AdPositionRepository extends EntityRepository
{
	public function getAdPosition($type)
	{
		$query = $this->createQueryBuilder('ap');
	
		$query = $query->select('ap.position,ap.adId');
	
		$query = $query->Where('ap.type = :type');
		$query = $query->setParameter('type',$type);
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
	
	public function getInfoPosition($id)
	{
		$query = $this->createQueryBuilder('ap');
		$query = $query->select('ap.id as aid,ap.type,ap.position,ap.adId,a.id,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.incentiveRate,a.incentive,a.info');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ap.adId = a.id ');
		$query = $query->Where('ap.id = :id');
		$query = $query->andWhere('a.deleteFlag = 0');
		$query = $query->setParameter('id',$id);
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
}