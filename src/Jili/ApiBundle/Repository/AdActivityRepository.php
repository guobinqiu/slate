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

    /**
     *  @return max percentage where is in a valid duration and  not deleted
     */
    public function findMaxPercentage( \Datetime $at = null) {
        $o = $this->findOfMaxPercentage( $at);
        $percent = 1.0 ;
        if( $o && ! is_null( $o[1])  ) {
            $percent = $o[1];
        }
        return $percent;
    }

    /**
     *
     */
    public function findOfMaxPercentage( \Datetime $at = null) {
        $qb = $this->createQueryBuilder('q'); 
        $qb->select(array('q.id',$qb->expr()->max('q.percentage') ));
        $qb->where( $qb->expr()->eq('q.isDeleted', self::$IS_DELETED_FALSE  ))  ;
        $qb->andWhere( $qb->expr()->lte('q.startedAt', ':p1')  );
        $qb->andWhere( $qb->expr()->gt( 'q.finishedAt' , ':p1')  );
        $qb->setParameter('p1', is_null($at)  ?  new \Datetime('now'): $at  ) ;
        $q = $qb->getQuery();
        return $q->getOneOrNullResult();
    }

}
