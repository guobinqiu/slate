<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserDevice;
use Wenwen\FrontendBundle\Entity\UserProfile;

class AuthenticationService
{
    private $logger;
    private $em;
    private $redis;
    private $sopRespondentService;
    private $userTrackService;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                Client $redis,
                                SopRespondentService $sopRespondentService,
                                UserTrackService $userTrackService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->redis = $redis;
        $this->sopRespondentService = $sopRespondentService;
        $this->userTrackService = $userTrackService;
    }

    public function userDeviceLogin($params)
    {
        $userDevice = $this->em->getRepository('WenwenFrontendBundle:UserDevice')->findOneBy(array(
            'udid' => $params['device_id'],
            'type' => $params['device_type']
        ));
        if (!$userDevice) {
            $user = new User();
            $user->setNick(uniqid('guest'));
            $user->setIconPath(null);
            $user->setRegisterCompleteDate(new \DateTime());
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($params['client_ip']);
            $user->setLoginAs('guest');

            $userDevice = new UserDevice();
            $userDevice->setUdid($params['device_id']);
            $userDevice->setType($params['device_type']);
            $userDevice->setNotifiable(true);
            $userDevice->setOsVersion($params['os_version']);
            $userDevice->setUser($user);
            $user->setUserDevice($userDevice);

            $userProfile = new UserProfile();
            $user->setUserProfile($userProfile);
            $userProfile->setUser($user);

            $this->em->persist($user);
            $this->em->flush();

            $this->userTrackService->createUserTrack($user, $params['client_ip'], null, $params['recruit_route'], $params['owner_type']);
            $this->sopRespondentService->createSopRespondent($user->getId());
        } else {
            $user = $userDevice->getUser();
            $this->userTrackService->updateUserTrack($user);
        }

        return array('auth_token' => $this->generateAuthToken($user), 'user' => $user);
    }

    private function generateAuthToken(User $user)
    {
        $authToken = md5(uniqid($user->getUniqId(), true));
        $this->redis->set($authToken, $user->getUniqId());
        $this->redis->expire($authToken, 1800);
        return $authToken;
    }
}