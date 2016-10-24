<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\User;

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

    public function addPoints(User $user, $points, $categoryType, $taskType, $taskName, $orderId = 0, $happenTime = null, $canDrawPrize = false) {
        $this->em->getConnection()->beginTransaction();
        try {
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
            $taskHistory->setUserid($user->getId());
            $taskHistory->setOrderId($orderId);
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

        if ($canDrawPrize) {
            $this->prizeTicketService->createPrizeTicket($user, PrizeItem::TYPE_SMALL, $taskName);// 获得一次抽奖机会
        }
    }

    public function addPointsForInviter(User $user, $points, $categoryType, $taskType, $taskName, $canDrawPrize = true) {
        if ($user->getInviteId() != null) {
            $inviter = $this->em->getRepository('WenwenFrontendBundle:User')->find($user->getInviteId());
            if ($inviter != null) {
                $this->logger->info(__METHOD__ . '给邀请人加积分，邀请人ID：' . $user->getInviteId() . '，当前用户ID：' . $user->getId());
                $this->addPoints($inviter, $points, $categoryType, $taskType, $taskName, 0, null, $canDrawPrize);
            }
        }
    }
}