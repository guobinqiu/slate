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
}