<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class CheckinPointTimesRepository extends EntityRepository
{
	public function getCheckinTimes($checkinType)
	{
		$date = date("Y-m-d H:i:s");
		$query = $this->createQueryBuilder('cpt');
		$query = $query->select('cpt.pointTimes');
		$query = $query->Where('cpt.startTime <= :start');
		$query = $query->andWhere('cpt.endTime >= :end');
		$query = $query->andWhere('cpt.checkinType = :checkinType');
		$query = $query->orderBy('cpt.id','DESC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(1);
		$parameters = array('start'=>$date,'end'=>$date,'checkinType'=>$checkinType);
		$query = $query->setParameters($parameters);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getAllCheckinPoint()
	{
		$query = $this->createQueryBuilder('cpt');
		$query = $query->select('cpt.id,cpt.startTime,cpt.endTime,cpt.pointTimes,cpt.checkinType,cpt.createTime');
		$query = $query->orderBy('cpt.createTime','DESC');
		$query =  $query->getQuery();
		return $query->getResult();
	}

	public function checkDate($startTime,$endTime,$checkInType)
	{
		$query = $this->createQueryBuilder('cpt');
		$query = $query->select('cpt.id,cpt.startTime,cpt.endTime,cpt.pointTimes,cpt.checkinType,cpt.createTime');
		$query = $query->Where('cpt.checkinType = :checkInType and cpt.startTime <= :start');
		$query = $query->andWhere('cpt.endTime >= :start or (cpt.startTime <= :end and cpt.endTime >= :end)');
		$parameters = array('start'=>$startTime,'end'=>$endTime,'checkInType'=>$checkInType);
		$query = $query->setParameters($parameters);
		$query =  $query->getQuery();
		return $query->getResult();
	}

}