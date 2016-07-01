<?php

namespace Wenwen\FrontendBundle\ServiceDependency\Mailer;

interface IMailer {

    /**
     * Send mailing.
     *
     * @param $to 收件人
     * @param $subject 邮件主题
     * @param $html 邮件正文
     * @return array 返回一个hash数组，其中一定要包含一个key为result，value为true|false的键值对
     */
    public function send($to, $subject, $html);

    /**
     * @return string 邮件服务商名
     */
    public function getName();
}