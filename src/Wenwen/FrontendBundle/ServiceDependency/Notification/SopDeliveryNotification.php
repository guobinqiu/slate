<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Services\SurveySopService;

class SopDeliveryNotification implements DeliveryNotification
{
    private $em;
    private $surveySopService;

    public function __construct(EntityManager $em, SurveySopService $surveySopService) {
        $this->em = $em;
        $this->surveySopService = $surveySopService;
    }

    public function send(array $respondents) {
        $this->surveySopService->createSurvey($respondents[0]);
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $this->surveySopService->createParticipationByAppMid($respondent['app_mid'], $respondent['survey_id'], SurveyStatus::STATUS_TARGETED);
        }
        $job = new Job('mail:sop_delivery_notification_batch', array('--respondents=' . json_encode($respondents)), true, '91wenwen');
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }
}
