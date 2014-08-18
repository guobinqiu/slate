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
    public function fetchInfosByWebIds($webids)
    {
        if( is_array($webids) && count($webids) > 0) {



            $qb = $this->createQueryBuilder('p');
            $qb->select('p.webId,p.information');
            $qb->where( $qb->expr()->in( 'p.webId', $webids) ) ;
            $q =  $qb->getQuery();

            $rows = $q->getResult();
            $result = array();
            foreach($rows as $row) {
                $result[ $row['webId'] ] = $row['information'];
            }
        } else {
            $result = array();
        }
        return $result;

    }

    /**
     *
     * return an array of full fields;
     * */
    public function fetchByWebIds( $webids = array())
    {
        if( count($webids) == 0 ) {
            return array();
        }

        $qb = $this->createQueryBuilder('p');
        $qb->where( $qb->expr()->in( 'p.webId', $webids) ) ;
        $q =  $qb->getQuery();
        $rows = $q->getResult();
        return $rows;
    }

    /**
     * parser the commission string , return the max percentage in number
     **/
    public function parseMaxComission($commission)
    {
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
    public function serchByDigit()
    {
        $s =<<<EOT
SELECT web_id,web_name
FROM emar_websites_croned
WHERE web_name REGEXP '^[0-9]'
EOT;
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }
    public function serchByLetter($dic_key)
    {
        $s =<<<EOT
SELECT b.web_id, b.web_name FROM
(SELECT web_id, web_name, ELT( INTERVAL( CONV( HEX( left( CONVERT( web_name
USING gbk ) , 1 ) ) , 16, 10 ) , 0xB0A1, 0xB0C5, 0xB2C1, 0xB4EE, 0xB6EA, 0xB7A2, 0xB8C1, 0xB9FE, 0xBBF7, 0xBFA6, 0xC0AC, 0xC2E8, 0xC4C3, 0xC5B6, 0xC5BE, 0xC6DA, 0xC8BB, 0xC8F6, 0xCBFA, 0xCDDA, 0xCEF4, 0xD1B9, 0xD4D1 ) , 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z' ) AS name
FROM emar_websites_croned
)b WHERE b.name = :name ;
EOT;
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->bindParam(':name', $dic_key);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }
}
