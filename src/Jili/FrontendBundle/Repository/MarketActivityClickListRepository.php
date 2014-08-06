<?php
namespace   Jili\FrontendBundle\Repository;
use Doctrine\ORM\EntityRepository;

class MarketActivityClickListRepository extends EntityRepository {

    public function clickCount($marketActivityId) {
        $query = $this->createQueryBuilder('macl');
        $query = $query->select('count(macl.id) as num');
        $query = $query->Where('macl.marketActivityId = :marketActivityId');
        $query = $query->setParameter('marketActivityId', $marketActivityId);
        $query = $query->getQuery();
        return $query->getOneOrNullResult();
    }
}