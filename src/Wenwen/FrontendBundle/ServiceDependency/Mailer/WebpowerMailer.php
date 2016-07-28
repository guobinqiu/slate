<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

class WebpowerMailer implements IMailer {

    private $mailer;

    private $from;

    private $sender;

    public function __construct($from, $host, $username, $password, $sender)
    {
        $transport = \Swift_SmtpTransport::newInstance();
        $transport->setHost($host);
        $transport->setUsername($username);
        $transport->setPassword($password);
        $transport->setEncryption('tls');

        $this->mailer = \Swift_Mailer::newInstance($transport);
        $this->from = $from;
        $this->sender = $sender;
    }

    /**
     * Send mailing.
     *
     * @param $to 收件人
     * @param $subject 邮件主题
     * @param $html 邮件正文
     * @return array
     */
    public function send($to, $subject, $html)
    {
        $result = array(
            'email' => $to,
            'sent_at' => new \DateTime(),
        );

        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom(array($this->from => '91问问调查网'));
        $message->setSender($this->sender);
        $message->setTo($to);
        $message->setBody($html, 'text/html');
        
        if ($this->mailer->send($message) > 0) {
            $result['result'] = true;
        } else {
            $result['result'] = false;
        }
        $this->mailer->getTransport()->stop();
        return $result;
    }

    /**
     * @return string 邮件服务商名
     */
    public function getName()
    {
        return 'webpower';
    }
}