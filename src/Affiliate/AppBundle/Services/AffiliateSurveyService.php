<?php

namespace Affiliate\AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;

/**
 * 对不具备用户系统的网站做商业调查的代理
 * 目前只对应TripleS系统
 * 1，返回一个实际的问卷URL
 */
class AffiliateSurveyService
{
    private $logger;

    private $em;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
    * 返回一个实际可用的问卷URL
    * @param string $projectId
    * @return string
    */
    public function getSurveyUrl($projectId){
        $this->logger->debug(__METHOD__ . " START  projectId=" .  $projectId . PHP_EOL);
        
        $param = array(
            'projectId' => $projectId,
            'status' => AffiliateUrlHistory::SURVEY_STATUS_INIT
            );

        $surveyUrl = null;
        // 查找一个出于init状态的问卷url，并将其的状态更改为forward
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateUrlHistory = $this->em->getRepository('AffiliateAppBundle:AffiliateUrlHistory')->findOneBy($param);
            if($affiliateUrlHistory == null || sizeof($affiliateUrlHistory) == 0){
                
            } else {
                $affiliateUrlHistory->setStatus(AffiliateUrlHistory::SURVEY_STATUS_FORWARD);
                $affiliateUrlHistory->setUpdatedAt(date_create());
                $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneByProjectId($projectId);
                $affiliateProject->minusInitNum();
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $surveyUrl = $affiliateUrlHistory->getSurveyUrl();
                $this->logger->info(__METHOD__ . " Clicked    projectId=" .  $projectId . " url=" . $affiliateUrlHistory->getSurveyUrl() . PHP_EOL);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error(__METHOD__ . " Error    projectId=" .  $projectId . " errMsg=" . $e->getMessage() . PHP_EOL);
        }

        $this->logger->debug(__METHOD__ . " END    projectId=" .  $projectId . PHP_EOL);
        return $surveyUrl;
    }
    
}