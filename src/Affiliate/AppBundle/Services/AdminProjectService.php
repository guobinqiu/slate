<?php

namespace Affiliate\AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Affiliate\AppBundle\Entity\AffiliateProject;
use Affiliate\AppBundle\Entity\AffiliateUrlHistory;
use JMS\JobQueueBundle\Entity\Job;



/**
 * 
 */
class AdminProjectService
{
    private $logger;

    private $em;

    private $knp_paginator;

    private $router;

    const COMMAND = 'affiliate:urlUpload';
    const QUEUE_NAME = 'affiliate';

    const BASE_URL = 'http://www.91wenwen.net';

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                $knp_paginator,
                                $router)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->knp_paginator = $knp_paginator;
        $this->router = $router;
    }

    public function getProjectList($affiliatePartnerId, $page, $limit){
        $this->logger->debug(__METHOD__ . " START  affiliatePartnerId=" .  $affiliatePartnerId . PHP_EOL);

        $rtn = array(
            'status' => 'success',
            'errmsg' => '',
            'pagination' => array()
            );
        try{
            $affiliatePartner = $this->em->getRepository('AffiliateAppBundle:AffiliatePartner')->findOneById($affiliatePartnerId);
            $this->logger->debug(__METHOD__ . 'sizeof $affiliatePartner=' . sizeof($affiliatePartner));
            $pagination = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findProjects($affiliatePartner, $this->knp_paginator, $page, $limit);
            $this->logger->debug(__METHOD__ . 'sizeof $pagination=' . sizeof($pagination));
            $rtn['pagination'] = $pagination;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . 'err');
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = $e->getMessage();
            $this->logger->error(__METHOD__ . 'errmsg=' . $rtn['errmsg']);
        }
        $this->logger->debug(__METHOD__ . " END  affiliatePartnerId=" .  $affiliatePartnerId . PHP_EOL);
        return $rtn;
    }

    /**
    * @param string $affiliateProjectId
    * @param string $fullPath
    */
    public function asynchUploadUrl($affiliateProjectId, $fullPath){
        $job = new Job(
            self::COMMAND, 
            array(
                '--affiliateProjectId='.$affiliateProjectId,
                '--urlFile='.$fullPath,
            ), 
            true, 
            self::QUEUE_NAME, 
            Job::PRIORITY_HIGH);
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }

    /**
    * 检查指定projectId的状态是否为执行中的项目
    * @param integer $affiliateProjectId
    */
    public function validateProjectStatus($affiliateProjectId){
        $this->logger->debug(__METHOD__ . " START  affiliateProjectId=" .  $affiliateProjectId . PHP_EOL);

        $rtn = array();
        $rtn['status'] = 'success';
        try{
            $param = array(
                'id' => $affiliateProjectId,
                'status' => AffiliateProject::PROJECT_STATUS_OPEN
                );

            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneBy($param);

            if($affiliateProject == null || sizeof($affiliateProject) != 1){
                $rtn['status'] = 'failure';
                $rtn['errmsg'] = 'This project is not found or closed.';
            }
        } catch(\Exception $e){
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = 'Error happened. Errmsg=' . $e->getMessage();
            $this->logger->error(__METHOD__ . " validate failed    affiliateProjectId=" .  $affiliateProjectId . "errMsg=" . $rtn['errmsg'] . PHP_EOL);
            
        }
        $this->logger->debug(__METHOD__ . " END    affiliateProjectId=" .  $affiliateProjectId . PHP_EOL);
        return $rtn;
    }

    /**
    * Create a new project related to RFQ
    * Add record in affiliate_project with status init
    * Add a job for asynch upload url files
    * @param integer $affiliatePartnerId
    * @param integer $RFQId
    * @param string $originalFileName
    * @param string $fullPath
    */
    public function initProject($affiliatePartnerId, $RFQId, $originalFileName, $fullPath){

        $status = 'success';
        $msg = '';
        try{
            $affiliatePartner = $this->em->getRepository('AffiliateAppBundle:AffiliatePartner')->findOneById($affiliatePartnerId);

            $affiliateProject = new AffiliateProject();
            $affiliateProject->setAffiliatePartner($affiliatePartner);
            $affiliateProject->setRFQId($RFQId);
            $affiliateProject->setOriginalFileName($originalFileName);
            $affiliateProject->setRealFullPath($fullPath);
            $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_INIT);
            $this->em->persist($affiliateProject);
            $this->em->flush();
            $affiliateProjectId = $affiliateProject->getId();
            $msg = " Created new project. affiliatePartnerId=" . $affiliatePartnerId . "  RFQId=" .  $RFQId;
            $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            // 异步动作
            $this->asynchUploadUrl($affiliateProjectId, $fullPath);
        } catch (\Exception $e) {
            $status = 'failure';
            $msg = " Failed to create new project. affiliatePartnerId=" . $affiliatePartnerId . "  RFQId=" .  $RFQId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }

        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    /**
     *  @param integer $affiliateProjectId 
     *  @param integer $initNum
     */
    public function openProject($affiliateProjectId, $initNum){
        $status = 'success';
        $msg = '';
        $affiliateUrl = self::BASE_URL . $this->router->generate('affilate_survey', array('affiliateProjectId' => $affiliateProjectId));

        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneById($affiliateProjectId);
            if($affiliateProject == null || sizeof($affiliateProject) == 0){
                $status = 'failure';
                $msg = " Project not found   affiliateProjectId=" . $affiliateProjectId;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } else {
                $affiliateProject->setInitNum($initNum);
                $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_OPEN);
                $affiliateProject->setUrl($affiliateUrl);
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $msg = " Project closed   affiliateProjectId=" . $affiliateProjectId;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $status = 'failure';
            $msg = " Error   affiliateProjectId=" . $affiliateProjectId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }
        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    /**
     *  @param integer $affiliateProjectId 
     */
    public function closeProject($affiliateProjectId){
        $status = 'success';
        $msg = '';
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneById($affiliateProjectId);
            if($affiliateProject == null || sizeof($affiliateProject) == 0){
                $status = 'failure';
                $msg = " Project not found   affiliateProjectId=" . $affiliateProjectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } elseif($affiliateProject->getStatus() == AffiliateProject::PROJECT_STATUS_CLOSE) {
                // already closed no action
                $msg = " Project already closed   affiliateProjectId=" . $affiliateProjectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            } else {
                $affiliateProject->setStatus(AffiliateProject::PROJECT_STATUS_CLOSE);
                $affiliateProject->setUpdatedAt(date_create());
                $this->em->flush();
                $msg = " Project closed   affiliateProjectId=" . $affiliateProjectId ;
                $this->logger->info(__METHOD__ . $msg . PHP_EOL);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $status = 'failure';
            $msg = " Error   affiliateProjectId=" . $affiliateProjectId . " errMsg=" . $e->getMessage();
            $this->logger->error(__METHOD__ . $msg . PHP_EOL);
        }
        return array(
            'status' => $status,
            'msg' => $msg
            );
    }

    /**
    * @param integer $affiliateProjectId
    * @param array $urlsAndKeys =(
    *                             'ukey' => string, (max: 30 bytes)
    *                             'url' => string, (max: 255 bytes)
    *                            )
    */
    public function importSurveyUrl($affiliateProjectId, $urlsAndUkeys){
        $this->logger->debug(__METHOD__ . " START " . PHP_EOL);
        
        $rtn['status'] = 'success';
        $rtn['count'] = 0;
        $rtn['errmsg'] = '';
        $rtn['failedUrls'] = '';

        $affiliateProject = null;
        try{
            $affiliateProject = $this->em->getRepository('AffiliateAppBundle:AffiliateProject')->findOneById($affiliateProjectId);
        } catch(\Exception $e) {
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = 'Failed to find affiliateProject. affiliateProjectId=' . $affiliateProjectId;
            return $rtn;
        }

        if(is_null($affiliateProject) || sizeof($affiliateProject) != 1){
            $rtn['status'] = 'failure';
            $rtn['errmsg'] = 'Could not find affiliateProject. affiliateProjectId=' . $affiliateProjectId;
            return $rtn;
        }

        $failedUrls = array();
        $count = 0;
        foreach($urlsAndUkeys as $urlsAndUkey){
            try{
                $affiliateUrlHistory = new AffiliateUrlHistory();
                $affiliateUrlHistory->setUKey($urlsAndUkey['ukey']);
                $affiliateUrlHistory->setAffiliateProject($affiliateProject);
                $affiliateUrlHistory->setSurveyUrl($urlsAndUkey['url']);
                $affiliateUrlHistory->setStatus(AffiliateUrlHistory::SURVEY_STATUS_INIT);
                $this->em->persist($affiliateUrlHistory);
                $count ++;
                if($count % 2000 == 0){
                    $this->em->flush();
                    $this->em->clear('AffiliateAppBundle:AffiliateUrlHistory');
                }
            } catch (\Exception $e){
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