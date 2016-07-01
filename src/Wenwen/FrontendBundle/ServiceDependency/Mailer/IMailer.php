<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

interface IMailer {

    /**
     * Send mailing.
     *
     * @param $to 收件人
     * @param $subject 邮件主题
     * @param $html 邮件正文
     * @return mixed
     */
    public function send($to, $subject, $html);

    /**
     * @return string 邮件服务商名
     */
    public function getName();
}