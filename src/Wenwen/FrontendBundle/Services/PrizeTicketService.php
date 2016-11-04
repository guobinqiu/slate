<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\PrizeTicket;

class PrizeTicketService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * 检索出该用户所有未使用过的奖券.
     *
     * @param $user
     * @return mixed
     */
    public function getUnusedPrizeTickets($user) {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->isNull('deletedAt'));

        return $user->getPrizeTickets()->matching($criteria);
    }

    /**
     * 创建一张奖券即一次抽奖机会.
     *
     * @param User $user
     * @param $type
     * @param null $comment
     * @return PrizeTicket
     */
    public function createPrizeTicket(User $user, $type, $comment = null, $surveyId = null, $status = null)
    {
        $prizeTicket = new PrizeTicket();
        $prizeTicket->setType($type);
        $prizeTicket->setComment($comment);
        $prizeTicket->setCreatedAt(new \DateTime());
        $prizeTicket->setSurveyId($surveyId);
        $prizeTicket->setStatus($status);

        $prizeTicket->setUser($user);
        $user->getPrizeTickets()->add($prizeTicket);

        $this->em->persist($prizeTicket);
        $this->em->flush();

        return $prizeTicket;
    }

    /**
     * 作废一张奖券.
     *
     * @param PrizeTicket $prizeTicket
     */
    public function deletePrizeTicket(PrizeTicket $prizeTicket)
    {
        $prizeTicket->setDeletedAt(new \DateTime());
        $this->em->flush();
    }
}