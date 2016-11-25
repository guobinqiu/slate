<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Services\SopSurveyService;

class SopDeliveryNotification extends DeliveryNotification
{
    protected $sopSurveyService;

    public function __construct(EntityManager $em, SopSurveyService $sopSurveyService) {
        $this->em = $em;
        $this->sopSurveyService = $sopSurveyService;
    }

    public function send(array $respondents) {
        //$unsubscribed_app_mids = array();
        $this->sopSurveyService->createResearchSurvey($respondents[0]);
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $this->sopSurveyService->createStatusHistory($respondent['app_mid'], $respondent['survey_id'], SurveyStatus::STATUS_TARGETED);
            $recipient = $this->getRecipient($respondent['app_mid']);
            if ($recipient['email']) {
                if ($this->isSubscribed($recipient['email'])) {
                    $respondent['recipient'] = $recipient;
                    //$channel = $this->getChannel($i);
                    $this->runJob($respondent);
                }
            }
            //else {
                // 没有 email 并不代表这个app_mid不存在，不用返回信息给SOP那边
                //$unsubscribed_app_mids[] = $respondent['app_mid'];
            //}
        }
        //return $unsubscribed_app_mids;
    }

    private function runJob($respondent, $channel = null) {
        $name1 = $respondent['recipient']['name1'];
        if ($name1 == null) {
            $name1 = $respondent['recipient']['email'];
        }

        $toAddress = $respondent['recipient']['email'];
        $surveyTitle = $respondent['title'];
        $completePoint = $respondent['extra_info']['point']['complete'];
        $loi = $respondent['loi'];
        $surveyId = $respondent['survey_id'];

        // 随机从下面挑选一个名称作为邮件标题
        $subjects = array(
            '亲爱的' . $name1 . '，为您呈上一份价值' . $completePoint . '分的新问卷（编号：r' . $surveyId . '）',
            '91问问邀请您参加名为<r' . $surveyId . ' ' . $surveyTitle . '>的问卷，回答成功奖励为' . round($completePoint/100, 0) . '元',
            );
        srand();
        $subjectIndex = rand(0, count($subjects) - 1);
        $subject = $subjects[$subjectIndex];

        $job = new Job('mail:sop_delivery_notification', array(
            '--name1='.$name1,
            '--email='.$toAddress,
            '--survey_title='.$surveyTitle,
            '--survey_point='.$completePoint,
            '--survey_length='.$loi,
            '--subject='.$subject,
            '--survey_id='.$surveyId,
            //'--channel='.$channel,//sendcloud
        ), true, '91wenwen');
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }

//    private function getChannel($i) {
//        return $i % 2 == 0 ? 'channel2' : 'channel3';
//    }
}
