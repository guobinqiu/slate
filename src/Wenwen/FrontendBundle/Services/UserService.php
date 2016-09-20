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
                                $redis, $serializer,
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
     * @param $xxxUser QQUser|WeixinUser|WeiboUser
     * @param $userProfile
     * @param $clientIp
     * @param $userAgent
     * @return User
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function autoRegister($xxxUser, $userProfile, $clientIp, $userAgent, $inviteId) {
        // 创建用户
        $user = $this->createUser(
            $xxxUser,
            $userProfile,
            $clientIp,
            $userAgent,
            $inviteId
        );

        // 给当前用户加积分
        $this->addPoints(
            $user,
            User::POINT_SIGNUP,
            CategoryType::SIGNUP,
            TaskType::RENTENTION,
            User::COMMENT_SIGNUP
        );

        // 给邀请人加积分
        $this->addPointsForInviter(
            $user,
            User::POINT_INVITE_SIGNUP,
            CategoryType::EVENT_INVITE_SIGNUP,
            TaskType::RENTENTION,
            User::COMMENT_INVITE_SIGNUP
        );
    }

    public function addPoints(User $user, $points, $categoryType, $taskType, $taskName) {
        $this->em->getConnection()->beginTransaction();
        try {
            $user->setPoints($user->getPoints() + $points);

            $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ($user->getId() % 10);
            $pointHistory = new $classPointHistory();
            $pointHistory->setUserId($user->getId());
            $pointHistory->setPointChangeNum($points);
            $pointHistory->setReason($categoryType);
            $this->em->persist($pointHistory);

            $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ($user->getId() % 10);
            $taskHistory = new $classTaskHistory();
            $taskHistory->setUserid($user->getId());
            $taskHistory->setOrderId(0);
            $taskHistory->setOcdCreatedDate(new \DateTime());
            $taskHistory->setCategoryType($categoryType);
            $taskHistory->setTaskType($taskType);
            $taskHistory->setTaskName($taskName);
            $taskHistory->setDate(new \DateTime());
            $taskHistory->setPoint($points);
            $taskHistory->setStatus(1);
            $this->em->persist($taskHistory);

            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->em->close();
            throw $e;
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

    private function createUser($xxxUser, $userProfile, $clientIp, $userAgent, $inviteId) {
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
            $user->setInviteId($inviteId);
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
}