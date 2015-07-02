<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;


class AdwOrderRepository extends EntityRepository
{
    public function getCpsOne($uid,$adid)
    {
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id');
        $query = $query->Where('ao.adid = :adid');
        $query = $query->andWhere('ao.userid = :uid');
        $query = $query->orderBy('ao.id', 'ASC');
        $query = $query->setFirstResult(0);
        $query = $query->setMaxResults(1);
        $parameters = array('uid'=>$uid,'adid'=>$adid);
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * getCpsInfo
     *
     * Getting the cps title from advertisment table for a adw order of the user.
     *
     * @param mixed $uid
     * @param mixed $adid
     * @access public
     * @return void
     */
    public function getCpsInfo($uid,$adid,$cps_advertisement = false)
    {
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.ocd,a.title');
        if($cps_advertisement){
            $query = $query->innerJoin('JiliFrontendBundle:CpsAdvertisement', 'a', 'WITH', 'ao.adid = a.id');
        }else{
            $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ao.adid = a.id');
        }
        $query = $query->Where('ao.adid = :adid');
        $query = $query->andWhere('ao.userid = :uid');
        $parameters = array('uid'=>$uid,'adid'=>$adid);
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();

    }

    /**
     * TODO: just select count() is ok!
     */
    public function getOrderNum($aid)
    {
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.adid,ao.orderStatus,ao.confirmTime');
        $query = $query->Where('ao.adid = :aid');
        $query = $query->setParameter('aid',$aid);
        $query = $query->getQuery();
        return count($query->getResult());
    }

    public function getCountOfJoinedByCat($ad_ids)
    {
        $qb = $this->createQueryBuilder('ao');
        $qb->select('count( ao.adid ) as cnt , ao.adid')
            ->groupby('ao.adid')
            ->where( $qb->expr()->in('ao.adid',  $ad_ids ) );

        $query  = $qb->getQuery();
        $results = $query->getResult();

        $ret= array();
        if( $results) {
            foreach ($results as $r) {
                $ret [ $r['adid'] ] = $r['cnt'] ;
            }
        }
        return $ret;
    }

    /**
     * getOrderStatus
     *
     *  finding the adw_order
     *
     * @param mixed $uid
     * @param mixed $aid
     * @param string $ocd
     * @access public
     * @return void
     */
    public function getOrderStatus($uid,$aid,$ocd='')
    {
        $parameters = array();
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.incentiveType,ao.orderStatus,ao.confirmTime');
        $query = $query->Where('ao.userid = :id');
        $query = $query->andWhere('ao.adid = :adid');
        // 3,4 is the finish status
        $query = $query->andWhere("ao.orderStatus in (3,4)");
        $parameters = array('id'=>$uid,'adid'=>$aid);
        if($ocd){
            $query = $query->andWhere('ao.ocd = :ocd');
            $parameters['ocd'] = $ocd;
        }
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();

    }

    /**
     * getOrderInfo
     *
     *  fetch the order info join with advertiserment table.
     *
     * @param mixed $userid
     * @param mixed $adid
     * @param string $ocd
     * @param string $status
     * @access public
     * @return void
     */
    public function getOrderInfo($userid,$adid,$ocd='',$status='' ,$cps_advertisement = false)
    {
        $userid = (int) $userid;
        $adid = (int) $adid;

        $parameters = array();
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.orderStatus,ao.incentiveType,ao.confirmTime,ao.ocd,a.title');
        if($cps_advertisement){
            $query = $query->innerJoin('JiliFrontendBundle:CpsAdvertisement', 'a', 'WITH', 'ao.adid = a.id');
        }else{
            $query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ao.adid = a.id');
        }
        $query = $query->Where('ao.userid = :id');
        $query = $query->andWhere('ao.adid = :adid');
        $parameters = array('id'=>$userid,'adid'=>$adid);
        if($ocd){
            $query = $query->andWhere('ao.ocd = :ocd');
            $parameters['ocd'] = $ocd;
        }
        if($status){
            $query = $query->andWhere('ao.orderStatus = :status');
            $parameters['status'] = $status;
        }
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();

    }

    /**
     * getOrderInfoForCpsAdvertisement
     *
     *  fetch the order info join with advertiserment table.
     *
     * @param mixed $userid
     * @param mixed $adid
     * @access public
     * @return void
     */
    public function getOrderInfoForCpsAdvertisement($userid,$adid)
    {
        $userid = (int) $userid;
        $adid = (int) $adid;

        $parameters = array();
        $query = $this->createQueryBuilder('ao');
        $query = $query->select('ao.id,ao.orderStatus,ao.incentiveType,ao.confirmTime,ao.ocd,a.title');
        $query = $query->innerJoin('JiliFrontendBundle:CpsAdvertisement', 'a', 'WITH', 'ao.adid = a.id');
        $query = $query->Where('ao.userid = :id');
        $query = $query->andWhere('ao.adid = :adid');
        $parameters = array('id'=>$userid,'adid'=>$adid);
        $query = $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();

    }
    /**
     * 找出某用户click过的advertiserment，但没有加入过的.
     */
    public function findOneCpsOrderInit($params)
    {
        $parameters = array('user_id'=>$params['user_id'],
            'ad_id'=>$params['ad_id'],
            'delete_flag'=> $params['delete_flag'],
            'status'=> $params['status']);

        $query = $this->createQueryBuilder('ao')
            ->select('ao')
            ->where('ao.adid = :ad_id')
            ->andWhere('ao.ocd IS NULL')
            ->andWhere('ao.deleteFlag = :delete_flag')
            ->andWhere('ao.orderStatus= :status')
            ->andWhere('ao.userid = :user_id')
            ->setParameters($parameters)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /*
	public function getUseradtaste($id,$option=array())
	{
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$monthdate =  date("Y-m-d H:i:s", strtotime(' -6 month'));
		$yeardate =  date("Y-m-d H:i:s", strtotime(' -1 year'));
		$query = $this->createQueryBuilder('ao');
		$query = $query->select('ao.adid,ao.createTime,ao.orderStatus,ao.incentive,a.incentiveRate,a.title,a.incentiveType,a.category,ad.displayName');
		$query = $query->innerJoin('JiliApiBundle:Advertiserment', 'a', 'WITH', 'ao.adid = a.id');
		$query = $query->innerJoin('JiliApiBundle:AdCategory', 'ad', 'WITH', 'a.category = ad.id');
		$query = $query->Where('ao.userid = :id');
		if($option['daytype']){
			switch($option['daytype']){
			    case 0:
			    	break;
			    case 1:
			    	$query = $query->andWhere('ao.createTime > :daydate');
			    	$query = $query->setParameter('daydate',$daydate);
			        break;
			    case 2:
			    	$query = $query->andWhere('ao.createTime > :monthdate');
			    	$query = $query->setParameter('monthdate',$monthdate);
			    	break;
			    case 3:
			    	$query = $query->andWhere('ao.createTime > :yeardate');
			    	$query = $query->setParameter('yeardate',$yeardate);
			    	break;
			}
		}
		$query = $query->setParameter('id',$id);
		$query = $query->orderBy('ao.createTime', 'DESC');
		if($option['offset'] && $option['limit']){
			$query = $query->setFirstResult(0);
			$query = $query->setMaxResults(10);
		}
		$query = $query->getQuery();
		return $query->getResult();
	}
	*/



}
