<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;

/**
 * AdminSurvey
 */
class AdminSurveyPartnerService
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

    public function getSurveyPartnerList($page, $limit = 10){
        $pagination = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->getSurveyPartners($this->knp_paginator, $page, $limit);
        
        return $pagination;
    }

    /**
     * 开启问卷项目
     * @param $surveryPartnerId
     */
    public function openSurveyPartner($surveryPartnerId) {
        $this->logger->debug(__METHOD__ . ' START ');
        try{
            $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneById($surveryPartnerId);
            $surveyPartner->setStatus(SurveyPartner::STATUS_OPEN);
            $this->em->persist($surveyPartner);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ErrorMsg:   ' . $e->getMessage());
            $this->logger->error(__METHOD__ . ' ErrorStack:   ' . $e->getTraceAsString());
            $this->em->close();
            throw $e;
        }

        $this->logger->debug(__METHOD__ . ' END   ');
    }

    /**
     * 关闭问卷项目
     * @param $surveryPartnerId
     */
    public function closeSurveyPartner($surveryPartnerId) {
        $this->logger->debug(__METHOD__ . ' START ');

        try{
            $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneById($surveryPartnerId);
            $surveyPartner->setStatus(SurveyPartner::STATUS_CLOSE);
            $this->em->persist($surveyPartner);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ErrorMsg:   ' . $e->getMessage());
            $this->logger->error(__METHOD__ . ' ErrorStack:   ' . $e->getTraceAsString());
            $this->em->close();
            throw $e;
        }

        $this->logger->debug(__METHOD__ . ' END   ');
    }

    /**
     * 新建问卷项目
     * @param $partnerId
     */
    public function createUpdateSurveyPartner(SurveyPartner $surveyPartner) {
        $this->logger->debug(__METHOD__ . ' START ');

        if(! preg_match('/\?*=__UID__/', $surveyPartner->getUrl())){
            // url里没有__UID__
            throw new \Exception('问卷入口url里必须有一个参数的值为__UID__');
        }

        try{
            $this->em->persist($surveyPartner);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' ErrorMsg:   ' . $e->getMessage());
            $this->logger->error(__METHOD__ . ' ErrorStack:   ' . $e->getTraceAsString());
            $this->em->close();
            throw $e;
        }

        $this->logger->debug(__METHOD__ . ' END   ');
    }

    /**
     * 新建问卷项目
     * @param $surveryPartnerId
     */
    public function findSurveyPartner($surveryPartnerId) {
        $this->logger->debug(__METHOD__ . ' START ');

        $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneById($surveryPartnerId);

        return $surveyPartner;

        $this->logger->debug(__METHOD__ . ' END   ');
    }

    public function getSurveyPartnerParticipationSummary($surveryPartner){
        $summary = array();

        $initCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_INIT);
        $forwardCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_FORWARD);
        $completeCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_COMPLETE);
        $screenoutCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_SCREENOUT);
        $quotafullCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_QUOTAFULL);
        $errorCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyPartnerParticipationHistory::STATUS_ERROR);

        $summary['initCount'] = $initCount;
        $summary['forwardCount'] = $forwardCount;
        $summary['completeCount'] = $completeCount;
        $summary['screenoutCount'] = $screenoutCount;
        $summary['quotafullCount'] = $quotafullCount;
        $summary['errorCount'] = $errorCount;
        return $summary;
    }

    public function getSurveyPartnerParticipationDetail($surveryPartner, $page, $limit = 10){

        $pagination = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getSurveyPartnersParticipationHistorys($surveryPartner, $this->knp_paginator, $page, $limit);
        return $pagination;

        
    }

}
