<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Services\FulcrumSurveyService;

class FulcrumDeliveryNotification extends DeliveryNotification
{
    protected $fulcrumSurveyService;

    public function __construct(EntityManager $em, FulcrumSurveyService $fulcrumSurveyService) {
        $this->em = $em;
        $this->fulcrumSurveyService = $fulcrumSurveyService;
    }

    public function send(array $respondents) {
        $this->fulcrumSurveyService->createResearchSurvey($respondents[0]);
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $this->fulcrumSurveyService->createStatusHistory($respondent['app_mid'], $respondent['survey_id'], SurveyStatus::STATUS_TARGETED);
//            $recipient = $this->getRecipient($respondent['app_mid']);
//            if ($recipient['email']) {
//                if ($this->isSubscribed($recipient['email'])) {
//                    $respondent['recipient'] = $recipient;
//                    $this->runJob($respondent);
//                }
//            }
        }
    }

    private function runJob($respondent) {
        $name1 = $respondent['recipient']['name1'];
        if ($name1 == null) {
            $name1 = $respondent['recipient']['email'];
        }
        $job = new Job('mail:fulcrum_delivery_notification', array(
            '--name1='.$name1,
            '--email='.$respondent['recipient']['email'],
            '--survey_title='.$respondent['title'],
            '--survey_point='.$respondent['extra_info']['point']['complete'],
            '--subject=亲爱的'.$name1.'，您的新问卷来了！',
        ), true, '91wenwen');
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }
}