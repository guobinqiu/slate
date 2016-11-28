<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Notification;

interface DeliveryNotification
{
    /**
     * @param array $respondents
     * @return array|void
     */
    public function send(array $respondents);
}