<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

use Wenwen\FrontendBundle\ServiceDependency\HttpClient;
use Wenwen\FrontendBundle\Services\ParameterService;

class SendCloudMailerFactory {

    private static $instances = array();

    private function __construct(){}

    /**
     * @return SendCloudMailer
     */
    public static function createMailer(ParameterService $parameterService, HttpClient $httpClient, $channel) {
        if (!isset(self::$instances[$channel])) {
            $mailer = $parameterService->getParameter('mailer');
            $sendcloud = $mailer['sendcloud'];
            $account = $sendcloud[$channel];

            self::$instances[$channel] = new SendCloudMailer(
                $account['api_user'],
                $account['api_key'],
                $sendcloud['url'],
                $account['from'],
                $httpClient
            );
        }
        return self::$instances[$channel];
    }
}