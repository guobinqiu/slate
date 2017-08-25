<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\SopRespondent;
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
use Wenwen\FrontendBundle\Model\OwnerType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

class UserService
{
    private $em;
    private $redis;
    private $serializer;
    private $pointService;
    private $parameterService;

    public function __construct(EntityManager $em,
                                Client $redis,
                                Serializer $serializer,
                                LoggerInterface $logger,
                                PointService $pointService,
                                ParameterService $parameterService)
    {
        $this->em = $em;
        $this->redis = $redis;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->pointService = $pointService;
        $this->parameterService = $parameterService;
    }

    public function getSopCredentialsByOwnerType($ownerType)
    {
        if (!OwnerType::isValid($ownerType)) {
            throw new \InvalidArgumentException('Unsupported owner_type: ' . $ownerType);
        }
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        foreach($sopApps as $sopApp) {
            if (!isset($sopApp['owner_type'])) {
                throw new \InvalidArgumentException("Missing option 'owner_type'");
            }
            if ($sopApp['owner_type'] == $ownerType) {
                if (!isset($sopApp['app_id'])) {
                    throw new \InvalidArgumentException("Missing option 'app_id'");
                }
                if (!isset($sopApp['app_secret'])) {
                    throw new \InvalidArgumentException("Missing option 'app_secret'");
                }
                return $sopApp;
            }
        }
        throw new \InvalidArgumentException('SopCredentials was not found. owner_type=' . $ownerType);
    }

    public function getSopCredentialsByAppId($appId)
    {
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        foreach($sopApps as $sopApp) {
            if (!isset($sopApp['app_id'])) {
                throw new \InvalidArgumentException("Missing option 'app_id'");
            }
            if ($sopApp['app_id'] == $appId) {
                if (!isset($sopApp['app_secret'])) {
                    throw new \InvalidArgumentException("Missing option 'app_secret'");
                }
                return $sopApp;
            }
        }
        throw new \InvalidArgumentException('SopCredentials was not found. appId=' . $appId);
    }

    public function getAllSopCredentials()
    {
        $sopApps = $this->parameterService->getParameter('sop_apps');
        if (is_null($sopApps)) {
            throw new \InvalidArgumentException("Missing option 'sop_apps'");
        }
        return $sopApps;
    }

    public function getAppIdByOwnerType($ownerType)
    {
        $sopCredentials = $this->getSopCredentialsByOwnerType($ownerType);
        return $sopCredentials['app_id'];
    }

    public function getAppSecretByOwnerType($ownerType)
    {
        $sopCredentials = $this->getSopCredentialsByOwnerType($ownerType);
        return $sopCredentials['app_secret'];
    }

    public function getAppSecretByAppId($appId)
    {
        $sopCredentials = $this->getSopCredentialsByAppId($appId);
        return $sopCredentials['app_secret'];
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

    public function getSopRespondentByUserId($userId) {
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(['userId' => $userId]);
        if (null === $sopRespondent) {
            throw new \Exception('SopRespondent was not found. userId=' . $userId);
        }
        return $sopRespondent;
    }

    public function createSopRespondent($userId, $ownerType) {
        $appId = $this->getAppIdByOwnerType($ownerType);
        $sopRespondent = new SopRespondent();
        $i = 0;
        while ($this->isAppMidDuplicated($sopRespondent->getAppMid())) {
            $sopRespondent->setAppMid(SopRespondent::generateAppMid());
            $i++;
            if ($i > 1000) {
                break;
            }
        }
        $sopRespondent->setUserId($userId);
        $sopRespondent->setStatusFlag(SopRespondent::STATUS_ACTIVE);
        $sopRespondent->setAppId($appId);
        $this->em->persist($sopRespondent);
        $this->em->flush();
        return $sopRespondent;
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

    public function createUserTrack(User $user, $clientIp, $fingerprint, $recruitRoute, $oauth = null)
    {
        $userTrack = new UserTrack();
        $userTrack->setLastFingerprint(null);
        $userTrack->setCurrentFingerprint($fingerprint);
        $userTrack->setSignInCount(1);
        $userTrack->setLastSignInAt(null);
        $userTrack->setCurrentSignInAt(new \DateTime());
        $userTrack->setLastSignInIp(null);
        $userTrack->setCurrentSignInIp($clientIp);
        $userTrack->setOauth($oauth);
        $userTrack->setRegisterRoute($recruitRoute);
        $userTrack->setUser($user);
        $this->em->persist($userTrack);
        $this->em->flush();
        return $userTrack;
    }

    public function updateUserTrack(UserTrack $userTrack, $clientIp, $fingerprint, $oauth = null)
    {
        $userTrack->setLastFingerprint($userTrack->getLastFingerprint());
        $userTrack->setCurrentFingerprint($fingerprint);
        $userTrack->setSignInCount($userTrack->getSignInCount() + 1);
        $userTrack->setLastSignInAt($userTrack->getCurrentSignInAt());
        $userTrack->setCurrentSignInAt(new \DateTime());
        $userTrack->setLastSignInIp($userTrack->getCurrentSignInIp());
        $userTrack->setCurrentSignInIp($clientIp);
        $userTrack->setOauth($oauth);
        $this->em->flush();
    }

    public function saveOrUpdateUserTrack(User $user, $clientIp, $fingerprint, $recruitRoute, $oauth = null)
    {
        $userTrack = $user->getUserTrack();
        if ($userTrack) {
            $this->updateUserTrack($userTrack, $clientIp, $fingerprint, $oauth);
        } else {
            $this->createUserTrack($user, $clientIp, $fingerprint, $recruitRoute, $oauth);
        }
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

    private function isAppMidDuplicated($key)
    {
        return count($this->em->getRepository('JiliApiBundle:SopRespondent')->findByAppMid($key)) > 0;
    }
}
