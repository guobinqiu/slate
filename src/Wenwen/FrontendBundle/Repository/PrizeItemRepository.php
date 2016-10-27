<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PrizeItemRepository extends EntityRepository
{
    /**
     * 根据余额多少来获取可抽奖奖项.
     *
     * @param string $type 大奖池或小奖池
     * @param $pointBalance 奖池积分余额
     * @return array
     */
    public function getPrizeItems($type, $pointBalance)
    {
        $qb = $this->createQueryBuilder('ld');
        $qb->where('ld.type = :type');
        $qb->andWhere('ld.points <= :pointBalance');
        $qb->orderBy('ld.max', 'DESC');
        $qb->setParameter('type', $type);
        $qb->setParameter('pointBalance', $pointBalance);

        return $qb->getQuery()->getResult();
    }
}