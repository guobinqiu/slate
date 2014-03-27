<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 */
class EmarWebsitesCronedRepository extends EntityRepository
{

    /**
     * $webids array of web_ids 
     * return an array of (information, web_id);
     */
    public function fetchInfosByWebIds( $webids ) {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.webId,p.information');
        $qb->where( $qb->expr()->in( 'p.webId', $webids) ) ;

        $q =  $qb->getQuery();

        $rows = $q->getResult();
        $result = array();
        foreach($rows as $row) {
            $result[ $row['webId'] ] = $row['information'];
        }
        return $result;

    }

    /**
     * 
     * return an array of full fields;
     * */
    public function fetchByWebIds( $webids ) {

        $qb = $this->createQueryBuilder('p');

        $qb->where( $qb->expr()->in( 'p.webId', $webids) ) ;

        $q =  $qb->getQuery();

        $rows = $q->getResult();

        $result = array();

        foreach($rows as $row) {
            $result[ $row->getWebId() ] = $row;
        }

        return $result;
    }
}
