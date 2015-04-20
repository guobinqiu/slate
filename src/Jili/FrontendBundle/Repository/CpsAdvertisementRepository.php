<?php
namespace Jili\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jili\FrontendBundle\Entity\CpsAdvertisement;

class CpsAdvertisementRepository extends EntityRepository 
{
    
    /**
     * @return array [ categery_name=>count_of_rows, ...  ]
     */
    public function fetchCategoryList() 
    {
        $qb = $this->createQueryBuilder('ea');
        $qb->select('count( ea) as cnt , ea.websiteCategory')
            ->groupby('ea.websiteCategory')
            ->orderBy('cnt', 'DESC');
        $query  = $qb->getQuery();
        $results = [];
        foreach( $query->getResult() as $row) {
            $results[$row['websiteCategory']] =  $row['cnt'];

        }
        return $results;
    }

    /**
     * @param array array('limit'=>, 'category'=>);
     */
    public function findSameCatWebsitesByRandom() {
        
        $sql = 'select ca.* from cps_advertisement ca inner join(select r1.id from cps_advertisement as r1  join( select (rand() * (select max(id) from cps_advertisement) ) AS id ) AS r2 Where  r1.id > r2.id and r1.website_category = ":web_cate"  order by r1.id asc  limit :cnt) as rows on rows.id = ca.id ';

        $conn = $em = $this->getEntityManager()->getConnection();
        $sth = $conn->prepare($sql);
        $sth->bindParam(':cnt',$limit , \PDO::PARAM_INT);
        $sth->bindParam(':web_cat',$category, \PDO::PARAM_STR);
        $sth->execute();
        $rs =  $sth->fetchAll();
        return $rs;
    }

    public function serchByDigit()
    {
        $s =<<<EOT
SELECT id,web_name
FROM cps_advertisement 
WHERE website_name REGEXP '^[0-9]'
EOT;
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    public function serchByLetter($dic_key)
    {
        $s =<<<EOT
SELECT b.id, b.website_name FROM
(SELECT id, website_name, ELT( INTERVAL( CONV( HEX( left( CONVERT( website_name
USING gbk ) , 1 ) ) , 16, 10 ) , 0xB0A1, 0xB0C5, 0xB2C1, 0xB4EE, 0xB6EA, 0xB7A2, 0xB8C1, 0xB9FE, 0xBBF7, 0xBFA6, 0xC0AC, 0xC2E8, 0xC4C3, 0xC5B6, 0xC5BE, 0xC6DA, 0xC8BB, 0xC8F6, 0xCBFA, 0xCDDA, 0xCEF4, 0xD1B9, 0xD4D1 ) , 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z' ) AS name
FROM cps_advertisement  
)b WHERE b.website_name = :name ;
EOT;
        $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
        $stmt->bindParam(':name', $dic_key);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }
}
