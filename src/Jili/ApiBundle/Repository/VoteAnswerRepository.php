<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoteAnswerRepository extends EntityRepository
{

    /**
     * get answer count
     *
     * @param integer $vote_id
     *
     * @return integer The answer count of vote
     */
    public function getAnswerCount($vote_id)
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare("SELECT COUNT(id) FROM vote_answer where vote_id = :vote_id");
        $params['vote_id'] = $vote_id;
        $stmt->execute($params);
        $return = $stmt->fetchColumn();
        return $return;
    }

    /**
     * get each answer count
     *
     * @param integer $vote_id
     * @param integer $answer_number
     *
     * @return integer The answer count of each choice
     */
    public function getEachAnswerCount($vote_id, $answer_number)
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare("SELECT COUNT(id) FROM vote_answer where vote_id = :vote_id and answer_number = :answer_number");
        $params['vote_id'] = $vote_id;
        $params['answer_number'] = $answer_number;
        $stmt->execute($params);
        $return = $stmt->fetchColumn();
        return $return;
    }

    /**
     * get user answer count
     *
     * @param integer $user_id
     * @param integer $vote_id
     *
     * @return integer The answer count of user
     */
    public function getUserAnswerCount($user_id, $vote_id)
    {
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare("SELECT COUNT(id) FROM vote_answer where vote_id = :vote_id and user_id = :user_id");
        $params['vote_id'] = $vote_id;
        $params['user_id'] = $user_id;
        $stmt->execute($params);
        $return = $stmt->fetchColumn();
        return $return;
    }
}