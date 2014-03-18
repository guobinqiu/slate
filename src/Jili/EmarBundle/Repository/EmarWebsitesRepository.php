<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 */
class EmarWebsitesRepository extends EntityRepository
{
    /**
     *
     */
	public function getSortedByParams($params){
        
        extract($params);
        
        $qb  = $this->createQueryBuilder('ew');
        $qb->where($qb->expr()->eq('ew.isDeleted', 'false'));

        if( isset( $wids) && is_array($wids)  && count( $wids) > 0) {
            $qb->andWhere($qb->expr()->in('ew.webId',  $wids ));
        }

        //todo: add the catid for performance.
        
        $qb->orderBy('ew.position', 'ASC');

        $query  = $qb->getQuery();
        $results = $query->getResult();


        return $results;
    }
}
