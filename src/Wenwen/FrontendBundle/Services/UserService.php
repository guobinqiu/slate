<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use JMS\Serializer\Serializer;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\QQUser;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\WeiboUser;
use Wenwen\FrontendBundle\Entity\WeixinUser;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserService
{
    private $em;
    private $redis;
    private $serializer;

    public function __construct(EntityManager $em,
                                Client $redis,
                                Serializer $serializer,
                                LoggerInterface $logger)
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function getUserBySopRespondentAppMid($appMid) {
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid($appMid);
        if (null === $sopRespondent) {
            throw new \Exception('SopRespondent was not found. appMid=' . $appMid);
        }
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($sopRespondent->getUserId());
        if (null === $user) {
            throw new \Exception('User was not found. userId=' . $sopRespondent->getUserId());
        }
        return $user;
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

    public function createUser(User $user, $clientIp, $userAgent, $inviteId, $canRewardInviter)
    {
        $user->setCreatedRemoteAddr($clientIp);
        $user->setCreatedUserAgent($userAgent);
        if ($canRewardInviter) {
            $user->setInviteId($inviteId);
        }
        $i = 0;
        while ($this->isUniqIdDuplicated($user->getUniqId())) {
            $user->setUniqId(User::generateUniqId());
            $i++;
            if ($i > 1000) {
                break;
            }
        }
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function createUserByQQUser(QQUser $qqUser, UserProfile $userProfile, $clientIp, $userAgent, $inviteId, $canRewardInviter)
    {
        $user = new User();
        $user->setNick($qqUser->getNickname());
        $user->setIconPath($qqUser->getPhoto());
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setLastLoginIp($clientIp);

        $qqUser->setUser($user);
        $user->setQQUser($qqUser);

        $userProfile->setUser($user);
        $user->setUserProfile($userProfile);

        return $this->createUser($user, $clientIp, $userAgent, $inviteId, $canRewardInviter);
    }

    public function createUserByWeixinUser(WeixinUser $weixinUser, UserProfile $userProfile, $clientIp, $userAgent, $inviteId, $canRewardInviter)
    {
        $user = new User();
        $user->setNick($weixinUser->getNickname());
        $user->setIconPath($weixinUser->getPhoto());
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setLastLoginIp($clientIp);

        $weixinUser->setUser($user);
        $user->setWeixinUser($weixinUser);

        $userProfile->setUser($user);
        $user->setUserProfile($userProfile);

        return $this->createUser($user, $clientIp, $userAgent, $inviteId, $canRewardInviter);
    }

    public function createUserByWeiboUser(WeiboUser $weiboUser, UserProfile $userProfile, $clientIp, $userAgent, $inviteId, $canRewardInviter)
    {
        $user = new User();
        $user->setNick($weiboUser->getNickname());
        $user->setIconPath($weiboUser->getPhoto());
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setLastLoginDate(new \DateTime());
        $user->setLastLoginIp($clientIp);

        $weiboUser->setUser($user);
        $user->setWeiboUser($weiboUser);

        $userProfile->setUser($user);
        $user->setUserProfile($userProfile);

        return $this->createUser($user, $clientIp, $userAgent, $inviteId, $canRewardInviter);
    }

    public function createQQUser($openId, $userInfo)
    {
        $qqUser = new QQUser();
        $qqUser->setOpenId($openId);
        $qqUser->setNickname($userInfo->nickname);
        $qqUser->setPhoto($userInfo->figureurl_qq_1);
        $qqUser->setGender($userInfo->gender == '女' ? 2 : 1);
        $this->em->persist($qqUser);
        $this->em->flush();
    }

    public function createWeixinUser($openId, $userInfo)
    {
        $weixinUser = new WeixinUser();
        $weixinUser->setOpenId($openId);
        $weixinUser->setNickname($userInfo->nickname);
        $weixinUser->setPhoto($userInfo->headimgurl);
        $weixinUser->setGender($userInfo->sex);
        $weixinUser->setUnionId($userInfo->unionid);
        $this->em->persist($weixinUser);
        $this->em->flush();
    }

    public function createWeiboUser($openId, $userInfo)
    {
        $weiboUser = new WeiboUser();
        $weiboUser->setOpenId($openId);
        $weiboUser->setNickname($userInfo->screen_name);
        $weiboUser->setPhoto($userInfo->profile_image_url);
        $weiboUser->setGender($userInfo->gender == 'f' ? 2 : 1);
        $this->em->persist($weiboUser);
        $this->em->flush();
    }

    /**
     * 推送用户基本信息
     */
    public function pushBasicProfileJob($userId)
    {
        $args = array(
            '--user_id=' . $userId,
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
        $job->setMaxRetries(3);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * 如果用户把cookie删了，就通过fingerprint来判断，fingerprint相同的邀请不给分
     */
    public function canRewardInviter($isUserLoggedIn, $fingerprint)
    {
        if (!$isUserLoggedIn) {
            $userTrack = $this->em->getRepository('WenwenFrontendBundle:UserTrack')->findOneBy(array('currentFingerprint' => $fingerprint));
            if ($userTrack == null) {
                return true;
            }
        }
        return false;
    }

    private function isUniqIdDuplicated($key)
    {
        return count($this->em->getRepository('WenwenFrontendBundle:User')->findByUniqId($key)) > 0;
    }
}
