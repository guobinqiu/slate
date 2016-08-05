<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * 对不具备用户系统的网站做商业调查的代理
 * 目前只对应Triples系统
 * 1，返回一个实际的问卷URL
 */
class FreeSurveyService
{
    private $logger;

    private $em;

    private $parameterService;

    const SURVEY_STATUS_INIT      = 'init'; // url的初始状态，被导入的时候设置
    const SURVEY_STATUS_FORWARD   = 'forward'; // 有用户点击后，被分配出去的url，不能再次被分配
    const SURVEY_STATUS_COMPLETE  = 'complete'; // 用户完成，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_SCREENOUT = 'screenout'; // 用户screenout，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_QUOTAFULL = 'quotafull'; // 用户quotafull，triples那边回调后更新（暂时没做）
    const SURVEY_STATUS_ERROR     = 'error'; // 用户可能已经在别处回答过问卷了，客户那边直接拒绝（暂时没做）

    const PROJECT_STATUS_OPEN      = 'open'; // 执行中的项目
    const PROJECT_STATUS_CLOSE     = 'close';// 结束的项目

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;

    }

    /**
    * 检查指定partnerId和projectId的状态是否为执行中的项目
    */
    public function validateProjectStatus($partnerId, $projectId){
        $this->logger->debug(__METHOD__ . " START partnerId=" . $partnerId . "  projectId=" .  $projectId . PHP_EOL);

        $param = array(
            'partnerId' => $partnerId,
            'projectId' => $projectId,
            'status' => self::PROJECT_STATUS_OPEN
            );

        $freeProjectHistory = $this->em->getRepository('WenwenFrontendBundle:FreeProjectHistory')->findOneBy($param);

        $rtn = false;
        if($freeProjectHistory == null || sizeof($freeProjectHistory) != 1){
            $rtn = false;
        } else {
            $rtn = true;
        }
        $this->logger->debug(__METHOD__ . " END   partnerId=" . $partnerId . "  projectId=" .  $projectId . " project status=" . $rtn . PHP_EOL);
        return $rtn;
    }

    /**
    * 返回一个实际的问卷URL
    * @param string $partnerId
    * @param string $projectId
    * @return string
    */
    public function getSurveyUrl($partnerId, $projectId){
        $this->logger->debug(__METHOD__ . " START partnerId=" . $partnerId . "  projectId=" .  $projectId . PHP_EOL);
        
        $param = array(
            'partnerId' => $partnerId,
            'projectId' => $projectId,
            'status' => self::SURVEY_STATUS_INIT
            );

        $surveyUrl = null;
        // 查找一个出于init状态的问卷url，并将其的状态更改为forward
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $freeSurveyHistory = $this->em->getRepository('WenwenFrontendBundle:FreeSurveyHistory')->findOneBy($param);
            if($freeSurveyHistory == null || sizeof($freeSurveyHistory) == 0){
                
            } else {
                $freeSurveyHistory->setStatus(self::SURVEY_STATUS_FORWARD);
                $freeSurveyHistory->setUpdatedAt(date_create());
                $this->em->flush();
                $surveyUrl = $freeSurveyHistory->getSurveyUrl();
                $this->logger->info(__METHOD__ . " Clicked   partnerId=" . $partnerId . "  projectId=" .  $projectId . " url=" . $freeSurveyHistory->getSurveyUrl() . PHP_EOL);
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $this->logger->error(__METHOD__ . " Error   partnerId=" . $partnerId . "  projectId=" .  $projectId . " errMsg=" . $e->getMessage() . PHP_EOL);
        }

        $this->logger->debug(__METHOD__ . " END   partnerId=" . $partnerId . "  projectId=" .  $projectId . PHP_EOL);
        return $surveyUrl;
    }

}