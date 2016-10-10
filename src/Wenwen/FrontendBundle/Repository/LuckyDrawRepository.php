<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LuckyDrawRepository extends EntityRepository
{
    /**
     * 返回中奖积分
     *
     * @param string $type 大奖池或小奖池
     * @param $pointBlance 奖池积分余额
     * @param $randNum 随机数
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPrize($type, $pointBlance, $randNum)
    {
        $qb = $this->createQueryBuilder('ld');
        $qb->where('ld.type = :type');
        $qb->andWhere('ld.points <= :pointBlance');
        $qb->andWhere('ld.min <= :randNum');
        $qb->andWhere('ld.max >= :randNum');
        $qb->setParameter('type', $type);
        $qb->setParameter('pointBlance', $pointBlance);
        $qb->setParameter('randNum', $randNum);
        $record = $qb->getQuery()->getOneOrNullResult();
        return $record['points'];
    }
}