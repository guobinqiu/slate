<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class PointHistoryRepository extends EntityRepository
{
	public function issetInsert($uid)
	{
		$date = date('Y-m-d');
		$nextdate = date("Y-m-d",strtotime("+1 day"));
		$query = $this->createQueryBuilder('ph');
		$query = $query->select('ph.id');
		$query = $query->Where('ph.userId = :uid');
		$query = $query->andWhere('ph.reason = 16');
		$query = $query->andWhere('ph.createTime > :date');
		$query = $query->andWhere('ph.createTime < :ndate');
		$query = $query->setParameters(array('uid'=>$uid,'date'=>$date,'ndate'=>$nextdate));
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function issetInsertReward($uid)
	{
		$query = $this->createQueryBuilder('ph');
		$query = $query->select('ph.id');
		$query = $query->Where('ph.userId = :uid');
		$query = $query->andWhere('ph.reason = 9');
		$query = $query->setParameters(array('uid'=>$uid));
		$query =  $query->getQuery();
		return $query->getResult();
	}

}