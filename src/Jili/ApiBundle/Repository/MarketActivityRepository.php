<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MarketActivityRepository extends EntityRepository {
	public function nowMall() {
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.aid,a.imageurl,a.title');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ma.aid = a.id');
		$query = $query->Where('ma.deleteFlag is null');
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->groupBy('ma.aid');
		$query = $query->setParameters(array (
			'startTime' => $date,
			'endTime' => $date
		));
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function existMarket($id) {
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.aid,ma.activityUrl');
		$query = $query->Where('ma.deleteFlag IS NULL  OR ma.deleteFlag = 0');
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->andWhere('ma.id = :id');
		$query = $query->setParameters(array (
			'startTime' => $date,
			'endTime' => $date,
			'id' => $id
		));
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function nowCate($mallId = null) {
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.categoryId');
		$query = $query->Where('ma.deleteFlag IS NULL  OR ma.deleteFlag = 0');
		if ($mallId) {
			$query = $query->andWhere("ma.aid = :aid");
			$parameters['aid'] = $mallId;
		}
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->groupBy('ma.categoryId');
		$parameters['startTime'] = $date;
		$parameters['endTime'] = $date;
		$query = $query->setParameters($parameters);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getAllBusinessList() {
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.id,ma.aid,ma.businessName,ma.categoryId,ma.activityUrl,ma.activityImage,ma.startTime,ma.endTime');
		$query = $query->Where('ma.deleteFlag IS NULL  OR ma.deleteFlag = 0');
		$query = $query->orderBy('ma.id', 'DESC');
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function nowActivity($aid = null) {
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.id,ma.aid,ma.businessName,ma.categoryId,ma.activityUrl,ma.activityImage,ma.startTime,ma.endTime,a.imageurl,a.title');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ma.aid = a.id');
		$query = $query->Where('ma.deleteFlag IS NULL  OR ma.deleteFlag = 0');
		if ($aid) {
			$query = $query->andWhere('ma.aid = :aid');
			$parameters['aid'] = $aid;
		}
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->orderBy('ma.startTime', 'DESC');
		$parameters['startTime'] = $date;
		$parameters['endTime'] = $date;
		$query = $query->setParameters($parameters);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getActivityList($limit) {
		$date = date('Y-m-d H:i:s');
		$query = $this->createQueryBuilder('ma');
		$query = $query->select('ma.id,ma.aid,ma.businessName,ma.categoryId,ma.activityUrl,ma.activityImage,ma.startTime,ma.endTime,a.imageurl,a.title');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ma.aid = a.id');
		$query = $query->Where('ma.deleteFlag IS NULL  OR ma.deleteFlag = 0');
		$query = $query->andWhere('ma.startTime <= :startTime');
		$query = $query->andWhere('ma.endTime >= :endTime');
		$query = $query->orderBy('ma.startTime', 'DESC');
		$parameters['startTime'] = $date;
		$parameters['endTime'] = $date;
		$query = $query->setParameters($parameters);
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults($limit);
		$query = $query->getQuery();
		return $query->getResult();
	}

}
