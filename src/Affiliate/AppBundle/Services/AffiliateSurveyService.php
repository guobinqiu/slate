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

    public function processEndpage($status, $ukey = null){
        $this->logger->debug(__METHOD__ . " START  uKey=" .  $uKey . " status=" . $status . PHP_EOL);
        $rtn = array();
        $rtn['point'] = 0;

        // status参数必须存在 complete screenout quotafull error
        // 不是以上4种的时候全部作为error处理
        if(in_array($status, 
            array(
                AffiliateUrlHistory::SURVEY_STATUS_COMPLETE,
                AffiliateUrlHistory::SURVEY_STATUS_SCREENOUT,
                AffiliateUrlHistory::SURVEY_STATUS_QUOTAFULL,
                AffiliateUrlHistory::SURVEY_STATUS_ERROR
                )
            )
            ){
            $rtn['status'] = $status;
        } else {
            $rtn['status'] = AffiliateUrlHistory::SURVEY_STATUS_ERROR;
        }

        // ukey 不存在时，单纯返回status
        if(is_null($ukey)){
            return $rtn;
        }

        // ukey 存在时，查找这个ukey的回答状态
        $param = array(
            'uKey' => $uKey,
            'status' => AffiliateUrlHistory::SURVEY_STATUS_FORWARD
        );

        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateUrlHistory = $this->em->getRepository('AffiliateAppBundle:AffiliateUrlHistory')->findOneBy($param);
            if($affiliateUrlHistory == null || sizeof($affiliateUrlHistory) == 0){

            } else{
                $affiliateUrlHistory->setStatus($rtn['status']);
                $this->em->flush();
                $this->logger->info(__METHOD__ . " Finished    uKey=" .  $uKey . " status=" . $status . PHP_EOL);
            }
            $connection->commit();
            // 更行状态成功，获取奖励积分数
            $rtn['point'] = $affiliateUrlHistory->getAffiliateProject()->getIncentivePoints();
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . " Error    ukey=" .  $ukey . " errMsg=" . $e->getMessage() . PHP_EOL);
            $connection->rollBack();
        }

        $this->logger->debug(__METHOD__ . " END    uKey=" .  $uKey . " status=" . $status . PHP_EOL);
        return $rtn;
    }
    
}