<?php

namespace Affiliate\AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\JobQueueBundle\Entity\Job;



/**
 * 
 */
class AdminProjectService
{
    private $logger;

    private $em;

    private $knp_paginator;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                $knp_paginator)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->knp_paginator = $knp_paginator;
    }

    public function getProjectList($partnerId, $page, $limit){
        $pagination = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->getProjects($partnerId, $this->knp_paginator, $page, $limit);
        
        return $pagination;
    }

    public function asynchUploadUrl($projectId, $fullPath){
        $job = new Job(
            'affiliate:urlUpload', 
            array(
                '--projectId='.$projectId,
                '--urlFile='.$fullPath,
            ), 
            true, 
            'affiliate', 
            Job::PRIORITY_HIGH);
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }

    /**
    * 检查指定partnerId和projectId的状态是否为执行中的项目
    */
    public function validateProjectStatus($projectId){
        $this->logger->debug(__METHOD__ . " START  projectId=" .  $projectId . PHP_EOL);

        $param = array(
            'projectId' => $projectId,
            'status' => AffiliateProject::PROJECT_STATUS_OPEN
            );

        $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneBy($param);

        $rtn = false;
        if($affiliateProject == null || sizeof($affiliateProject) != 1){
            $rtn = false;
        } else {
            $rtn = true;
        }
        $this->logger->debug(__METHOD__ . " END    projectId=" .  $projectId . " project status=" . $rtn . PHP_EOL);
        return $rtn;
    }

    /**
    * Create a new project related to RFQ
    * @param string $partnerId
    * @param string $RFQId
    */
    public function initProject($partnerId, $RFQId, $originalFileName, $fullPath){

        $status = 'success';
        $msg = '';
        try{
            $affiliateProject = new AffiliateProject();
            $affiliateProject->setPartnerId($partnerId);
            $affiliateProject->setRFQId($RFQId);
            $affiliateProject->setOriginalFileName($originalFileName);
            $affiliateProject->setRealFullPath($fullPath);
            $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_INIT);
            $this->em->persist($affiliateProject);
            $this->em->flush();
            $projectId = $affiliateProject->getProjectId();
            $msg = " Created new project. partnerId=" . $partnerId . "  RFQId=" .  $RFQId;
            $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            // 异步动作
            $this->asynchUploadUrl($projectId, $fullPath);
        } catch (Exception $e) {
            $status = 'failure';
            $msg = " Failed to create new project. partnerId=" . $partnerId . "  RFQId=" .  $RFQId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }

        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    public function openProject($projectId, $initNum){
        $status = 'success';
        $msg = '';
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneByProjectId($projectId);
            if($affiliateProject == null || sizeof($affiliateProject) == 0){
                $status = 'failure';
                $msg = " Project not found   projectId=" . $projectId;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } else {
                $affiliateProject->setInitNum($initNum);
                $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_OPEN);
                $affiliateProject->setUrl('http://www.91wenwen.net/affiliate/survey/' . $affiliateProject->getProjectId());
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $msg = " Project closed   projectId=" . $projectId;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $status = 'failure';
            $msg = " Error   projectId=" . $projectId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }
        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    public function closeProject($projectId){
        $status = 'success';
        $msg = '';
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneByProjectId($projectId);
            if($affiliateProject == null || sizeof($affiliateProject) == 0){
                $status = 'failure';
                $msg = " Project not found   projectId=" . $projectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } else if($affiliateProject->setStatus != AffiliateProject::PROJECT_STATUS_OPEN) {
                $status = 'failure';
                $msg = " Project not open   projectId=" . $projectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } else {
                $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_CLOSE);
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $msg = " Project closed   projectId=" . $projectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $status = 'failure';
            $msg = " Error   projectId=" . $projectId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }
        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    /**
    * @param string $projectId
    * @param array $urlsAndKeys =(
    *                             'ukey' => string, (max: 30 bytes)
    *                             'url' => string, (max: 255 bytes)
    *                            )
    */
    public function importSurveyUrl($projectId, $urlsAndUkeys){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        
        $rtn['status'] = 'success';
        $rtn['count'] = 0;
        $rtn['errmsg'] = '';
        $rtn['failedUrls'] = '';
        
        $failedUrls = array();
        $count = 0;
        foreach($urlsAndUkeys as $urlsAndUkey){
            try{
                $affiliateUrlHistory = new AffiliateUrlHistory();
                $affiliateUrlHistory->setUKey($urlsAndUkey['ukey']);
                $affiliateUrlHistory->setProjectId($projectId);
                $affiliateUrlHistory->setSurveyUrl($urlsAndUkey['url']);
                $affiliateUrlHistory->setStatus(AffiliateUrlHistory::SURVEY_STATUS_INIT);
                $this->em->persist($affiliateUrlHistory);
                $count ++;
                if($count % 2000 == 0){
                    $this->em->flush();
                    $this->em->clear();
                }
            } catch (Exception $e){
                $urlsAndUkey['errmsg'] = $e->getMessage();
                $failedUrls[] = $urlsAndUkey;
                $this->logger->error(__METHOD__ . " Failed to add url.   errmsg=" . $urlsAndUkey['errmsg'] . PHP_EOL);
            }
        }
        $this->em->flush();
        $this->em->clear();

        if(sizeof($failedUrls) > 0){
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = 'Failed to import some urls.';
            $rtn['failedUrls'] = $failedUrls;
        }
        
        $rtn['count'] = $count;
        $this->logger->debug(__METHOD__ . " END   " . PHP_EOL);
        return $rtn;
    }

}