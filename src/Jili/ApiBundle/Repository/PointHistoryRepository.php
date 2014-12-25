<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;


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
        $date->setTime(0,0);
        $nextdate = new \Datetime();
        $nextdate->setTime(0,0);
        $nextdate->add(new \DateInterval('P1D'));

        $query = $this->createQueryBuilder('ph');
        $query = $query->select('ph.id');
        $query = $query->Where('ph.userId = :uid');
        $query = $query->andWhere('ph.reason = :reason');
        $query = $query->andWhere('ph.createTime > :date');
        $query = $query->andWhere('ph.createTime < :ndate');
        $query = $query->setParameters(array('uid'=>$uid, 'reason'=> $reason, 'date'=>$date,'ndate'=>$nextdate));
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

    /**
     * @param array $params = array (
     *      'userid' => 1057622,
     *      'point' => 17,
     *      'type' => 1,
     *    )
     *
     * @return PointHistory Instance
     */
    public function get(array $params = array() )
    {
        if( ! isset($params['userid'] ) || $params['userid'] <= 0 
            || ! isset($params['point']) || ! isset($params['type']) ) {
                return null;
            }
        $po  = SequenseEntityClassFactory::createInstance('PointHistory', $params['userid']);
        $em = $this->getEntityManager();
        $po->setUserId($params['userid']);
        $po->setPointChangeNum($params['point']);
        $po->setReason($params['type']);
        $em->persist($po);
        $em->flush();
        return $po;
    }

    /**
     * @return boolean 
     */
    public function isGameSeekerCompletedToday ($uid) {
        $gameSeekerCategoryId = \Jili\ApiBundle\Entity\AdCategory::ID_GAME_SEEKER; 
        $pointLog =  $this->issetInsert( $uid, $gameSeekerCategoryId) ;
        return (empty($pointLog )) ? false: true;
    }
}
