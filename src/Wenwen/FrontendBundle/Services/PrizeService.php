<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\PrizeTicket;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class PrizeService
{
    private $em;
    private $logger;
    private $redis;
    private $userService;
    private $parameterService;

    public function __construct(EntityManager $em,
                                LoggerInterface $logger,
                                Client $redis,
                                UserService $userService,
                                ParameterService $parameterService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->redis = $redis;
        $this->userService = $userService;
        $this->parameterService = $parameterService;
    }

    /**
     * 返回抽中的奖项.
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
     * 抽大奖.
     *
     * @param User $user
     * @return int
     */
    public function drawBigPrize(User $user)
    {
        $prizeItem = $this->getPrizeItem(PrizeItem::TYPE_BIG, $this->getPointBalance());
        $points = $prizeItem->getPoints();
        if ($points > 0) {
            if ($points == PrizeItem::FIRST_PRIZE_POINTS) {
                if ($prizeItem->getQuantity() == 0) {
                    $this->logger->info('userid=' . $user->getId() . '杯具了，中了头奖，但很遗憾由于库存不足作废');
                    return $this->drawBigPrize($user);//再抽一次
                }
                $this->logger->info('userid=' . $user->getId() . '运气好，中了头奖');
            }
            $this->userService->addPoints($user, $points, CategoryType::EVENT_PRIZE, TaskType::RENTENTION, '抽奖');
            $this->minusPointBalance($points);
        }
        $this->minusPrizeQuantity($prizeItem);
        return $points;
    }

    /**
     * 抽小奖.
     *
     * @param User $user
     * @return int
     */
    public function drawSmallPrize(User $user)
    {
        $prizeItem = $this->getPrizeItem(PrizeItem::TYPE_SMALL, $this->getPointBalance());
        $points = $prizeItem->getPoints();
        if ($points > 0) {
            $this->userService->addPoints($user, $points, CategoryType::EVENT_PRIZE, TaskType::RENTENTION, '抽奖');
            $this->minusPointBalance($points);
        }
        $this->minusPrizeQuantity($prizeItem);
        return $points;
    }

    /**
     * 抽奖.
     *
     * @param PrizeTicket $prizeTicket
     * @return int
     */
    public function drawPrize(PrizeTicket $prizeTicket)
    {
        $points = 0;
        if ($prizeTicket->getType() == PrizeItem::TYPE_BIG) {
            $points = $this->drawBigPrize($prizeTicket->getUser());
        } elseif ($prizeTicket->getType() == PrizeItem::TYPE_SMALL) {
            $points = $this->drawSmallPrize($prizeTicket->getUser());
        }
        return $points;
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
     * 创建一张奖券即一次抽奖机会.
     *
     * @param User $user
     * @param $type
     * @param null $comment
     * @return PrizeTicket
     */
    public function createPrizeTicket(User $user, $type, $comment = null)
    {
        $prizeTicket = new PrizeTicket();
        $prizeTicket->setUser($user);
        $prizeTicket->setType($type);
        $prizeTicket->setComment($comment);
        $prizeTicket->setCreatedAt(new \DateTime());
        $this->em->persist($prizeTicket);
        $this->em->flush();
        return $prizeTicket;
    }

    /**
     * 回答商业问卷后得到抽奖机会.
     *
     * @param User $user
     * @param $answerStatuts
     */
    public function createPrizeTicketForResearchSurvey(User $user, $answerStatus, $comment) {
        if ($answerStatus == $this->parameterService->getParameter('research_survey_status_complete')) {
            $this->createPrizeTicket($user, PrizeItem::TYPE_BIG, $comment);
        } elseif ($answerStatus == $this->parameterService->getParameter('research_survey_status_screenout')) {
            $this->createPrizeTicket($user, PrizeItem::TYPE_SMALL, $comment);
        } elseif ($answerStatus == $this->parameterService->getParameter('research_survey_status_quotafull')) {
            $this->createPrizeTicket($user, PrizeItem::TYPE_SMALL, $comment);
        }
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
     * 减去奖品库存.
     *
     * @param PrizeItem $prizeItem
     */
    private function minusPrizeQuantity(PrizeItem $prizeItem) {
        $quantity = $prizeItem->getQuantity() - 1;
        if ($quantity < 0) {
            $quantity = 0;
        }
        $prizeItem->setQuantity($quantity);
        $this->em->flush($prizeItem);
    }
}