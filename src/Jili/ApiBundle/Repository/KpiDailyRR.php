<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class KpiDailyRR extends EntityRepository {

    public function findByKpiYMD($kpiYMD) {
        $query = $this->createQueryBuilder('k');
        $query = $query->select('k');
        $query = $query->Where('k.kpiYMD = :kpiYMD');
        $query = $query->setParameters(array (
            'kpiYMD' => $kpiYMD
        ));
        $query = $query->getQuery();
        return $query->getResult();
    }

}