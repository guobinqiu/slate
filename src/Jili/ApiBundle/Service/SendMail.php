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
    private $soap_mail;
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

    public function setSoapMail($soap_mail)
    {
        $this->soap_mail = $soap_mail;
    }
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendMails($subject,$email,$content)
    {
        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom( array('account@91jili.com'=>'积粒网') )
        ->setTo($email)
        ->setBody($content,'text/html');
        $flag = $this->mailer->send($message);
        if($flag===1){
            return true;
        }else{
            return false;
        }

    }
}
