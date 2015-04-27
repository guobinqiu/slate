<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;

use Jili\ApiBundle\Entity\DuomaiOrder;

class DuomaiOrderRepository extends EntityRepository
{

    /**
     * @param integer $status
     * @param string $status
     * @return boolean false 不是重复请求。
     */
    public function isDuplicatedRequest($status, $order_sn) 
    {
        $exists = $this->findOneByOcd($order_sn);
        if( $exists && $exists->isHistoryStatusRequest($status)) {
            return true;
        }
        return false;
    }

    /**
     */
    public function init($params = array ()) 
    {
        $em = $this->getEntityManager();
        $order = new DuomaiOrder();

        $order->setUserId($params['userId'])
            ->setAdsId($params['adsId'])
            ->setAdsName($params['adsName'])
            ->setSiteId($params['siteId'])
            ->setLinkId($params['linkId'])
            ->setOcd($params['ocd'])
            ->setOrderTime($params['orderTime'])
            ->setOrdersPrice($params['ordersPrice']);

        // ->setComm(0 /*$params['comm']*/)
        // ->setStatus(DuomaiOrder::STATUS_PENDING);

        $em->persist($order);
        $em->flush();
        return $order;
    }

/*
                'userId'=> $userid,
                'adsId'=>$request->get('ads_id'),
                'adsName'=>$request->get('ads_name'),
                'siteId'=>$request->get('site_id'),
                'linkId'=> $request->get('link_id'),
                'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', $request->get('order_time')),
                'ocd' => $request->get('order_sn'),
                'ordersPrice'=> $request->get('orders_price'),
                'commission' => $request->get('siter_commission'),
                'status' => $status
 */
    public function update ($params = array ()) 
    {

        $em = $this->getEntityManager();

        $sql = 'UPDATE Jili\ApiBundle\Entity\DuomaiOrder  SET ';
        if( isset($params['confirmedAt'] )) {
            $sql .=  ' confirmedAt = :confirmedAt' ; 
        } elseif (isset($params['balancedAt']) ) {
            $sql .=  ' balancedAt = :balancedAt' ; 
        } elseif (isset($params['deactivatedAt']) ) {
            $sql .=  ' deactivatedAt = :deactivatedAt' ; 
        }

        $sql .= ', status = :status, comm = :commission WHERE  userId= :userId and adsId = :adsId  and siteId = :siteId and linkId = :linkId AND ocd = :ocd AND orderTime = :orderTime AND orderPrice =:orderPrice  LIMIT 1';

        $q_update = $em->createQuery($sql);
        $q_update ->setParameters( $params);
        return  $q_update->execute();
    }

}
