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

class PrizeItemService
{
    private $em;
    private $logger;
    private $redis;
    private $userService;

    public function __construct(EntityManager $em,
                                LoggerInterface $logger,
                                Client $redis,
                                UserService $userService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->redis = $redis;
        $this->userService = $userService;
    }

    /**
     * 返回中奖奖项.
     *
     * @param $type 大奖池或小奖池
     * @param $pointBalance 积分余额
     * @return PrizeItem
     */
    public function getPrizeItem($type, $pointBalance)
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
     * 给大奖池获奖用户加积分.
     *
     * @param User $user
     * @return int
     */
    public function bigPrizeBox(User $user)
    {
        $prizeItem = $this->getPrizeItem(PrizeItem::PRIZE_BOX_BIG, $this->getPointBalance());
        $points = $prizeItem->getPoints();
        if ($points == PrizeItem::FIRST_PRIZE_POINTS && $prizeItem->getQuantity() == 0) {
            $this->bigPrizeBox($user);//再抽一次
        }
        if ($points > 0) {
            $this->userService->addPoints($user, $points, CategoryType::EVENT_LOTTERY, TaskType::RENTENTION, PrizeItem::PRIZE_BOX_BIG);
            $this->minusPointBalance($points);
        }
        $this->minusPrizeQuantity($prizeItem);
        return $points;
    }

    /**
     * 给小奖池获奖用户加积分.
     *
     * @param User $user
     * @return int
     */
    public function smallPrizeBox(User $user)
    {
        $prizeItem = $this->getPrizeItem(PrizeItem::PRIZE_BOX_SMALL, $this->getPointBalance());
        $points = $prizeItem->getPoints();
        if ($points > 0) {
            $this->userService->addPoints($user, $points, CategoryType::EVENT_LOTTERY, TaskType::RENTENTION, PrizeItem::PRIZE_BOX_SMALL);
            $this->minusPointBalance($points);
        }
        $this->minusPrizeQuantity($prizeItem);
        return $points;
    }

    public function isPointBalanceEmpty()
    {
        return $this->getPointBalance() == 0;
    }

    public function getPointBalance()
    {
        $pointBalance = $this->redis->get(CacheKeys::LUCKY_DRAW_POINT_BALANCE);
        if ($pointBalance == null) {
            $this->resetPointBalance();
        }
        return $pointBalance;
    }

    public function addPointBalance($points)
    {
        $this->redis->set(CacheKeys::LUCKY_DRAW_POINT_BALANCE, $this->getPointBalance() + $points);
    }

    public function minusPointBalance($points)
    {
        $this->redis->set(CacheKeys::LUCKY_DRAW_POINT_BALANCE, $this->getPointBalance() - $points);
    }

    public function resetPointBalance()
    {
        $this->redis->set(CacheKeys::LUCKY_DRAW_POINT_BALANCE, 0);
    }

    private function minusPrizeQuantity(PrizeItem $prizeItem) {
        $quantity = $prizeItem->getQuantity() - 1;
        if ($quantity < 0) {
            $quantity = 0;
        }
        $prizeItem->setQuantity($quantity);
        $this->em->flush($prizeItem);
    }
}