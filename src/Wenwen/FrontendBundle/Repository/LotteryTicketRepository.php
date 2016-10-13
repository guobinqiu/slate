<?php

namespace Wenwen\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LotteryTicketRepository extends EntityRepository
{
    public function getUnusedLotteryTickets($userId)
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.userId = :userId')
            ->andWhere('t.deletedAt is null')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $query->getResult();
    }
}