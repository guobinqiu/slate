<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\SopRespondent;
use Psr\Log\LoggerInterface;

class SopRespondentService
{
    private $em;
    private $logger;
    private $surveySopService;

    public function __construct(EntityManager $em, LoggerInterface $logger, SurveySopService $surveySopService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->surveySopService = $surveySopService;
    }

    public function getSopRespondentByUserId($userId) {
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->findOneBy(['userId' => $userId]);
        if (null === $sopRespondent) {
            $sopRespondent = $this->createSopRespondent($userId);
        }
        return $sopRespondent;
    }

    public function createSopRespondent($userId) {
        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($userId);

        $appId = $this->surveySopService->getAppIdByOwnerType($user->getUserTrack()->getOwnerType());
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

    private function isAppMidDuplicated($key)
    {
        return count($this->em->getRepository('JiliApiBundle:SopRespondent')->findByAppMid($key)) > 0;
    }
}