<?php
namespace Jili\ApiBundle\Services\Flow;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\FlowUtil;
use Jili\ApiBundle\Utility\CurlUtil;
use Jili\ApiBundle\Utility\FileUtil;

/**
 *
 **/
class FlowOrderCreateApiProcessor {

    private $em;
    private $configs;
    private $logger;
    private $alert_service;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($param) {
        $configs = $this->configs;

        $url = $configs['url'] . $configs['createorder_api'];
        $prv_key = $configs['prv_key'];
        $custom_sn = $configs['custom_sn'];
        $log_path = $configs['file_path_flow_api_log'];

        $out_arr['custom_sn'] = 'custom_sn=' . $custom_sn;
        $out_arr['custom_product_id'] = 'custom_product_id=' . $param['custom_product_id'];
        $out_arr['mobile'] = 'mobile=' . $param['mobile'];
        $out_arr['custom_order_sn'] = 'custom_order_sn=' . $param['custom_order_sn'];

        $encText = FlowUtil::params_md5($out_arr, $prv_key);

        $param['custom_sn'] = $custom_sn;
        $param['enctext'] = $encText;

        try {
            $return = CurlUtil::curl($url, $param);
        } catch (\Exception $e) {
            //写log
            FileUtil :: writeContents($log_path, "[flow_create_order_api]url:" . $url . $e->getMessage());
            $data['error_message'] = $configs['exchange_error'];

            $content = "兑换流量包-流量充值接口-调用失败。" . "[flow_create_order_api]url:" . $url . $e->getMessage();
            $this->alert_service->sendAlertToSlack($content);

            return $data;
        }

        //解析接口数据
        $data = json_decode($return, true);

        //返回空
        if (is_null($data)) {
            //写log
            $content = "[flow_create_order_api]url:" . $url . ' return: null';
            FileUtil :: writeContents($log_path, $content);
            $data['error_message'] = $configs['exchange_error'];
            return $data;
        }

        //正确场合
        if ($data['resultcode'] == 101) {
            return $data;
        }

        // resultcode not defined
        if(! in_array($data['resultcode'], array_keys(FlowUtil::$CREATEORDER_API_ERROR ))) {
            $content = '[flow_create_order_api]url:' . $url . ' return:' . $return .'.  resultcode not defined in FlowUtil::$CREATEORDER_API_ERROR' ;
            $this->alert_service->sendAlertToSlack($content);
            FileUtil::writeContents($log_path, $content);
            $data['error_message'] = $configs['exchange_error'];
            return $data;
        }

        // 写log
        $content = "[flow_create_order_api]url:" . $url . ' return:' . $return . FlowUtil::$CREATEORDER_API_ERROR[$data['resultcode']];
        FileUtil::writeContents($log_path, $content);

        // 出错场合：显示给用户的错误信息
        if (in_array($data['resultcode'], array (
                204,
                206,
                209,
                210
            ))) {
            $data['error_message'] = FlowUtil::$CREATEORDER_API_ERROR[$data['resultcode']];
        } else {
            // 其它的resultcode 是非用户直接相关。使用统一提示信息
            $content = '[flow_create_order_api]url:' . $url . ' return:' . $return. ', '.FlowUtil::$CREATEORDER_API_ERROR[$data['resultcode']] ;
            $this->alert_service->sendAlertToSlack($content);
            FileUtil::writeContents($log_path, $content);
            $data['error_message'] = $configs['exchange_error'];
        }

        return $data;
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function alertToSlack($alert_service) {
        $this->alert_service = $alert_service;
    }
}
