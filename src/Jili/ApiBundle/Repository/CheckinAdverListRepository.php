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

	public function showCheckinList($uid){
		$dateNow = date('Y-m-d');
		$sqlD = "select d.id from checkin_user_list c inner join checkin_adver_list d on c.open_shop_id = d.id where user_id = ".$uid." and inter_space > 1 and DATEDIFF('".$dateNow."',c.click_date) < d.inter_space group by c.open_shop_id order by c.click_date desc ";
		$sqlU = "select e.id from checkin_adver_list e inner join checkin_user_list u on e.id = u.open_shop_id where user_id = ".$uid." and click_date = '".$dateNow."'";
		$sql = "SELECT a.id as cid,a.inter_space,b.*
			FROM checkin_adver_list a left join advertiserment b on a.ad_id = b.id 
			WHERE a.id not in ($sqlU)  and a.id not in ($sqlD)
			ORDER BY a.id desc";
		$rs = $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
		return $rs;

	}
	
	
}