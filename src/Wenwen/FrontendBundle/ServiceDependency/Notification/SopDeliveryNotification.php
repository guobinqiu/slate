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
        $job = new Job('mail:sop_delivery_notification', array(
            '--name1='.$name1,
            '--email='.$respondent['recipient']['email'],
            '--survey_title='.$respondent['title'],
            '--survey_point='.$respondent['extra_info']['point']['complete'],
            '--survey_length='.$respondent['loi'],
            '--subject=亲爱的'.$name1.'，为您呈上一份价值'.$respondent['extra_info']['point']['complete'].'分的新问卷（编号：r'.$respondent['survey_id'].'）',
            '--survey_id='.$respondent['survey_id'],
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
