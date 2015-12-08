<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    /**
     * fetch vote entity list
     *
     * @param boolean $active_flag
     *
     * @return array The objects.
     */
    public function fetchVoteList($active_flag = true, $limit = false)
    {
        $query = $this->createQueryBuilder('v');
        $query->select('v.id,v.title,v.startTime,v.endTime,v.description,v.voteImage');
        if ($active_flag) {
            $query->andWhere('v.startTime <= :startTime');
        } else {
            $query->andWhere('v.startTime >= :startTime');
        }

        $query->orderBy('v.startTime', 'DESC');

        $parameters['startTime'] = new \Datetime();
        $query->setParameters($parameters);

        if ($limit) {
            $query = $query->setFirstResult(0);
            $query = $query->setMaxResults($limit);
        }

        $query = $query->getQuery();
        return $query->getResult();
    }

    /**
     * fetch active vote entity list
     *
     * @return array The objects.
     */
    public function getActiveVoteList()
    {
        $query = $this->createQueryBuilder('v');
        $query->select('v.id,v.title,v.startTime,v.endTime,v.pointValue');
        $query->andWhere('v.startTime <= :startTime');
        $query->andWhere('v.endTime >= :startTime');
        $query->orderBy('v.startTime', 'DESC');
        $parameters['startTime'] = new \Datetime();
        $query->setParameters($parameters);
        $query = $query->getQuery();
        return $query->getResult();
    }
}