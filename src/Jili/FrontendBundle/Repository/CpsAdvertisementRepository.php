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
    public function findSameCatWebsitesByRandom( $params ) 
    {
        
        $sql = 'select ca.* from cps_advertisement ca inner join(select r1.id from cps_advertisement as r1  join( select (rand() * (select max(id) from cps_advertisement) ) AS id ) AS r2 Where  r1.id > r2.id and r1.website_category = :web_cat  order by r1.id asc  limit :cnt) as rows on rows.id = ca.id ';

        $conn = $em = $this->getEntityManager()->getConnection();
        $sth = $conn->prepare($sql);
        $sth->bindParam(':cnt', $params['limit'] , \PDO::PARAM_INT);
        $sth->bindParam(':web_cat', $params['category'], \PDO::PARAM_STR);
        $sth->execute();
        $rs =  $sth->fetchAll();
        return $rs;
    }

    /**
     * @param string/number  $key
     * @return array  [ [id=>, websiteName=> , title=>] , ... ] 
     */
    public function fetchByWebsiteNameDictionaryKey( $key)
    {

        # notice; the '2' is not number 2.
        if($key === '2' ) {
            $s ='SELECT id,website_name as websiteName, title, md5(website_host) as hostHashAsLogoName FROM cps_advertisement WHERE website_name REGEXP "^[0-9]"';
            $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
            $stmt->execute();
            return  $stmt->fetchAll();
        }

        if($key === '1' ) {
            $s ='SELECT id,website_name as websiteName, title, md5(website_host) as hostHashAsLogoName FROM cps_advertisement ';
            $stmt =  $this->getEntityManager()->getConnection()->prepare($s);
            $stmt->execute();
            return  $stmt->fetchAll();
        }

        $ord = ord($key);
        if ( ( 65<= $ord && $ord <= 90 ) || 97 <= $ord && $ord <= 122 ) {
            $qb = $this->createQueryBuilder('ca');
            $qb->Where('ca.websiteNameDictionaryKey = :key')
                ->setParameter('key', $key);
            $query = $qb->getQuery();
            return $query->getResult();
        }

        return [];
    }

    /**
     * @param array [ keyword=> , wcat=>]
     */
    public function fetchByKeywordsAndCategory($params)
    {

        $qb = $this->createQueryBuilder('ca');
        $qb->Where('1=1');

        $keyword = $params['keyword']; 
        if(strlen($keyword) > 0 ) {

            $qb->Andwhere( 'ca.websiteName like :keyword')
                ->setParameter('keyword', '%'.$keyword .'%' ); 
        }

        $wcat = $params['wcat']; 
        if(strlen($wcat) > 0 && $wcat !== '-1' ) {
            $qb->Andwhere( 'ca.websiteCategory = :wcat')
                ->setParameter('wcat', $wcat ); 
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }


}
