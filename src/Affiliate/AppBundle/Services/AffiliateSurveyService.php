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
    * @param string $affiliateProjectId
    * @return string
    */
    public function getSurveyUrl($affiliateProjectId){
        $this->logger->debug(__METHOD__ . " START  affiliateProjectId=" .  $affiliateProjectId . PHP_EOL);
        
        $surveyUrl = null;
        // 查找一个出于init状态的问卷url，并将其的状态更改为forward
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneById($affiliateProjectId);
            $param = array(
            'affiliateProject' => $affiliateProject,
            'status' => AffiliateUrlHistory::SURVEY_STATUS_INIT
            );

            $affiliateUrlHistory = $this->em->getRepository('AffiliateAppBundle:AffiliateUrlHistory')->findOneBy($param);
            if($affiliateUrlHistory == null || sizeof($affiliateUrlHistory) == 0){
                
            } else {
                $affiliateUrlHistory->setStatus(AffiliateUrlHistory::SURVEY_STATUS_FORWARD);
                $affiliateUrlHistory->setUpdatedAt(date_create());
                $affiliateProject->minusInitNum();
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $surveyUrl = $affiliateUrlHistory->getSurveyUrl();
                $this->logger->info(__METHOD__ . " Clicked    affiliateProjectId=" .  $affiliateProjectId . " url=" . $affiliateUrlHistory->getSurveyUrl() . PHP_EOL);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error(__METHOD__ . " Error    affiliateProjectId=" .  $affiliateProjectId . " errMsg=" . $e->getMessage() . PHP_EOL);
        }

        $this->logger->debug(__METHOD__ . " END    affiliateProjectId=" .  $affiliateProjectId . PHP_EOL);
        return $surveyUrl;
    }

    /**
    * @param string $status
    * @param string $uKey
    * @return array(
    *               'status' => xxx
    *               'complete_point' => xxx
    *               'ukey' => xxx)
    */
    public function processEndpage($status = AffiliateUrlHistory::SURVEY_STATUS_ERROR, $uKey = null){
        $this->logger->debug(__METHOD__ . " START  uKey=" .  $uKey . " status=" . $status . PHP_EOL);

        $rtn = array();
        $rtn['status'] = $status;
        $rtn['complete_point'] = 0;
        $rtn['ukey'] = $uKey;

        // status参数必须存在 complete screenout quotafull error
        // 不是以上4种的时候全部作为error处理
        $validStatus = array(
                AffiliateUrlHistory::SURVEY_STATUS_COMPLETE,
                AffiliateUrlHistory::SURVEY_STATUS_SCREENOUT,
                AffiliateUrlHistory::SURVEY_STATUS_QUOTAFULL,
                AffiliateUrlHistory::SURVEY_STATUS_ERROR
                );
        // 如果status不合法，则强制变更为error
        if(! in_array($status, $validStatus)){
            $rtn['status'] = AffiliateUrlHistory::SURVEY_STATUS_ERROR;
            $this->logger->info(__METHOD__ . " END    uKey=" .  $uKey . " invalid status=" . $status . PHP_EOL);
            return $rtn;
        }

        if(is_null($uKey)){
            $this->logger->info(__METHOD__ . " Warn    uKey not provided. status=" . $status . PHP_EOL);
            return $rtn;
        }

        // uKey 存在时，查找这个ukey的回答状态
        $param = array(
            'uKey' => $uKey,
            'status' => AffiliateUrlHistory::SURVEY_STATUS_FORWARD
        );

        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateUrlHistory = $this->em->getRepository('AffiliateAppBundle:AffiliateUrlHistory')->findOneBy($param);
            if($affiliateUrlHistory == null || sizeof($affiliateUrlHistory) == 0){
                // 没有找到状态为forward的url，认为这个endpage的request不合法，将页面显示状态改为error
                $rtn['status'] = AffiliateUrlHistory::SURVEY_STATUS_ERROR;
            } else{
                $affiliateUrlHistory->setStatus($rtn['status']);
                $this->em->flush();
                // 更行状态成功，获取奖励积分数
                $rtn['complete_point'] = $affiliateUrlHistory->getAffiliateProject()->getCompletePoints();
            }
            
            $this->logger->info(__METHOD__ . " END    uKey=" .  $uKey . " process status=" . $rtn['status'] . "  complete_point=" . $rtn['complete_point'] . PHP_EOL);
            $connection->commit();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . " Error    uKey=" .  $uKey . " errMsg=" . $e->getMessage() . PHP_EOL);
            $rtn['status'] = AffiliateUrlHistory::SURVEY_STATUS_ERROR;
            $rtn['complete_point'] = 0;
            $connection->rollBack();
        }
        
        return $rtn;
    }
    
}