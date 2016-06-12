<?php

namespace Wenwen\FrontendBundle\Services;

interface IMailer {

    /**
     * 发送.
     *
     * @param $to 收件人
     * @param $subject 邮件主题
     * @param $html 邮件正文
     * @return mixed true|false|string|json
     */
    public function send($to, $subject, $html);

    /**
     * @return string 邮件服务商名
     */
    public function getName();
}