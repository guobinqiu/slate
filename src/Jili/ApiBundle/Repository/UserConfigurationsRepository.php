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
}