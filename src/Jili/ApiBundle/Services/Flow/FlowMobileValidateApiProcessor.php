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

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($mobile) {
        $configs = $this->configs;

        $url = $configs['url'] . 'mobile_validateV1.php'; //todo
        $prv_key = $configs['prv_key'];
        $custom_sn = $configs['custom_sn'];

        $out_arr['custom_sn'] = 'custom_sn=' . $custom_sn;
        $out_arr['mobile'] = 'mobile=' . $mobile;
        $encText = FlowUtil :: params_md5($out_arr, $prv_key);

        $post_data['custom_sn'] = $custom_sn;
        $post_data['mobile'] = $mobile;
        $post_data['enctext'] = $encText;

        $return = CurlUtil :: curl($url, $post_data);

        //解析接口数据
        $data = json_decode($return, true);

        //写log
        $log_path = $configs['file_path_flow_api_log'];
        if ($data['resultcode'] != 200) {
            $content = "[mobile_validate]url:" . $url . ' return:' . $return . FlowUtil :: $MOBILE_VALIDATE_ERROR[$data['resultcode']];
            FileUtil :: writeContents($log_path, $content);
        }

        //显示给用户的错误信息
        if (in_array($data['resultcode'], array (
                '204',
                '206'
            ))) {
            $data['error_message'] = FlowUtil :: $MOBILE_VALIDATE_ERROR[$data['resultcode']];
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

    public function getParameter($key) {
        return $this->container->getParameter($key);
    }
}