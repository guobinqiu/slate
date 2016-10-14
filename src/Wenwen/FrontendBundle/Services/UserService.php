<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\QQUser;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\WeiboUser;
use Wenwen\FrontendBundle\Entity\WeixinUser;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserService
{
    private $em;
    private $redis;
    private $serializer;
    private $parameterService;
    private $latestNewsService;
    private $lotteryService;

    /**
     * @param EntityManager $em
     * @param Client $redis
     * @param Serializer $serializer
     * @param ParameterService $parameterService
     * @param LoggerInterface $logger
     * @param LatestNewsService $latestNewsService
     */
    public function __construct(EntityManager $em,
                                Client $redis,
                                Serializer $serializer,
                                ParameterService $parameterService,
                                LoggerInterface $logger,
                                LatestNewsService $latestNewsService,
                                LotteryService $lotteryService
    ) {
        $this->em = $em;
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->parameterService = $parameterService;
        $this->logger = $logger;
        $this->latestNewsService = $latestNewsService;
        $this->lotteryService = $lotteryService;
    }

    /**
     * 自动注册一个用户.
     *
     * @param QQUser|WeixinUser|WeiboUser $xxxUser
     * @param UserProfile $userProfile
     * @param string $clientIp
     * @param string $userAgent
     * @param int $inviteId
     * @param bool $allowRewardInviter
     *
     * @return User
     */
    public function autoRegister($xxxUser, $userProfile, $clientIp, $userAgent, $inviteId, $allowRewardInviter) {
        // 创建用户
        $user = $this->createUser(
            $xxxUser,
            $userProfile,
            $clientIp,
            $userAgent,
            $inviteId,
            $allowRewardInviter
        );

        // 给当前用户加积分
        $this->addPoints(
            $user,
            User::POINT_SIGNUP,
            CategoryType::SIGNUP,
            TaskType::RENTENTION,
            '完成注册'
        );

        // 给邀请人加积分
        $this->addPointsForInviter(
            $user,
            User::POINT_INVITE_SIGNUP,
            CategoryType::EVENT_INVITE_SIGNUP,
            TaskType::RENTENTION,
            '您的好友' . $user->getNick(). '完成了注册'
        );

        // 获得一次抽奖机会
        $this->lotteryService->createLotteryTicket($user, PrizeItem::PRIZE_BOX_SMALL);

        return $user;
    }

    public function addPoints(User $user, $points, $categoryType, $taskType, $taskName, $orderId = 0, $happenTime = null) {
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
    }

    public function addPointsForInviter(User $user, $points, $categoryType, $taskType, $taskName) {
        if ($user->getInviteId() != null) {
            $inviter = $this->em->getRepository('WenwenFrontendBundle:User')->find($user->getInviteId());
            if ($inviter != null) {
                $this->logger->info(__METHOD__ . '给邀请人加积分，邀请人ID：' . $user->getInviteId() . '，当前用户ID：' . $user->getId());
                $this->addPoints($inviter, $points, $categoryType, $taskType, $taskName);
            }
        }
    }

    public function getProvinceList() {
        return $this->getPlaceList(CacheKeys::PROVINCE_LIST, 'Wenwen\FrontendBundle\Entity\ProvinceList');
    }

    public function getCityList() {
        return $this->getPlaceList(CacheKeys::CITY_LIST, 'Wenwen\FrontendBundle\Entity\CityList');
    }

    private function createUser($xxxUser, $userProfile, $clientIp, $userAgent, $inviteId, $allowRewardInviter) {
        $this->em->getConnection()->beginTransaction();
        try {
            $user = new User();
            $user->setNick($xxxUser->getNickname());
            $user->setIconPath($xxxUser->getPhoto());
            $user->setRegisterCompleteDate(new \DateTime());
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($clientIp);
            $user->setCreatedRemoteAddr($clientIp);
            $user->setCreatedUserAgent($userAgent);
            if ($allowRewardInviter) {
                $user->setInviteId($inviteId);
            }
            $this->em->persist($user);

            $userProfile->setUser($user);
            $this->em->persist($userProfile);

            $xxxUser->setUser($user);

            $this->em->flush();
            $this->em->getConnection()->commit();

            return $user;

        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
        }
    }

    private function getPlaceList($key, $className) {
        $cacheSettings = $this->parameterService->getParameter('cache_settings');
        if (!$cacheSettings['enable']) {
            return $this->em->getRepository($className)->findAll();
        }

        $val = $this->redis->get($key);
        if (is_null($val)) {
            $entities = $this->em->getRepository($className)->findAll();
            if (!empty($entities)) {
                $this->redis->set($key, $this->serializer->serialize($entities, 'json'));
            }
            return $entities;
        }

        return $this->serializer->deserialize($val, 'array<'.$className.'>', 'json');
    }
}