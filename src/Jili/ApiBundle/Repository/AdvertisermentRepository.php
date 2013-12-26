<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdvertisermentRepository extends EntityRepository {
	public function getAdvCate() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.type');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 5 or ap.type = 4');
		$query = $query->andWhere('a.deleteFlag = 0');
		$query = $query->andWhere('ap.position <> 0');
		$query = $query->getQuery();
		return $query->getResult();
	}
	public function getSearchAd($title) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.startTime,a.endTime,a.decription,a.content,a.incentive,a.info');
		//$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('a.deleteFlag = 0');
		$query = $query->andWhere("a.title LIKE :title");
		$query = $query->setParameter('title', '%' . $title . '%');
		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(10);
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getAllCps() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.startTime,a.endTime,a.decription,a.content,a.incentive,a.info');
		$query = $query->Where('a.deleteFlag = 0');
		$query = $query->andWhere("a.incentiveType = 2");
		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getCpsSearchAd($title) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.startTime,a.endTime,a.decription,a.content,a.incentive,a.info');
		$query = $query->Where('a.deleteFlag = 0');
		if ($title) {
			$query = $query->andWhere("a.title LIKE :title");
			$query = $query->setParameter('title', '%' . $title . '%');
		}
		$query = $query->andWhere("a.incentiveType = 2");

		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->getQuery();
		return $query->getResult();
	}

	public function getAdwAdverList($incentiveType, $id) {
		$query = $this->createQueryBuilder('a');
		if ($incentiveType == 1) {
			$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.info,la.incentive');
			$query = $query->innerJoin('JiliApiBundle:LimitAd', 'la', 'WITH', 'a.id = la.adId');
		}
		if ($incentiveType == 2) {
			$query = $query->select('a.id,a.title,a.content,a.endTime,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.rewardRate,a.info,a.incentiveRate');
			$query = $query->innerJoin('JiliApiBundle:RateAd', 'ra', 'WITH', 'a.id = ra.adId');
		}
		$query = $query->Where('a.incentiveType = :incentiveType');
		$query = $query->Andwhere('a.id = :id');
		$query = $query->setParameters(array (
			'id' => $id,
			'incentiveType' => $incentiveType
		));
		$query = $query->getQuery();

		return $query->getResult();
	}

	public function getAdvertisermentList($type = null) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.startTime,a.endTime,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.incentiveRate,a.incentive,a.info');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 1');
		if ($type) {
			$query = $query->andWhere('a.incentiveType = :type');
			$query = $query->setParameter('type', $type);
		}
		$query = $query->andWhere('a.deleteFlag = 0');
		$query = $query->orderBy('ap.position', 'ASC');
		$query = $query->getQuery();
		return $query->getResult();

	}

	public function getAdvertiserment($id = null) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentiveType,a.incentiveRate,a.incentive,a.info,ap.id as aid,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('a.deleteFlag = 0');
		if ($id) {
			$query = $query->andWhere('a.id = :id');
			$query = $query->setParameter('id', $id);
		}
		$query = $query->andWhere('ap.position <> 0');
		$query = $query->orderBy('ap.id', 'DESC');
		$query = $query->getQuery();
		return $query->getResult();

	}

	//获取广告列表
	public function getAllAdvertiserList() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.endTime,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentive,a.incentiveType,a.incentiveRate,a.info');
		$query = $query->Where('a.deleteFlag = 0');
		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->getQuery();
		return $query->getResult();

	}

	//搜索标题
	public function searchTitle($title) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.endTime,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentive,a.incentiveType,a.incentiveRate,a.info');
		$query = $query->Where('a.deleteFlag = 0');
		$query = $query->andWhere('a.title like :title');
		$query = $query->orderBy('a.id', 'DESC');
		$query = $query->setParameter('title', '%' . $title . '%');
		$query = $query->getQuery();
		return $query->getResult();

	}

	//获取广告位置列表, 首页热门商家
	public function getAdvertiserAreaList($area, $limit = null) {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.type as cate,a.title,a.decription,a.content,a.imageurl,a.iconImage,a.listImage,a.incentive,a.incentiveType,a.incentiveRate,a.rewardRate,a.info,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		if ($area == 5) {
			$query = $query->Where('ap.type = :area or ap.type = 4');
		} else {
			$query = $query->Where('ap.type = :area');
		}
		$query = $query->andWhere('a.deleteFlag = 0');
		$query = $query->andWhere('ap.position <> 0');
		$query = $query->orderBy('ap.position');
		$query = $query->setParameter('area', $area);
		if (!$limit) {
			$query = $query->setFirstResult(0);
			$query = $query->setMaxResults($limit);
		}
		$query = $query->getQuery();
		return $query->getResult();

	}

	//首页广告
	public function getAdvertiserList() {
		$query = $this->createQueryBuilder('a');
		$query = $query->select('a.id,a.title,a.content,a.imageurl,a.iconImage,a.listImage,a.incentive,a.incentiveType,a.incentiveRate,a.rewardRate,a.info,ap.type,ap.position');
		$query = $query->innerJoin('JiliApiBundle:AdPosition', 'ap', 'WITH', 'a.id = ap.adId');
		$query = $query->Where('ap.type = 1');
		$query = $query->andWhere('a.deleteFlag = 0');
		$query = $query->andWhere('ap.position <> 0');
		$query = $query->orderBy('ap.position');
		$query = $query->setFirstResult(0);
		$query = $query->setMaxResults(9);
		$query = $query->getQuery();
		return $query->getResult();

	}

	//首页 可以做的任务 cpa
	public function getAdvertiserListCPA($user_id) {
		$sql = "SELECT id, title, incentive
	                FROM advertiserment
	                WHERE incentive_type = 1
	                AND id NOT
	                IN (
	                SELECT ad_id AS id
	                FROM adw_order
	                WHERE user_id = " . $user_id . "
	                AND order_status > 1
	                )";
		return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
	}
}