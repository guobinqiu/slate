<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Services\SurveyFulcrumService;

class FulcrumDeliveryNotification implements DeliveryNotification
{
    private $em;
    private $surveyFulcrumService;

    public function __construct(EntityManager $em, SurveyFulcrumService $surveyFulcrumService) {
        $this->em = $em;
        $this->surveyFulcrumService = $surveyFulcrumService;
    }

    public function send(array $respondents) {
        $this->surveyFulcrumService->createResearchSurvey($respondents[0]);
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $this->surveyFulcrumService->createStatusHistory($respondent['app_mid'], $respondent['survey_id'], SurveyStatus::STATUS_TARGETED);
        }
    }
}