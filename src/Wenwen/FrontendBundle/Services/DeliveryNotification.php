<?php

namespace Wenwen\FrontendBundle\Services;

interface DeliveryNotification
{
    /**
     * @param array $respondents
     * @return array|void
     */
    public function send(array $respondents);
}