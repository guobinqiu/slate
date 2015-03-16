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
        $pointsExchangeType = new PointsExchangeType();

        $em = $this->em;

        $valid = $this->checkData($data);
        if(!$valid){
            //写log
            $content = "[flow_order_request_processor] 推送数据格式不正确" . var_dump($data);
            FileUtil :: writeContents($log_path, $content);
            return false;
        }

        //get point exchange id
        $exchangeFlowOrder = $em->getRepository('JiliApiBundle:ExchangeFlowOrder')->find($data['custom_order_sn']);
        if(!($exchangeFlowOrder && $exchangeFlowOrder->getExchangeId())){
            //写log
            $content = "[flow_order_request_processor] 该订单不存在exchange_flow_order_id" . $data['custom_order_sn'];
            FileUtil :: writeContents($log_path, $content);
            return false;
        }

        $pointsExchangeType = new PointsExchangeType();
        $type = $pointsExchangeType :: TYPE_FLOW;
        if ($data['status'] == 'error') {
            //兑换失败
            return $this->exchange_service->exchangeNg($exchangeFlowOrder->getExchangeId(), null, null, $type, $log_path);
        }
        elseif ($data['status'] == 'success') {
            //兑换成功
            return $this->exchange_service->exchangeOK($exchangeFlowOrder->getExchangeId(), null, null, $type, $log_path);
        }

        return true;
    }

    public function checkData($data) {
        if(!($data['status'] && $data['custom_order_sn'])){
            return false;
        }

        if (!($data['status'] == 'error' || $data['status'] == 'success')) {
            return false;
        }

        return true;
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function getParameter($key) {
        return $this->container->getParameter($key);
    }

    public function setSendMail($send_mail)
    {
        $this->send_mail = $send_mail;
    }

    public function setExchange($exchange_service) {
        $this->exchange_service = $exchange_service;
    }
}