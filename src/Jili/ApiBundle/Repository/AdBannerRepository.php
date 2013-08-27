<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class AdBannerRepository extends EntityRepository
{
	public function getBannerPosition($position_id,$id)
	{
		$query = $this->createQueryBuilder('ab');
		$query = $query->select('ab.id,ab.position');
		$query = $query->Where('ab.position = :position_id');
		$query = $query->andWhere('ab.id <> :id');
		$query = $query->setParameters(array('id'=>$id,'position_id'=>$position_id));
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
	
	public function getInfoBanner()
	{
		$query = $this->createQueryBuilder('ab');
		$query = $query->select('ab.id,ab.iconImage,ab.adUrl,ab.position,ab.createTime');
		$query = $query->orderBy('ab.position','ASC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(6);
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function getInfoAdminBanner()
	{
		$query = $this->createQueryBuilder('ab');
		$query = $query->select('ab.id,ab.iconImage,ab.adUrl,ab.position,ab.createTime');
		$query = $query->orderBy('ab.position','ASC');
		$query =  $query->getQuery();
		return $query->getResult();
	}
	
}