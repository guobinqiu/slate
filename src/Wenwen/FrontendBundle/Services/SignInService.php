<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserSignInDetail;
use Wenwen\FrontendBundle\Entity\UserSignInSummary;

class SignInService
{
    private $em;
    private $prizeTicketService;

    public function __construct(EntityManager $em, PrizeTicketService $prizeTicketService) {
        $this->em = $em;
        $this->prizeTicketService = $prizeTicketService;
    }

    public function alreadySigned(User $user, \DateTime $date = null) {
        if ($date == null) {
            $date = new \DateTime();
        }
        return !$this->findUserSignInDetailsByDate($user, $date)->isEmpty();
    }

    public function createUserSignInDetail(User $user, \DateTime $date)
    {
        $userSignInDetail = new UserSignInDetail();
        $userSignInDetail->setSignInDate($date);
        $userSignInDetail->setSignInTime($date);

        $userSignInDetail->setUser($user);
        $user->getUserSignInDetails()->add($userSignInDetail);

        $this->em->persist($userSignInDetail);
        $this->em->flush();
    }

    public function createUserSignInSummary(User $user, \DateTime $date)
    {
        $userSignInSummary = new UserSignInSummary();
        $userSignInSummary->setTotalSignInCount(1);
        $userSignInSummary->setConsecutiveDays(1);
        $userSignInSummary->setStartDate($date);

        $userSignInSummary->setUser($user);
        $user->setUserSignInSummary($userSignInSummary);

        $this->em->persist($userSignInSummary);
        $this->em->flush();
    }

    /**
     * 签到
     */
    public function signIn(User $user, \DateTime $date = null)
    {
        if ($date == null) {
            $date = new \DateTime();
        }

        $prizeTicket = null;
        $today = $date;
        $this->createUserSignInDetail($user, $today);

        $userSignInSummary = $user->getUserSignInSummary();
        if ($userSignInSummary == null) {
            $this->createUserSignInSummary($user, $today);

        } else {
            $yesterday = clone $today;
            $yesterday = $yesterday->modify('-1 day');
            $userSignInDetails = $this->findUserSignInDetailsByDate($user, $yesterday);

            if ($userSignInDetails->isEmpty()) {
                $consecutiveDays = 1;
                $startDate = $today;

            } else {
                $consecutiveDays = $userSignInSummary->getConsecutiveDays() + 1;

                if ($consecutiveDays == UserSignInSummary::MAX_CONSECUTIVE_DAYS) {
                    $taskName = '连续签到'. UserSignInSummary::MAX_CONSECUTIVE_DAYS . '天';
                    $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_BIG, $taskName);// 获得一次抽奖机会

                } elseif ($consecutiveDays > UserSignInSummary::MAX_CONSECUTIVE_DAYS) {
                    $consecutiveDays = 1;
                    $startDate = $today;
                }
            }
            $userSignInSummary->setTotalSignInCount($userSignInSummary->getTotalSignInCount() + 1);
            $userSignInSummary->setConsecutiveDays($consecutiveDays);
            if (isset($startDate)) {
                $userSignInSummary->setStartDate($startDate);
            }
            $this->em->flush();
        }
    }

    //http://docs.doctrine-project.org/en/latest/reference/working-with-associations.html#filtering-collections
    public function findUserSignInDetailsByDate(User $user, \DateTime $date) {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('signInDate', $date));
        return $user->getUserSignInDetails()->matching($criteria);
    }
}