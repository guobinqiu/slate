<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\AppBundle\Entity\SsiRespondent;

class SsiDeliveryNotification extends DeliveryNotification
{
    private $em;

    protected $logger;

    public function setLogger($logger){
        $this->logger = $logger;
    }

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function send(array $respondents)
    {
        $this->logger->debug('send START');
        $respondentIds = $respondents;
        for ($i = 0; $i < count($respondentIds); $i++) {
            $this->logger->info('Start process respondentId=' . $respondentIds[$i]);
            $ssiRespondentId = SsiRespondent::parseRespondentId($respondentIds[$i]);
            $recipient = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->retrieveRecipientDataToSendMailById($ssiRespondentId);
            $this->logger->info('Got recipient ' . json_encode($recipient));
            if ($recipient && $this->isSubscribed($recipient['email'])) {
                $respondent['recipient'] = $recipient;
                $name1 = $respondent['recipient']['name1'];
                if ($name1 == null) {
                    $name1 = $respondent['recipient']['email'];
                }
                $job = new Job('mail:ssi_delivery_notification', array(
                    '--name1='.$name1,
                    '--email='.$respondent['recipient']['email'],
                    '--survey_title=SSI海外调查',
                    '--survey_point=180',
                    '--subject=亲爱的'.$name1.'，您的新问卷来了！',
                    //'--channel='.$this->getChannel($i),//sendcloud
                ), true, '91wenwen');
                $this->logger->info('To persist Job memory=' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB');
                $this->em->persist($job);
            }
            $this->logger->info('End process respondentId=' . $respondentIds[$i]);
        }
        $this->em->flush();
        $this->em->clear();
        $this->logger->debug('send END');
    }

    // sendcloud
//    private function getChannel($i) {
//        return $i % 2 == 0 ? 'channel2' : 'channel3';
//    }
}