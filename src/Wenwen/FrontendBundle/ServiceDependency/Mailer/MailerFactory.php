<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

use Wenwen\FrontendBundle\ServiceDependency\HttpClient;
use Wenwen\FrontendBundle\Services\ParameterService;

class MailerFactory {

    private static $sendcloudMailers = array();

    private static $webpowerSignupMailer = null;

    private static $webpowerMailer = null;

    private function __construct(){}

    public static function createSendCloudMailer(ParameterService $parameterService, HttpClient $httpClient, $channel) {
        if (!isset(self::$sendcloudMailers[$channel])) {
            $mailer = $parameterService->getParameter('mailer');
            $sendcloud = $mailer['sendcloud'];
            $account = $sendcloud[$channel];

            self::$sendcloudMailers[$channel] = new SendCloudMailer(
                $account['api_user'],
                $account['api_key'],
                $sendcloud['url'],
                $account['from'],
                $httpClient
            );
        }
        return self::$sendcloudMailers[$channel];
    }

    public static function createWebpowerSignupMailer(ParameterService $parameterService) {
        if (is_null(self::$webpowerSignupMailer)) {
            self::$webpowerSignupMailer = new WebpowerMailer(
                $parameterService->getParameter('webpower_from'),
                $parameterService->getParameter('webpower_host'),
                $parameterService->getParameter('webpower_signup_username'),
                $parameterService->getParameter('webpower_signup_password'),
                $parameterService->getParameter('webpower_signup_sender')
            );
        }
        return self::$webpowerSignupMailer;
    }

    public static function createWebpowerMailer(ParameterService $parameterService) {
        if (is_null(self::$webpowerMailer)) {
            self::$webpowerMailer = new WebpowerMailer(
                $parameterService->getParameter('webpower_from'),
                $parameterService->getParameter('webpower_host'),
                $parameterService->getParameter('webpower_username'),
                $parameterService->getParameter('webpower_password'),
                $parameterService->getParameter('webpower_sender')
            );
        }
        return self::$webpowerMailer;
    }
}