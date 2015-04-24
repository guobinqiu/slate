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

    public function update ($params = array ()) 
    {


    }

}
