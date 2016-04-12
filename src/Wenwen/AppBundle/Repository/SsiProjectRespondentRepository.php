<?php
namespace Wenwen\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SsiProjectRespondentRepository extends EntityRepository
{

    public function retrieveAllForRespondentCount($ssi_respondent)
    {
        $query = $this->createQueryBuilder('ssi');
        $query = $query->select('COUNT(ssi.id)');
        $query = $query->Where('ssi.ssiRespondent = :ssiRespondent');
        $param['ssiRespondent'] = $ssi_respondent;
        $query = $query->setParameters($param);
        $query = $query->getQuery();
        $count = $query->getSingleScalarResult();

        return $count;
    }

    public function retrieveAllForRespondent($ssi_respondent, $pageSize = 10, $page = 1)
    {
        $query = $this->createQueryBuilder('ssi');

        $query = $query->select('ssi');

        $query = $query->Where('ssi.ssiRespondent = :ssiRespondent');

        $param['ssiRespondent'] = $ssi_respondent;
        $query = $query->setParameters($param);

        if ($page < 1) {
            $page = 1;
        }
        $query = $query->setFirstResult($pageSize * ($page - 1));
        $query = $query->setMaxResults($pageSize);
        $query->orderBy('ssi.id', 'DESC');
        $query = $query->getQuery();

        return $query->getResult();
    }
}
