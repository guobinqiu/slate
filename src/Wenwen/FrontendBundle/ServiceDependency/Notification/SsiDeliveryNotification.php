<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\AppBundle\Entity\SsiRespondent;

class SsiDeliveryNotification implements DeliveryNotification
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function send(array $respondents)
    {
        $respondentIds = $respondents;
        for ($i = 0; $i < count($respondentIds); $i++) {
            $ssiRespondentId = SsiRespondent::parseRespondentId($respondentIds[$i]);
            $recipient = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById($ssiRespondentId);
            if ($recipient && $this->isSubscribed($recipient)) {
                $respondent['recipient'] = $recipient;
                $job = new Job('mail:ssi_delivery_notification', array(
                    '--name1='.$respondent['recipient']['name1'],
                    '--email='.$respondent['recipient']['email'],
                    '--survey_title=SSI海外调查',
                    '--survey_point=180',
                    '--subject=亲爱的'.$respondent['recipient']['name1'].'，您的新问卷来了！',
                    '--channel='.$this->getChannel($i),
                ), true, '91wenwen');
                $this->em->persist($job);
                $this->em->flush($job);
                $this->em->clear();
            }
        }
    }

    private function isSubscribed($recipient) {
        $userEdmUnsubscribes = $this->em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail($recipient['email']);
        return count($userEdmUnsubscribes) == 0;
    }

    private function getChannel($i) {
        return $i % 2 == 0 ? 'channel2' : 'channel3';
    }
}