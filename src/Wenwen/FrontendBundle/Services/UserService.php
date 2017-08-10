<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
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
            while ($this->duplicated($user->getUniqId())) {
                $user->setUniqId(Uuid::uuid1()->toString());
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

    private function duplicated($key)
    {
        return count($this->em->getRepository('WenwenFrontendBundle:User')->findByUniqId($key)) > 0;
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
     * Get how many times a fingerprint has been used at registration within punishing time period
     * @param $fingerprint
     * @return int
     */
    public function getRegisteredFingerPrintCount($fingerprint){
        $key = CacheKeys::REGISTER_FINGER_PRINT_PRE . $fingerprint;
        if($this->redis->exists($key)){
            // Get current count
            $count = $this->redis->get($key);
            // Calculate new expire time
            $newExpireSeconds = $this->redis->ttl($key) + CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT;

            // *Note* ttl will be reset to -1 when the value of key is updated
            // Update count
            if($count < CacheKeys::REGISTER_FINGER_PRINT_MAX_COUNT){
                $this->redis->set($key, ++$count);
            }

            // Update expire time
            if($newExpireSeconds > CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT){
                $this->redis->expire($key, CacheKeys::REGISTER_FINGER_PRINT_MAX_TIMEOUT);
            } else {
                $this->redis->expire($key, $newExpireSeconds);
            }

            // return the count number before update
            return $count;
        } else {
            // Record this fingerprint and set expire time
            $this->redis->set($key, 1);
            $this->redis->expire($key, CacheKeys::REGISTER_FINGER_PRINT_TIMEOUT);
            return 1;
        }
    }
}
