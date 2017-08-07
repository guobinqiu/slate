<?php

namespace Wenwen\FrontendBundle\Services;

use Psr\Log\LoggerInterface;

class InternalMailService
{
    private $logger;
    private $mailer;
    private $parameterService;

    public function __construct(LoggerInterface $logger,
                                $mailer,
                                $parameterService)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->parameterService = $parameterService;
    }

    public function sendMails($subject, $email, $content)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->parameterService->getParameter('qqmail_sender') => '91问问调查网'))
            ->setTo($email)
            ->setBody($content, 'text/html');
        return $this->mailer->send($message);
    }
}
