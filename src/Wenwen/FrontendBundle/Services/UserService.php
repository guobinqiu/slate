<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\QQUser;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\WeiboUser;
use Wenwen\FrontendBundle\Entity\WeixinUser;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserService
{
    private $em;
    private $redis;
    private $serializer;
    private $parameterService;

    public function __construct(EntityManager $em,
                                $redis,
                                $serializer,
                                ParameterService $parameterService,
                                LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->parameterService = $parameterService;
        $this->logger = $logger;
    }

    /**
     * @param QQUser|WeixinUser|WeiboUser $xxxUser
     * @param $userProfile
     * @param $clientIp
     * @param $userAgent
     * @param $inviteId
     * @param $allowRewardInviter bool
     * @throws \Exception
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
            $news = $this->buildNews($user, $points, $categoryType, $taskType);
            $this->insertLatestNews($news);
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
        return $this->getList(CacheKeys::PROVINCE_LIST, 'Wenwen\FrontendBundle\Entity\ProvinceList');
    }

    public function getCityList() {
        return $this->getList(CacheKeys::CITY_LIST, 'Wenwen\FrontendBundle\Entity\CityList');
    }

    /**
     * 插入一条最新动态
     *
     * @param $news string
     */
    public function insertLatestNews($news)
    {
        $cacheSettings = $this->parameterService->getParameter('cache_settings');
        if (!$cacheSettings['enable']) {
            return;
        }

        $latestNewsList = $this->getLatestNews();
        $count = array_unshift($latestNewsList, $news);
        if ($count > 100) {
            array_pop($latestNewsList);
        }
        $this->redis->set(CacheKeys::LATEST_NEWS_LIST, $this->serializer->serialize($latestNewsList, 'json'));
    }

    /**
     * 显示最新动态
     *
     * @return array
     */
    public function getLatestNews()
    {
        $cacheSettings = $this->parameterService->getParameter('cache_settings');
        if (!$cacheSettings['enable']) {
            return array();
        }

        $val = $this->redis->get(CacheKeys::LATEST_NEWS_LIST);
        if (is_null($val)) {
            return array();
        }
        return $this->serializer->deserialize($val, 'array', 'json');
    }

    private function getList($key, $className) {
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

    public function buildNews(User $user, $points, $categoryType, $taskType) {
        $message = substr($user->getNick(), 0, 3) . '**';
        switch($taskType) {
            case TaskType::CPA:
                $message .= '任务墙';
                break;
            case TaskType::CPS:
                $message .= '购物返利';
                break;
            case TaskType::SURVEY:
                $message .= '商业问卷';
                break;
            case TaskType::RENTENTION:
                switch($categoryType) {
                    case CategoryType::SOP_EXPENSE:
                    case CategoryType::SSI_EXPENSE:
                    case CategoryType::CINT_EXPENSE:
                    case CategoryType::FULCRUM_EXPENSE:
                        $message .= '属性问卷';
                        break;
                    case CategoryType::SIGNUP:
                        $message .= '完成注册';
                        break;
                    case CategoryType::QUICK_POLL:
                        $message .= '快速问答';
                        break;
                    case CategoryType::EVENT_INVITE_SIGNUP:
                        $message .= '邀请好友';
                        break;
                    case CategoryType::EVENT_INVITE_SURVEY:
                        $message .= '好友答问卷';
                        break;
                }
        }
        $message .= '获得' . $points . '积分';
        return $message;
    }
}