<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Jili\ApiBundle\Entity\CheckinAdverList;

class CheckinAdverListRepository extends EntityRepository
{
    public function getAllCheckinList()
    {
        $query = $this->createQueryBuilder('cal');
        $query = $query->select('cal.id,cal.interSpace,cal.createTime,a.title');
        $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'cal.adId = a.id');
        $query = $query->orderBy('cal.createTime','DESC');
        $query =  $query->getQuery();
        return $query->getResult();
    }

    /**
     * @param integer $uid user id 
     * @param integer $operation manual or auto or both, 0 means undefined 
     */
    public function showCheckinList($uid, $operation = 1, $dateNow = null)
    {
        if( is_null($dateNow)) {
            $dateNow = date('Y-m-d');
        }

        # 找出最近签过的商家
        $sqlD = 'SELECT d.id 
            FROM checkin_user_list c 
            INNER JOIN checkin_adver_list d ON c.open_shop_id = d.id 
            WHERE c.user_id = :uid AND mod(d.operation_method, :operation)=0 AND inter_space > 1 AND DATEDIFF(:dateNow, c.click_date) < d.inter_space 
            GROUP BY c.open_shop_id '; //ORDER BY c.click_date desc 

        # 找出今天签过的商家
        $sqlU = 'SELECT e.id 
            FROM checkin_adver_list e 
            INNER JOIN checkin_user_list u ON e.id = u.open_shop_id 
            WHERE u.user_id = :uid AND mod(e.operation_method, :operation)=0 AND click_date = :dateNow';

        # 找出今天没有签过的商家
        $sql = "SELECT a.id as cid,a.inter_space,b.id,b.title,b.list_image,b.reward_rate,b.incentive_rate
            FROM checkin_adver_list a 
                LEFT JOIN advertiserment b on a.ad_id = b.id 
            WHERE mod(a.operation_method, :operation)=0 
            AND b.end_time > :endTime 
            AND (b.delete_flag is null OR b.delete_flag = 0 ) 
            AND a.id NOT IN ($sqlU)  AND a.id NOT IN ($sqlD)
			ORDER BY a.id desc";

        if($operation === CheckinAdverList::ANY_OP_METHOD) {
            $operation  = 1;
        }

        $conn = $em = $this->getEntityManager()->getConnection();
        $sth = $conn->prepare($sql);
        $sth->bindParam(':uid',$uid, \PDO::PARAM_INT);
        $sth->bindParam(':operation',$operation, \PDO::PARAM_INT);
        $sth->bindParam(':dateNow',$dateNow, \PDO::PARAM_STR);

        $end_time= $dateNow.' 00:00:00';

        $sth->bindParam(':endTime', $end_time , \PDO::PARAM_STR);
        $sth->execute();
        $rs =  $sth->fetchAll();
        return $rs;
    }

}
