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
class FlowMobileValidateApiProcessor {

    private $em;
    private $configs;
    private $logger;
    private $alert_service;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($mobile) {
        $configs = $this->configs;

        $url = $configs['url'] . $configs['mobile_validate_api'];
        $prv_key = $configs['prv_key'];
        $custom_sn = $configs['custom_sn'];
        $log_path = $configs['file_path_flow_api_log'];

        $out_arr['custom_sn'] = 'custom_sn=' . $custom_sn;
        $out_arr['mobile'] = 'mobile=' . $mobile;
        $encText = FlowUtil :: params_md5($out_arr, $prv_key);

        $post_data['custom_sn'] = $custom_sn;
        $post_data['mobile'] = $mobile;
        $post_data['enctext'] = $encText;

        try {
            $return = CurlUtil :: curl($url, $post_data);
        } catch (\Exception $e) {
            //写log
            FileUtil :: writeContents($log_path, "[mobile_validate_api]url:" . $url . $e->getMessage());
            $data['error_message'] = $configs['exchange_error'];

            $content = "兑换流量包-手机号码验证接口-调用失败。" . "[mobile_validate_api]url:" . $url . $e->getMessage();
            $this->alert_service->sendAlertToSlack($content);

            return $data;
        }

        //解析接口数据
        $data = json_decode($return, true);
        if ($data['resultcode'] == 200) {
            //处理数据，计算价格
            $data = $this->getChangePoint($data);
            return $data;
        }

        //显示给用户的错误信息
        if (in_array($data['resultcode'], array (
                '204',
                '206'
            ))) {
            $data['error_message'] = FlowUtil :: $MOBILE_VALIDATE_ERROR[$data['resultcode']];
        } else {
            $data['error_message'] = $configs['exchange_error'];
        }

        //写log
        $content = "[flow_mobile_validate]url:" . $url . var_export($data, true);
        FileUtil :: writeContents($log_path, $content);

        return $data;
    }

    public function getChangePoint($data) {
        $product_list = array ();
        if (isset ($data['product_list'])) {
            foreach ($data['product_list'] as $key => $value) {
                if ($value['custom_prise'] >= 14 && $value['custom_prise'] < 20) {
                    $value['change_point'] = round($value['custom_prise'] * 1.07, 1) * 100;
                    $product_list[] = $value;
                }
                elseif ($value['custom_prise'] >= 20 && $value['custom_prise'] < 40) {
                    $value['change_point'] = round($value['custom_prise'] * 1.03, 1) * 100;
                    $product_list[] = $value;
                }
                elseif ($value['custom_prise'] >= 40) {
                    $value['change_point'] = round($value['custom_prise'] * 1.01, 1) * 100;
                    $product_list[] = $value;
                }

                //测试，对用户开放时删除
                if ($value['custom_prise'] < 14){
                    $value['change_point'] = round($value['custom_prise'] * 1.00, 1)*100;
                    $product_list[] = $value;
                }
            }
        }
        $data['product_list'] = $product_list;
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