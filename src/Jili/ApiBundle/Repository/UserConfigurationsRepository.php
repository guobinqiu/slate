<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class UserConfigurationsRepository extends EntityRepository {

    public function searchUserConfiguration($flagName = null, $user = null) {
        $query = $this->createQueryBuilder('uc');
        $query = $query->select('uc');
        $query = $query->Where('1 = 1');
        $param = array ();
        if ($flagName) {
            $query = $query->andWhere('uc.flagName = :flagName');
            $param['flagName'] = $flagName;
        }
        if ($user) {
            $query = $query->andWhere('uc.user = :user');
            $param['user'] = $user;
        }
        if ($param) {
            $query = $query->setParameters($param);
        }
        $query = $query->getQuery();
        return $query->getResult();
    }
}