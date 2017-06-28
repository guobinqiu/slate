<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Entity\QQUser;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
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
    private $pointService;

    public function __construct(EntityManager $em,
                                Client $redis,
                                Serializer $serializer,
                                LoggerInterface $logger,
                                PointService $pointService
    ) {
        $this->em = $em;
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->pointService = $pointService;
    }

    public function toUserId($app_mid) {
        $arr = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($app_mid);
        if (empty($arr)) {
            return new NotFoundHttpException('No user_id matches the app_mid');
        }
        return $arr['id'];
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
        $this->pointService->addPoints(
            $user,
            User::POINT_SIGNUP,
            CategoryType::SIGNUP,
            TaskType::RENTENTION,
            '完成注册',
            null,
            new \DateTime(),
            true,
            PrizeItem::TYPE_SMALL
        );

        // 给邀请人加积分
        $this->pointService->addPointsForInviter(
            $user,
            User::POINT_INVITE_SIGNUP,
            CategoryType::EVENT_INVITE_SIGNUP,
            TaskType::RENTENTION,
            '您的好友' . $user->getNick(). '完成了注册',
            null,
            new \DateTime(),
            true,
            PrizeItem::TYPE_SMALL
        );

        return $user;
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

    public function getProvinceList() {
        return $this->getPlaceList(CacheKeys::PROVINCE_LIST, 'Wenwen\FrontendBundle\Entity\ProvinceList');
    }

    public function getCityList() {
        return $this->getPlaceList(CacheKeys::CITY_LIST, 'Wenwen\FrontendBundle\Entity\CityList');
    }

    private function getPlaceList($key, $className) {
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

    /**
     * Check whether a fingerprint already used at registration
     * @param $fingerprint
     * @param $maxExpireTime default 2592000 seconds (30 days)
     * @param $maxCount default 2592000
     * @return boolean
     */
    public function isRegisteredFingerPrint($fingerprint, $maxExpireTime = 2592000, $maxCount = 2592000){
        $key = CacheKeys::REGISTER_FINGER_PRINT_PRE . $fingerprint;
        $penaltyExpireTime = CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT;
        if($this->redis->exists($key)){
            // if same fingerprint already exists Stop registration and plus expiring time.
            $count = $this->redis->get($key);

            $ttl = $this->redis->ttl($key);
            $expireSeconds = $ttl + $penaltyExpireTime;
            if($expireSeconds > $maxExpireTime){
                $expireSeconds = $maxExpireTime;
            }
            if($count < $maxCount){
                $count++;
            }
            $this->redis->del($key);
            $this->redis->set($key, $count);
            $this->redis->expire($key, $expireSeconds);
            return true;
        } else {
            // if same fingerprint not exists => registration
            $this->redis->set($key, 1);
            $this->redis->expire($key, $penaltyExpireTime);
            return false;
        }
    }
}
