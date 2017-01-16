<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\SurveySop;
use Wenwen\FrontendBundle\Entity\SurveySopParticipationHistory;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * AdminSurvey
 */
class AdminSurveySopService
{
    private $logger;

    private $em;

    private $parameterService;

    private $knp_paginator;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                $knp_paginator)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->knp_paginator = $knp_paginator;
    }

    public function getSurveySopList($page, $limit = 10){

    }

    /**
     * 查找一个问卷项目
     * @param $surveryId
     */
    public function findSurveySop($surveryId) {
        $this->logger->debug(__METHOD__ . ' START ');

        $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveySop')->findOneById($surverySopId);

        $this->logger->debug(__METHOD__ . ' END   ');
        return $surveyPartner;
    }

    public function getSurveySopParticipationSummary($surveryPartner){

    }

    public function getSurveySopParticipationDetail($surveryId, $page, $limit = 10){
        
    }

    public function getParticipationHistoriesByUserId($userId){
        $results = $this->em->getRepository('WenwenFrontendBundle:SurveySopParticipationHistory')->getByUserIdGroupBySurveyId($userId);
        $this->logger->debug(__METHOD__ . ' results=' . json_encode($results));
        return $results;
    }

    public function getSurveySopParticipationDailyReport(\DateTime $from = null, \DateTime $to = null){
        
    }


}
