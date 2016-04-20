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

        if ($limit > 0) {
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

    /**
     * 获取参数指定的用户还没有回答过的，正在进行中的快速问答数据
     *
     * @return array votes
     */
    public function retrieveUnanswered($user_id = null)
    {
        $em = $this->getEntityManager();
        $query = $this->createQueryBuilder('v');
        $query->select('v.id,v.title,v.startTime,v.endTime,v.pointValue');

        $query->andWhere('v.startTime <= :today');
        $query->andWhere('v.endTime >= :today');

        $query->orderBy('v.endTime', 'ASC');
        $parameters['today'] = new \Datetime();
        $query->setParameters($parameters);
        $query = $query->getQuery();
        $active_votes = $query->getResult();

        if ($user_id) {
            foreach ($active_votes as $key => $value) {
                $user_answer_count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $value['id']);
                if ($user_answer_count) {
                    unset($active_votes[$key]);
                }
            }
        }
        return $active_votes;
    }
}