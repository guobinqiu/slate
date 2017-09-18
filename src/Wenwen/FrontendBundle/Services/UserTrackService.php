<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Entity\User;

class UserTrackService
{
    private $em;
    private $logger;

    public function __construct(EntityManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function createUserTrack(User $user, $clientIp = null, $fingerprint = null, $recruitRoute = null, $ownerType = 'dataspring')
    {
        $userTrack = new UserTrack();
        $userTrack->setLastFingerprint(null);
        $userTrack->setCurrentFingerprint($fingerprint);
        $userTrack->setSignInCount(1);
        $userTrack->setLastSignInAt(null);
        $userTrack->setCurrentSignInAt(new \DateTime());
        $userTrack->setLastSignInIp(null);
        $userTrack->setCurrentSignInIp($clientIp);
        $userTrack->setRegisterRoute($recruitRoute);
        $userTrack->setOwnerType($ownerType);
        $userTrack->setUser($user);
        $user->setUserTrack($userTrack);
        $this->em->persist($userTrack);
        $this->em->flush();
        return $userTrack;
    }

    public function updateUserTrack(User $user, $clientIp = null, $fingerprint = null)
    {
        $userTrack = $user->getUserTrack();
        $userTrack->setLastFingerprint($userTrack->getLastFingerprint());
        $userTrack->setCurrentFingerprint($fingerprint);
        $userTrack->setSignInCount($userTrack->getSignInCount() + 1);
        $userTrack->setLastSignInAt($userTrack->getCurrentSignInAt());
        $userTrack->setCurrentSignInAt(new \DateTime());
        $userTrack->setLastSignInIp($userTrack->getCurrentSignInIp());
        $userTrack->setCurrentSignInIp($clientIp);
        $this->em->flush();
        return $userTrack;
    }
}