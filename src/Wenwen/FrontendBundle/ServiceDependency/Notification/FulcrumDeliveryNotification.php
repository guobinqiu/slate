<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use JMS\JobQueueBundle\Entity\Job;

class FulcrumDeliveryNotification extends SopDeliveryNotification
{
    protected function runJob($respondent, $channel) {
        $name1 = $respondent['recipient']['name1'];
        if ($name1 == null) {
            $name1 = $respondent['recipient']['email'];
        }
        $job = new Job('mail:fulcrum_delivery_notification', array(
            '--name1='.$name1,
            '--email='.$respondent['recipient']['email'],
            '--survey_title='.$respondent['title'],
            '--survey_point='.$respondent['extra_info']['point']['complete'],
            '--subject=亲爱的'.$respondent['recipient']['name1'].'，您的新问卷来了！',
            //'--channel='.$channel,//sendcloud
        ), true, '91wenwen');
        $this->em->persist($job);
        $this->em->flush($job);
        $this->em->clear();
    }
}