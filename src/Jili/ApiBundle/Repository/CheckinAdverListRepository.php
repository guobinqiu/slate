<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function showCheckinList($uid)
    {
        $dateNow = date('Y-m-d');
        $sqlD = "SELECT d.id FROM checkin_user_list c INNER JOIN checkin_adver_list d ON c.open_shop_id = d.id WHERE user_id = ".$uid." AND inter_space > 1 AND DATEDIFF('".$dateNow."',c.click_date) < d.inter_space GROUP BY c.open_shop_id ORDER BY c.click_date desc ";
        $sqlU = "SELECT e.id FROM checkin_adver_list e INNER JOIN checkin_user_list u ON e.id = u.open_shop_id WHERE user_id = ".$uid." AND click_date = '".$dateNow."'";
        $sql = "SELECT a.id as cid,a.inter_space,b.id,b.title,b.list_image,b.reward_rate,b.incentive_rate
			FROM checkin_adver_list a LEFT JOIN advertiserment b on a.ad_id = b.id
			WHERE a.id not in ($sqlU)  and a.id not in ($sqlD)
			ORDER BY a.id desc";
        $rs = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
        return $rs;

    }


}
