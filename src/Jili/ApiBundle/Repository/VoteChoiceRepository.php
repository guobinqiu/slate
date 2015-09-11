<?php

namespace Jili\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VoteChoiceRepository extends EntityRepository
{
    public function getVoteChoice($vote_id, $answer_number)
    {
        $query = $this->createQueryBuilder('vc');
        $query->select('vc');
        $query->Where('vc.voteId = :voteId');
        $query->andWhere('vc.answerNumber = :answerNumber');

        $parameters['voteId'] = $vote_id;
        $parameters['answerNumber'] = $answer_number;
        $query->setParameters($parameters);

        $query = $query->getQuery();

        return $query->getOneOrNullResult();
    }
}