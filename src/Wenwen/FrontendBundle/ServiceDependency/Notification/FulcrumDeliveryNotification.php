<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Services\FulcrumSurveyService;

class FulcrumDeliveryNotification implements DeliveryNotification
{
    private $em;
    private $fulcrumSurveyService;

    public function __construct(EntityManager $em, FulcrumSurveyService $fulcrumSurveyService) {
        $this->em = $em;
        $this->fulcrumSurveyService = $fulcrumSurveyService;
    }

    public function send(array $respondents) {
        $this->fulcrumSurveyService->createResearchSurvey($respondents[0]);
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $this->fulcrumSurveyService->createStatusHistory($respondent['app_mid'], $respondent['survey_id'], SurveyStatus::STATUS_TARGETED);
        }
    }
}