<?php
namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Jili\ApiBundle\Entity\ExchangeFlowOrder;

class ExchangeFlowOrderRepository extends EntityRepository
{
    public function insert($params = array ()) {
        $em = $this->getEntityManager();
        $exchangeFlowOrder = new ExchangeFlowOrder();
        $exchangeFlowOrder->setUserId($params['user_id']);
        $exchangeFlowOrder->setProvider($params['provider']);
        $exchangeFlowOrder->setProvince($params['province']);
        $exchangeFlowOrder->setCustomProductId($params['custom_product_id']);
        $exchangeFlowOrder->setPackagesize($params['packagesize']);
        $exchangeFlowOrder->setCustomPrise($params['custom_prise']);
        $em->persist($exchangeFlowOrder);
        $em->flush();
        return $exchangeFlowOrder;
    }
}
