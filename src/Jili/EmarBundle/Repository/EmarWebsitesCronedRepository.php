<?php
namespace Jili\EmarBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Jili\EmarBundle\Entity\EmarWebsitesCron;

/**
 * for query
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
    public function fetchByWebIds( $webids = array()) {
        $qb = $this->createQueryBuilder('p');
        $qb->where( $qb->expr()->in( 'p.webId', $webids) ) ;
        $q =  $qb->getQuery();
        $rows = $q->getResult();
        return $rows;
    }

    /**
     * parser the commission string , return the max percentage in number 
     **/
    public function parseMaxComission(  $commission) {
        $comm = null;
        if( 0< strlen(trim($commission))) {
            $reg = '/(\d+\.?\d*)%/m';
            preg_match_all($reg, $commission , $m);
            if(count($m) > 1 && count($m[1])>0  ) {
                $comm = max($m[1]); 
            }
        }

        return $comm;
    }
}
