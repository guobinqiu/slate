<?php

namespace Wenwen\FrontendBundle\Services\Dependency\Notification;

interface DeliveryNotification
{
    /**
     * @param array $respondents
     * @return array|void
     */
    public function send(array $respondents);
}