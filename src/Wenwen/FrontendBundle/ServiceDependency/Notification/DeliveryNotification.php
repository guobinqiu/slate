<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

abstract class DeliveryNotification
{
    protected $em;

    /**
     * @param array $respondents
     * @return array|void
     */
    abstract public function send(array $respondents);

    protected function getRecipient($app_mid) {
        return $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($app_mid);
    }

    protected function isSubscribed($email) {
        $userEdmUnsubscribes = $this->em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByEmail($email);
        return count($userEdmUnsubscribes) == 0;
    }

    public function __construct(EntityManager $em, SopSurveyService $sopSurveyService) {
        $this->em = $em;
    }
}