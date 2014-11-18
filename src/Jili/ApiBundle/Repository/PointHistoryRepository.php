<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class PointHistoryRepository extends EntityRepository
{
    /**
     * @param integer $uid user id 
     * @param integer $reason ad_category.id 
     * @return array  array(0=> array('id'=> ), 1=>array('id'=>),..) or array()
     **/
    public function issetInsert($uid, $reason = 16)
    {
        $date = date('Y-m-d');
        $nextdate = date("Y-m-d",strtotime('+1 day'));
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
     * @return boolean 
     */
    public function isGameSeekerCompletedToday ($uid) {
        $gameSeekerCategoryId = \Jili\ApiBundle\Entity\AdCategory::ID_GAME_SEEKER; 
        $pointLog =  $this->issetInsert( $uid, $gameSeekerCategoryId) ;
        return (empty($pointLog )) ? false: true;
    }
}
