<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class TaskHistoryRepository extends EntityRepository
{
    public function getTaskPercent($orderId)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('to.rewardPercent');
        $query = $query->Where('to.orderId = :orderId');
        $query = $query->andWhere('to.taskType = 1');
        $query = $query->setParameter('orderId',$orderId);
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     *@param $user_id the userId
     *@param $status 0|1|2|3
     */
    public function getTaskHistoryCount($user_id, $status = 0)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('COUNT(to.id)');
        $query = $this->getTaskHistoryCondition($query, $user_id, $status);
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     *@param $user_id the userId
     *@param $query
     */
    public function getTaskHistoryCondition($query, $user_id, $status = 0)
    {
        $query = $query->innerJoin('JiliApiBundle:AdCategory', 'adc', 'WITH', 'to.categoryType = adc.id');
        $query = $query->Where('to.userId = :userId');
        if (isset($status) && $status) {
            switch ($status) {
                case 0 :
                    break;
                case 1 :
                    $query = $query->andWhere('(to.status = 2  and to.taskType = 1) or to.status = 0');
                    break;
                case 2 :
                    $query = $query->andWhere('to.status = 3 or (to.status = 1 and to.taskType > 1)');
                    break;
                case 3 :
                    $query = $query->andWhere('to.status = 4 or (to.status = 2 and to.taskType > 1)');
                    break;
            }
        }
        $query = $query->setParameter('userId', $user_id);
        return $query;
    }

    /**
     *@param $id the userId
     *@param $status 0|1|2|3
     *@param $page_size
     *@param $page
     */
    public function getUseradtaste($user_id, $status=0, $page = 1, $page_size = 10)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('to.userId,to.orderId,to.date as createTime,to.taskType as type,to.status as orderStatus,to.categoryType as incentiveType,to.point as incentive,to.taskName as title,adc.displayName');
        $query = $this->getTaskHistoryCondition($query, $user_id, $status);
        $query = $query->orderBy('to.date', 'DESC');

        if ((int) $page < 1) {
            $page = 1;
        }
        $query = $query->setFirstResult($page_size * ($page - 1));
        $query = $query->setMaxResults($page_size);

        $query = $query->getQuery();
        return $query->getResult();
    }
    public function getUserAdwId($orderId)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('ao.adid');
        $query = $query->innerJoin('JiliApiBundle:AdwOrder', 'ao', 'WITH', 'to.orderId = ao.id');
        $query = $query->Where('to.orderId = :orderId');
        $query = $query->setParameter('orderId',$orderId);
        $query = $query->getQuery();
        return $query->getResult();

    }

    public function getFindOrderId($orderId,$taskType)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('to.id,to.status,to.date');
        if($taskType==1)
            $query = $query->innerJoin('JiliApiBundle:AdwOrder', 'ao', 'WITH', 'to.orderId = ao.id');
        if($taskType==2)
            $query = $query->innerJoin('JiliApiBundle:PagOrder', 'po', 'WITH', 'to.orderId = po.id');
        $query = $query->Where('to.orderId = :orderId');
        $query = $query->andWhere('to.taskType = :taskType');
        $query = $query->setParameters(array('orderId'=>$orderId,'taskType'=>$taskType));
        $query = $query->getQuery();
        return $query->getResult();
    }

    public function getConfirmPoints($userid)
    {
        $query = $this->createQueryBuilder('to');
        $query = $query->select('sum(to.point)');
        $query = $query->Where('to.userId = :userId');
        $query = $query->andWhere('to.categoryType in (1,2,17,19)');
        $query = $query->andWhere('to.status = 2');
        $query = $query->setParameter('userId',$userid);
        $query = $query->getQuery();
        return $query->getSingleScalarResult()?$query->getSingleScalarResult():0;
    }

    /**
     * @param array $params array()
     * @return TaskHistory instance
     */
    public function init( array $params=array())
    {
        $po  = SequenseEntityClassFactory::createInstance('TaskHistory', $params['userid']);
        $po->setUserid($params['userid'])
            ->setOrderId($params['orderId'])
            ->setOcdCreatedDate($params['date'])
            ->setCategoryType($params['categoryType'])
            ->setTaskType($params['taskType'])
            ->setTaskName(trim($params['task_name']))
            ->setDate($params['date'])
            ->setPoint( $params['point'])
            ->setStatus($params['status']);
        if(isset($params['reward_percent'])) {
            $po->setRewardPercent( $params['reward_percent']);
        }

        $em = $this->getEntityManager();
        $em->persist($po);
        $em->flush();
        return $po;
    }


    /**
     *  private function updateTaskHistory($params=array()) {
     *  @param: $params   'userId' => 1057622,
     *                     'orderId' => 2,
     *                     'taskType' => 1,
     *                     'rewardPercent' => '',
     *                     'point' => 17,
     *                     'date' =>
     *                     DateTime::__set_state(array(
     *                        'date' => '2014-01-03 13:46:23',
     *                        'timezone_type' => 3,
     *                        'timezone' => 'Asia/Hong_Kong',
     *                     )),
     *                     'status' => 4,
     * @return null
     */
    public function update( array $params=array())
    {

        $flag =  $params['userId'] % 10;

        $sql = 'UPDATE JiliApiBundle:TaskHistory0'. $flag.' t';
        $sql .= ' SET t.status= :status, t.point =:point';
        if( isset($params['rewardPercent']) ) {
            $sql .=', t.rewardPercent= :rewardPercent';
        }
        if( isset($params['date']) ) {
            $sql .= ', t.date = :date ';
        }

        $sql .= ' WHERE t.userId = :userId and t.orderId = :orderId and t.categoryType =:categoryType and t.taskType = :taskType and t.status = :statusPrevious';

        $em = $this->getEntityManager();
        $q_update = $em->createQuery($sql);
        $q_update->setParameters( $params);
        return  $q_update->execute();
    }

}
