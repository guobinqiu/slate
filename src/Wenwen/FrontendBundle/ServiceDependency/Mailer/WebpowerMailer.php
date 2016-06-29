<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

class WebpowerMailer implements IMailer {

    private $mailer;

    private $from;

    public function __construct($host, $username, $password, $from)
    {
        $transport = \Swift_SmtpTransport::newInstance();
        $transport->setHost($host);
        $transport->setUsername($username);
        $transport->setPassword($password);

        $this->mailer = \Swift_Mailer::newInstance($transport);
        $this->from = $from;
    }

    /**
     * Send mailing.
     *
     * @param $to 收件人
     * @param $subject 邮件主题
     * @param $html 邮件正文
     * @return mixed true|false|string|json|void
     */
    public function send($to, $subject, $html)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom(array($this->from => '91问问调查网'));
        $message->setTo($to);
        $message->setBody($html);
        $this->mailer->send($message);
    }

    /**
     * @return string 邮件服务商名
     */
    public function getName()
    {
        return 'webpower';
    }
}