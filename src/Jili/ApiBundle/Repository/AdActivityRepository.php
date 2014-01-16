<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

class AdActivityRepository extends EntityRepository
{

    private  static $IS_DELETED_FALSE = 0;
    private  static $IS_DELETED_TRUE = 1;

    /**
     * find all not deleted activites.
     */
    public function findActivities()
    {
        $qb = $this->createQueryBuilder('q'); 

        $qb->where( $qb->expr()->eq('q.isDeleted', self::$IS_DELETED_FALSE  )  );
        $qb->orderBy('q.createdAt', 'DESC');

        $q = $qb->getQuery();

        $rows = $q->getResult();
        return $rows;
    }

}
