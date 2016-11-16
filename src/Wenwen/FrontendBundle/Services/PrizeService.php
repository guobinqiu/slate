<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class PrizeService
{
    private $em;
    private $logger;
    private $redis;
    private $pointService;
    private $prizeTicketService;

    public function __construct(EntityManager $em,
                                LoggerInterface $logger,
                                Client $redis,
                                PointService $pointService,
                                PrizeTicketService $prizeTicketService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->redis = $redis;
        $this->pointService = $pointService;
        $this->prizeTicketService = $prizeTicketService;
    }

    /**
     * 返回抽中的奖项.
     *
     * @param $type 大奖池或小奖池
     * @param $pointBalance 积分余额
     * @return PrizeItem
     */
    public function getPrizedItem($type, $pointBalance)
    {
        $prizeItems = $this->em->getRepository('WenwenFrontendBundle:PrizeItem')->getPrizeItems($type, $pointBalance);
        $randNum = mt_rand(1, $prizeItems[0]->getMax());
        foreach($prizeItems as $prizeItem) {
            if ($randNum >= $prizeItem->getMin() && $randNum <= $prizeItem->getMax()) {
                return $prizeItem;
            }
        }
        return null;
    }

    /**
     * 抽奖.
     *
     * @param User $user
     * @return int
     */
    public function drawPrize(User $user)
    {
        $rewardPoints = 0;
        $prizeTickets = $this->prizeTicketService->getUnusedPrizeTickets($user);
        if ($this->getPointBalance() > 0 && count($prizeTickets) > 0) {
            $prizeTicket = $prizeTickets[0];
            $prizedItem = $this->getPrizedItem($prizeTicket->getType(), $this->getPointBalance());
            if ($prizedItem != null) {
                $rewardPoints = $prizedItem->getPoints();
                $this->minusPrizeItemQuantity($prizedItem);
            }
            if ($rewardPoints > 0) {
                $this->minusPointBalance($rewardPoints);
            }
            $this->pointService->addPoints($user, $rewardPoints, CategoryType::EVENT_PRIZE, TaskType::RENTENTION, '抽奖', $prizeTicket->getId());
            $this->prizeTicketService->deletePrizeTicket($prizeTicket);
        }

        return $rewardPoints;
    }

    /**
     * 奖池是否空.
     *
     * @return bool
     */
    public function isPointBalanceEmpty()
    {
        return $this->getPointBalance() == 0;
    }

    /**
     * 奖池积分余额.
     *
     * @return int
     */
    public function getPointBalance()
    {
        $pointBalance = $this->redis->get(CacheKeys::PRIZE_POINT_BALANCE);
        if ($pointBalance == null) {
            $this->resetPointBalance();
        }
        return (int)$pointBalance;
    }

    /**
     * 设置奖池积分.
     *
     * @param $points
     */
    public function setPointBalance($points)
    {
        $this->redis->set(CacheKeys::PRIZE_POINT_BALANCE, $points);
    }

    /**
     * 增加奖池积分.
     *
     * @param int $points
     */
    public function addPointBalance($points)
    {
        $points = $this->getPointBalance() + $points;
        if ($points > PrizeItem::POINT_BALANCE_MAX) {
            $points = PrizeItem::POINT_BALANCE_MAX;
        }
        $this->setPointBalance($points);
    }

    /**
     * 减去奖池积分.
     *
     * @param int $points
     */
    public function minusPointBalance($points)
    {
        $points = $this->getPointBalance() - $points;
        if ($points < PrizeItem::POINT_BALANCE_MIN) {
            $points = PrizeItem::POINT_BALANCE_MIN;
        }
        $this->setPointBalance($points);
    }

    /**
     * 重置奖池积分.
     */
    public function resetPointBalance()
    {
        $this->setPointBalance(PrizeItem::POINT_BALANCE_BASE);
    }

    /**
     * 减去奖品库存.
     *
     * @param PrizeItem $prizeItem
     */
    private function minusPrizeItemQuantity(PrizeItem $prizeItem) {
        $quantity = $prizeItem->getQuantity() - 1;
        if ($quantity < 0) {
            $quantity = 0;
        }
        $prizeItem->setQuantity($quantity);
        $this->em->flush($prizeItem);
    }
}