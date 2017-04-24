<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Model\CategoryType;

class PointService
{
    private $em;
    private $latestNewsService;
    private $prizeTicketService;

    /**
     * @param EntityManager $em
     * @param LoggerInterface $logger
     * @param LatestNewsService $latestNewsService
     * @param PrizeTicketService $prizeTicketService
     */
    public function __construct(EntityManager $em,
                                LoggerInterface $logger,
                                LatestNewsService $latestNewsService,
                                PrizeTicketService $prizeTicketService
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->latestNewsService = $latestNewsService;
        $this->prizeTicketService = $prizeTicketService;
    }

    public function addPoints(User $user, $points, $categoryType, $taskType, $taskName, $order = null, $happenTime = null, $needCreatePrizeTicket = false, $prizeType = PrizeItem::TYPE_SMALL) {
        $this->em->getConnection()->beginTransaction();
        try {
            if(TaskType::RENTENTION == $taskType){
                $user->setPointsExpense($user->getPointsExpense() + $points);
            } else {
                $user->setPointsCost($user->getPointsCost() + $points);
            }
            $user->setPoints($user->getPoints() + $points);
            $user->setLastGetPointsAt(new \DateTime());

            $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ($user->getId() % 10);
            $pointHistory = new $classPointHistory();
            $pointHistory->setUserId($user->getId());
            $pointHistory->setPointChangeNum($points);
            $pointHistory->setReason($categoryType);
            $this->em->persist($pointHistory);

            $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ($user->getId() % 10);
            $taskHistory = new $classTaskHistory();
            $taskHistory->setUserId($user->getId());
            if ($order != null) {
                $taskHistory->setOrderId($order->getId());
                $taskHistory->setOrderType(get_class($order));
            }
            $taskHistory->setOcdCreatedDate(new \DateTime());
            $taskHistory->setCategoryType($categoryType);
            $taskHistory->setTaskType($taskType);
            $taskHistory->setTaskName($taskName);
            $taskHistory->setDate($happenTime == null ? new \DateTime() : $happenTime);
            $taskHistory->setPoint($points);
            $taskHistory->setStatus(1);
            $taskHistory->setRewardPercent(0);
            $this->em->persist($taskHistory);

            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }

        if ($points >= 100) {
            $news = $this->latestNewsService->buildNews($user, $points, $categoryType, $taskType);
            $this->latestNewsService->insertLatestNews($news);
        }

        if ($needCreatePrizeTicket) {
            $this->prizeTicketService->createPrizeTicket($user, $prizeType, $taskName);// 获得一次抽奖机会
        }
    }

    public function addPointsForInviter(User $user, $points, $categoryType, $taskType, $taskName, $order = null, $happenTime = null, $needCreatePrizeTicket = false, $prizeType = PrizeItem::TYPE_SMALL) {
        if ($user->getInviteId() != null && ($user->getEmail() == null || ($user->getEmail() != null && $user->getIsEmailConfirmed() == 1))) {
            $inviter = $this->em->getRepository('WenwenFrontendBundle:User')->find($user->getInviteId());
            if ($inviter != null) {
                $this->logger->info(__METHOD__ . ' Reward inviter user_id=' . $user->getInviteId() . ' from invitee user_id=' . $user->getId());
                $this->addPoints($inviter, $points, $categoryType, $taskType, $taskName, $order, $happenTime, $needCreatePrizeTicket, $prizeType);
                // 该用户第一次完成(Complete)商业问卷（cost类型）时，给邀请者一次性增加奖励积分
                if($user->getCompleteN() == 1){
                    $this->logger->info(__METHOD__ . ' First complete bonus for inviter user_id=' . $user->getInviteId() . ' from invitee user_id=' . $user->getId());
                    $this->addPoints($inviter, User::POINT_INVITE_SIGNUP, CategoryType::EVENT_INVITE_SIGNUP, TaskType::RENTENTION, '完成好友邀请', null, $happenTime, false, null);
                }
            } else {
                $this->logger->debug(__METHOD__ . ' Inviter not found. user_id=' . $user->getInviteId() . ' from invitee user_id=' . $user->getId());
            }
        } else {
            $this->logger->debug(__METHOD__ . ' No inviter. user_id=' . $user->getInviteId() . ' from invitee user_id=' . $user->getId());
        }
    }
}