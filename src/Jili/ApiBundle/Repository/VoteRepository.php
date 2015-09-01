<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    /**
     * @return array
     */
    public function fetchVoteList($active_flag = true)
    {
        $query = $this->createQueryBuilder('v');
        $query->select('v.id,v.title,v.startTime,v.endTime,v.yyyymm,vi.sqPath');
        $query->innerJoin('JiliApiBundle:VoteImage', 'vi', 'WITH', 'v.id = vi.voteId');
        if ($active_flag) {
            $query->andWhere('v.startTime <= :startTime');
        } else {
            $query->andWhere('v.startTime >= :startTime');
        }

        $query->orderBy('v.startTime', 'DESC');

        $parameters['startTime'] = new \Datetime();
        $query->setParameters($parameters);

        $query = $query->getQuery();
        return $query->getResult();
    }
}