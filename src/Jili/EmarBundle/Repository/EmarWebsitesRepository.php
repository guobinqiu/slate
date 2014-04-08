<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 */
class EmarWebsitesRepository extends EntityRepository
{

    /**
     * todo: statics by user clicked.
     * @param: array( limit => , )
     */
    public function getHot($params = array() ) {
        extract($params);

        $qb  = $this->createQueryBuilder('ew');
        $qb->select('DISTINCT ew.webId');
        $qb->where($qb->expr()->eq('ew.isDeleted', 'false'));
        $qb->andWhere($qb->expr()->eq('ew.isHidden', 'false'));
        $qb->andWhere($qb->expr()->eq('ew.isHot', 'true'));
        $qb->orderBy('ew.hotAt', 'DESC');
        $qb->addOrderBy('ew.position', 'ASC');

        if( isset($limit)) {
            $qb->setMaxResults($limit);
        }

        $query  = $qb->getQuery();
        $results = $query->getResult();
        return $results;
    }
    /**
     * @param $params array(wids=>)
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

    /**
     * @abstract: the webs fitlers on product search page and retrieve pages.
     */
    public function getFilterWebs()
    {
        $qb  = $this->createQueryBuilder('ew');
        $qb->where($qb->expr()->eq('ew.isDeleted', 'false'));
        $qb->andWhere($qb->expr()->eq('ew.isHidden', 'false'));
        $qb->orderBy('ew.position', 'ASC');
        $query  = $qb->getQuery();
        $results = $query->getResult();
        return $results;

    }

}
