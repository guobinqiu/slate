<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class MarketActivityClickNumberRepository extends EntityRepository {

    public function getClickNumber($marketActivityId) {
        $query = $this->createQueryBuilder('macl');
        $query = $query->select('macl.clickNumber');
        $query = $query->Where('macl.marketActivityId = :marketActivityId');
        $query = $query->setParameter('marketActivityId', $marketActivityId);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }
}