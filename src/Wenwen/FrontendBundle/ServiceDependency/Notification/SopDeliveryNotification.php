<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;

class SopDeliveryNotification implements DeliveryNotification
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function send(array $respondents) {
        $unsubscribed_app_mids = array();
        for ($i = 0; $i < count($respondents); $i++) {
            $respondent = $respondents[$i];
            $recipient = $this->getRecipient($respondent);
            if ($recipient) {
                if ($this->isSubscribed($recipient)) {
                    $respondent['recipient'] = $recipient;
                    $channel = $this->getChannel($i);
                    $this->runJob($respondent, $channel);
                }
            } else {
                $unsubscribed_app_mids[] = $respondent['app_mid'];
            }
        }
        return $unsubscribed_app_mids;
    }

    protected function runJob($respondent, $channel) {
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
            '--subject=亲爱的'.$respondent['recipient']['name1'].'，您的新问卷来了！',
            //'--channel='.$channel,//sendcloud
        ), true, '91wenwen');
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }

    private function getRecipient($respondent) {
        return $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($respondent['app_mid']);
    }

    private function isSubscribed($recipient) {
        $userEdmUnsubscribes = $this->em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail($recipient['email']);
        return count($userEdmUnsubscribes) == 0;
    }

    private function getChannel($i) {
        return $i % 2 == 0 ? 'channel2' : 'channel3';
    }
}
