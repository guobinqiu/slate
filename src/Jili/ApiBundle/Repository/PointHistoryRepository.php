<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;
use Jili\ApiBundle\EventListener\PointHistory;

class PointHistoryRepository extends EntityRepository
{

    /**
     * @param integer $uid user id
     * @param integer $reason ad_category.id
     * @return array  array(0=> array('id'=> ), 1=>array('id'=>),..) or array()
     **/
    public function issetInsert($uid, $reason = 16)
    {
        $date = new \Datetime();
        $date->setTime(0, 0);
        $nextdate = new \Datetime();
        $nextdate->setTime(0, 0);
        $nextdate->add(new \DateInterval('P1D'));

        $query = $this->createQueryBuilder('ph');
        $query = $query->select('ph.id');
        $query = $query->Where('ph.userId = :uid');
        $query = $query->andWhere('ph.reason = :reason');
        $query = $query->andWhere('ph.createTime > :date');
        $query = $query->andWhere('ph.createTime < :ndate');
        $query = $query->setParameters(array (
            'uid' => $uid,
            'reason' => $reason,
            'date' => $date,
            'ndate' => $nextdate
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function issetInsertReward($uid)
    {
        $query = $this->createQueryBuilder('ph');
        $query = $query->select('ph.id');
        $query = $query->Where('ph.userId = :uid');
        $query = $query->andWhere('ph.reason = 9');
        $query = $query->setParameters(array (
            'uid' => $uid
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * @param array $params = array (
     *      'userid' => 1057622,
     *      'point' => 17,
     *      'type' => 1,
     *    )
     *
     * @return PointHistory Instance
     */
    public function get(array $params = array())
    {
        if (!isset($params['userid']) || $params['userid'] <= 0 || !isset($params['point']) || !isset($params['type'])) {
            return null;
        }
        $po = SequenseEntityClassFactory::createInstance('PointHistory', $params['userid']);
        $em = $this->getEntityManager();
        $po->setUserId($params['userid']);
        $po->setPointChangeNum($params['point']);
        $po->setReason($params['type']);
        $em->persist($po);
        $em->flush();
        return $po;
    }

    public function pointHistorySearch($user_id, $category_id, $start_time, $end_time)
    {
        $param = array ();
        $query = $this->createQueryBuilder('ph');
        $query = $query->select('ph.id, ph.userId, ph.reason, ph.pointChangeNum, ph.createTime');

        $query = $query->Where('ph.userId = :userId');
        $param['userId'] = $user_id;

        if ($category_id) {
            $query = $query->andWhere('ph.reason = :reason');
            $param['reason'] = $category_id;
        }

        if ($start_time) {
            $query = $query->andWhere('ph.createTime >= :start_time');
            $param['start_time'] = $start_time . " 00:00:00";
        }

        if ($end_time) {
            $query = $query->andWhere('ph.createTime <= :end_time');
            $param['end_time'] = $end_time . " 23:59:59";
        }
        $query = $query->setParameters($param);
        $query->orderBy('ph.createTime', 'DESC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * @param integer $user_id
     * @return integer
     */
    public function userPointHistoryCount($user_id)
    {
        $query = $this->createQueryBuilder('ph');
        $query = $query->select('COUNT(ph.id)');
        $query = $query->Where('ph.userId = :userId');
        $param['userId'] = $user_id;
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        $count = $query->getSingleScalarResult();
        return $count;
    }

    /**
     * @param integer $user_id
     * @param integer $pageSize
     * @param integer $currentPage
     * @return array PointHistory
     */
    public function userPointHistorySearch($user_id, $pageSize, $currentPage)
    {
        $query = $this->createQueryBuilder('ph');

        $query = $query->select('ph.id, ph.userId, a.displayName, ph.pointChangeNum, ph.createTime');
        $query = $query->innerJoin('JiliApiBundle:AdCategory', 'a', 'WITH', 'ph.reason = a.id ');

        $query = $query->Where('ph.userId = :userId');

        $param['userId'] = $user_id;
        $query = $query->setParameters($param);

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $query = $query->setFirstResult($pageSize * ($currentPage - 1));
        $query = $query->setMaxResults($pageSize);
        $query->orderBy('ph.id', 'DESC');
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * @param integer $user_id
     * @param PointHistory.id
     * @return total points
     */
    public function userTotalPoint($user_id, $id)
    {
        $query = $this->createQueryBuilder('ph');
        $query = $query->select('sum(ph.pointChangeNum)');
        $query = $query->Where('ph.id <= :id');
        $query = $query->andWhere('ph.userId = :userId');
        $param['id'] = $id;
        $param['userId'] = $user_id;
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        $totalPoint = $query->getSingleScalarResult();
        return $totalPoint;
    }
}
