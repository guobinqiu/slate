<?php
namespace Jili\ApiBundle\Services\Flow;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\PointsExchangeType;
use Jili\ApiBundle\Utility\FileUtil;

/**
 *
 **/
class FlowOrderRequestProcessor {

    private $em;
    private $configs;
    private $logger;
    private $send_mail;
    private $exchange_service;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($data) {
        $configs = $this->configs;
        $log_path = $configs['file_path_flow_api_log'];

        $em = $this->em;

        $error_message = $this->checkData($data, $configs);
        if ($error_message) {
            //验证不通过,写log
            $content = "[flow_order_request_processor] Failure to accept data:" . $error_message . var_export($data, true);
            FileUtil :: writeContents($log_path, $content);
            return false;
        }

        //get point exchange id
        $exchangeFlowOrder = $em->getRepository('JiliApiBundle:ExchangeFlowOrder')->find($data['custom_order_sn']);
        $type = PointsExchangeType :: TYPE_FLOW;
        if ($data['status'] == 'error') {
            //兑换失败
            $return = $this->exchange_service->exchangeNg($exchangeFlowOrder->getExchangeId(), null, null, $type, $log_path);
        }
        elseif ($data['status'] == 'success') {
            //兑换成功
            $return = $this->exchange_service->exchangeOK($exchangeFlowOrder->getExchangeId(), null, null, $type, $log_path);
        }

        //处理结果写log
        if ($return) {
            $content = "[flow_order_request_processor] Handle orders successful.";
        } else {
            $content = "[flow_order_request_processor] Handle orders failure.";
            $this->sendMail($configs, $content);
        }
        FileUtil :: writeContents($log_path, $content);

        return $return;
    }

    public function checkData($data, $configs) {
        $error_message = "";

        //缺少参数
        if (empty ($data['custom_order_sn']) || empty ($data['status'])) {
            $error_message = $configs['validations'][1]['message'];
            return $error_message;
        }

        //非法IP
        if (!in_array($data['client_ip'], $configs['client_ip'])) {
            $error_message = $configs['validations'][2]['message'];
            return $error_message;
        }

        //status不正确
        if (!($data['status'] == 'success' || $data['status'] == 'error')){
             $error_message = $configs['validations'][3]['message'];
            return $error_message;
        }

        //订单不存在
        $order = $this->em->getRepository("JiliApiBundle:ExchangeFlowOrder")->find($data['custom_order_sn']);
        if (is_null($order) || is_null($order->getExchangeId())) {
            $error_message = $configs['validations'][4]['message'];
            return $error_message;
        }

        //订单已结束
        $exchanges = $this->em->getRepository('JiliApiBundle:PointsExchange')->find($order->getExchangeId());
        if ($exchanges->getFinishDate()) {
            $error_message = $configs['validations'][5]['message'];
            return $error_message;

        }
        return $error_message;
    }

    public function sendMail($configs, $content) {
        //send email
        $content = $configs['mail_subject'] . "<br><br>" . $content;
        $alertTo = $configs['alertTo_contacts'];
        $this->send_mail->sendMails($configs['mail_subject'], $alertTo, $content);
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function setSendMail($send_mail) {
        $this->send_mail = $send_mail;
    }

    public function setExchange($exchange_service) {
        $this->exchange_service = $exchange_service;
    }
}