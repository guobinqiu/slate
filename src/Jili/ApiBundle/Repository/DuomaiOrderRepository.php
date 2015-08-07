<?php
namespace Jili\ApiBundle\Repository;
use Doctrine\ORM\EntityRepository;

use Jili\ApiBundle\Entity\DuomaiOrder;
use Jili\ApiBundle\Component\OrderBase;

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
            ->setOrderSn($params['orderSn'])
            ->setOrderTime($params['orderTime'])
            ->setOrdersPrice($params['ordersPrice'])
            ->setComm($params['commission']);


        $em->persist($order);
        $em->flush();
        return $order;
    }

/*
        $params = array( 'userId'=> 105,
            'adsId'=>61,
            'adsName'=>'京东商城CPS推广',
            'siteId'=>'152244',
            'linkId'=>'0',
            'orderSn'=>'9152050154',
            'ordersPrice'=>'799.00',
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', '2015-04-27 10:28:59'),
            'ocd' => '71440050',
            'status'=> 1,
            'statusPrevous'=> 0
        );
 */
    public function update($params = array ()) 
    {
        $em = $this->getEntityManager();

        $sql = 'UPDATE Jili\ApiBundle\Entity\DuomaiOrder d  SET ';

        if( isset($params['confirmedAt'] )) {
            $sql .=  ' d.confirmedAt = :confirmedAt' ; 
        } elseif (isset($params['balancedAt']) ) {
            $sql .=  ' d.balancedAt = :balancedAt' ; 
        } elseif (isset($params['deactivatedAt']) ) {
            $sql .=  ' d.deactivatedAt = :deactivatedAt' ; 
        } else {
            return ;
        }

        # when orders status transfer to pending , orders_price may changes.
        if( intval($params['status']) ===  OrderBase::getPendingStatus() ) {
            $sql .= ', d.status = :status, d.comm = :commission, d.ordersPrice = :ordersPrice WHERE  d.userId= :userId and d.adsId = :adsId  and d.siteId = :siteId and d.linkId = :linkId AND d.ocd = :ocd AND d.orderTime = :orderTime AND  d.orderSn = :orderSn ';
        } else {
            $sql .= ', d.status = :status, d.comm = :commission WHERE  d.userId= :userId and d.adsId = :adsId  and d.siteId = :siteId and d.linkId = :linkId AND d.ocd = :ocd AND d.orderTime = :orderTime AND d.ordersPrice = cast(  :ordersPrice  as decimal(10,2)) and d.orderSn = :orderSn ';
        }

        if ( isset($params['statusPrevous'])) {
            $sql .= ' and d.status = :statusPrevous';
        }

        $q_update = $em->createQuery($sql);
        $q_update->setParameters( $params);

        return  $q_update->execute();
    }

/*
            'id'=>1,
            'commission'
            'status'=> 1,
            'statusPrevous'=> 0
 */
    public function updateById($params = array ()) 
    {

        $em = $this->getEntityManager();

        $sql = 'UPDATE Jili\ApiBundle\Entity\DuomaiOrder d  SET ';

        if( isset($params['confirmedAt'] )) {
            $sql .=  ' d.confirmedAt = :confirmedAt' ; 
        } elseif (isset($params['balancedAt']) ) {
            $sql .=  ' d.balancedAt = :balancedAt' ; 
        } elseif (isset($params['deactivatedAt']) ) {
            $sql .=  ' d.deactivatedAt = :deactivatedAt' ; 
        } else {
            return ;
            # throw new Exception('');
        }

        $sql .= ', d.status = :status, d.comm = :commission WHERE  d.id = :id';

        if ( isset($params['statusPrevous'])) {
            $sql .= ' and d.status = :statusPrevous';
        }

        $q_update = $em->createQuery($sql);
        $q_update->setParameters( $params);
        return  $q_update->execute();
    }
}
