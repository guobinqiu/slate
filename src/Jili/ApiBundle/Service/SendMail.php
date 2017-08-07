<?php
namespace Jili\ApiBundle\Service;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;

/**
 *
 **/
class SendMail
{
    private $logger;
    private $mailer;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getParameter($key)
    {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c)
    {
        $this->container_ = $c;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMails($subject,$email,$content)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom(array($this->getParameter('qqmail_sender') => '91问问调查网'))
        ->setTo($email)
        ->setBody($content, 'text/html');
        return $this->mailer->send($message);
    }
}
