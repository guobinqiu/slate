<?php
namespace Jili\ApiBundle\Service;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;

/**
 *
 **/
class SendMail {

    private $logger;
    private $soap_mail;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function sendMailForRegisterFromWenwen($email, $url) {

        //通过soap发送
        $this->soap_mail->setCampaignId($this->getParameter('register_from_wenwen_campaign_id')); //活动id
        $this->soap_mail->setMailingId($this->getParameter('register_from_wenwen_mailing_id')); //邮件id
        $this->soap_mail->setGroup(array (
            'name' => '从91问问注册积粒网',
            'is_test' => 'false'
        )); //group
        $recipient_arr = array (
            array (
                'name' => 'email',
                'value' => $email
            ),
            array (
                'name' => 'url_reg',
                'value' => $url
            )
        );
        $send_email = $this->soap_mail->sendSingleMailing($recipient_arr);
        if ($send_email == "Email send success") {
            return true;
        } else {
            return false;
        }
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c) {
        $this->container_ = $c;
    }

    public function setSoapMail($soap_mail) {
        $this->soap_mail = $soap_mail;
    }
}
