<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Entity\SurveyPartnerParticipationHistory;
use Wenwen\FrontendBundle\Model\SurveyStatus;

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
            $surveyPartner->setUpdatedAt(new \DateTime());
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
            $surveyPartner->setUpdatedAt(new \DateTime());
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

        if(! preg_match('/uid=__UID__/', $surveyPartner->getUrl())){
            // url里没有__UID__
            throw new \Exception('问卷入口url的格式不正确!');
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
     * 查找一个问卷项目
     * @param $surveryPartnerId
     */
    public function findSurveyPartner($surveryPartnerId) {
        $this->logger->debug(__METHOD__ . ' START ');

        $surveyPartner = $this->em->getRepository('WenwenFrontendBundle:SurveyPartner')->findOneById($surveryPartnerId);

        $this->logger->debug(__METHOD__ . ' completePoint=' . $surveyPartner->getCompletePoint());
        $this->logger->debug(__METHOD__ . ' END   ');
        return $surveyPartner;
    }

    public function getSurveyPartnerParticipationSummary($surveryPartner){
        $summary = array();

        $initCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_INIT);
        $forwardCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_FORWARD);
        $completeCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_COMPLETE);
        $screenoutCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_SCREENOUT);
        $quotafullCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_QUOTAFULL);
        $errorCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getCountBySurveyPartnerAndStatus($surveryPartner, SurveyStatus::STATUS_ERROR);

        $realLOIs = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getRealLoi($surveryPartner);

        $this->logger->debug(__METHOD__ . ' realLOIs=' . json_encode($realLOIs));

        foreach($realLOIs as $key => $value){
            $this->logger->debug(__METHOD__ . ' key='. $key . ' value=' . $value);

        }

        $summary['initCount'] = $initCount;
        $summary['cvrInitToForward'] = $this->calculatePercentage($forwardCount, $initCount);
        $summary['forwardCount'] = $forwardCount;
        $summary['CSQE'] = $completeCount+$screenoutCount+$quotafullCount+$errorCount;
        $summary['cvrForwardToCSQE'] = $this->calculatePercentage($summary['CSQE'], $forwardCount);
        $summary['realIR'] = $this->calculatePercentage($completeCount, $summary['CSQE']);
        $summary['completeCount'] = $completeCount;
        $summary['screenoutCount'] = $screenoutCount;
        $summary['quotafullCount'] = $quotafullCount;
        $summary['errorCount'] = $errorCount;
        $summary['avgCompleteTime'] = $realLOIs['real_complete_time'];
        $summary['avgScreenoutTime'] = $realLOIs['real_screenout_time'];
        $summary['avgQuotafullTime'] = $realLOIs['real_quotafull_time'];
        $summary['avgErrorTime'] = $realLOIs['real_error_time'];
        return $summary;
    }

    public function getSurveyPartnerParticipationDetail($surveryPartner){

        $pagination = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getSurveyPartnersParticipationHistorys($surveryPartner, $this->knp_paginator, $page, $limit);
        return $pagination;

        
    }

    public function getSurveyPartnerParticipationDetailByUser($user){
        $results = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')->getSurveyPartnersParticipationHistorysByUser($user);
        return $results;
    }

    public function getParticipationDailyReport(\DateTime $from = null, \DateTime $to = null){
        // 默认检索一周时间的日报数据
        $from = (new \DateTime())->sub(new \DateInterval('P30D'))->setTime(0,0,0); 
        $to = (new \DateTime())->setTime(0,0,0);

        $current = clone $to;

        $dailyReports = array();

        while($current >= $from){
            $this->logger->debug(__METHOD__ . ' current=' . $current->format('Y-m-d'));
            $start = clone $current;
            $end = clone $start;
            $end->add(new \DateInterval('P01D'));

            $initCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_INIT, $start, $end);
            $forwardCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_FORWARD, $start, $end);
            $completeCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_COMPLETE, $start, $end);
            $screenoutCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_SCREENOUT, $start, $end);
            $quotafullCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_QUOTAFULL, $start, $end);
            $errorCount = $this->em->getRepository('WenwenFrontendBundle:SurveyPartnerParticipationHistory')
            ->getCountByStatus(SurveyStatus::STATUS_ERROR, $start, $end);

            $dailyReport = array();
            $dailyReport['initCount'] = $initCount;
            $dailyReport['forwardCount'] = $forwardCount;
            $dailyReport['cvrInitToForward'] = $this->calculatePercentage($forwardCount, $initCount);
            $dailyReport['CSQE'] = $completeCount+$screenoutCount+$quotafullCount+$errorCount;
            $dailyReport['cvrForwardToCSQE'] = $this->calculatePercentage($dailyReport['CSQE'], $forwardCount);
            $dailyReport['alert'] = ($dailyReport['CSQE'] > $dailyReport['forwardCount']);
            $dailyReport['completeCount'] = $completeCount;
            $dailyReport['realIR'] = $this->calculatePercentage($completeCount, $dailyReport['CSQE']);
            $dailyReport['screenoutCount'] = $screenoutCount;
            $dailyReport['quotafullCount'] = $quotafullCount;
            $dailyReport['errorCount'] = $errorCount;
            
            $dailyReports[$start->format('Y-m-d')] = $dailyReport;

            $current->sub(new \DateInterval('P01D'));
        }
        
        $result = array();
        $result['from'] = $from->format('Y-m-d');
        $result['to'] = $to->format('Y-m-d');
        $result['dailyReports'] = $dailyReports;

        return $result;
    }

    private function calculatePercentage($oldFigure, $newFigure) {
        if ($newFigure != 0) {
            $percentChange = round(($oldFigure / $newFigure) * 100, 1) . '%';
        }
        else {
            $percentChange = 'N/A';
        }
        return $percentChange;
    }

}
