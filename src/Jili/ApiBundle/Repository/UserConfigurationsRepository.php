<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserConfigurationsRepository extends EntityRepository {

    public function searchUserConfiguration($flagName = null, $userId = null) {
        $query = $this->createQueryBuilder('uc');
        $query = $query->select('uc');
        $query = $query->Where('1 = 1');
        $param = array ();
        if ($flagName) {
            $query = $query->andWhere('uc.flagName = :flagName');
            $param['flagName'] = $flagName;
        }
        if ($userId) {
            $query = $query->andWhere('uc.userId = :userId');
            $param['userId'] = $userId;
        }
        if ($param) {
            $query = $query->setParameters($param);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * @param  integer $user_id 
     * return boolean true is autocheckin; false not autocheckin; null the record is exist set yet.
     */
    public function isAutoCheckin($user_id) 
    {
        $r = $this->findOneBy(array('flagName'=> 'auto_checkin','userId'=>$user_id));
        if( $r === NULL) {
            return NULL;
        }
        return (boolean) $r->getFlagData();
    }
}
