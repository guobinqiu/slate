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

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($param) {
        $configs = $this->configs;

        $url = $configs['url'] . 'createorder_api.php'; //todo
        $prv_key = $configs['prv_key'];
        $custom_sn = $configs['custom_sn'];

        $out_arr['custom_sn'] = 'custom_sn=' . $custom_sn;
        $out_arr['custom_product_id'] = 'custom_product_id=' . $param['custom_product_id'];
        $out_arr['mobile'] = 'mobile=' . $param['mobile'];
        $out_arr['custom_order_sn'] = 'custom_order_sn=' . $param['custom_order_sn'];

        $encText = FlowUtil :: params_md5($out_arr, $prv_key);

        $param['custom_sn'] = $custom_sn;
        $param['enctext'] = $encText;

        $return = CurlUtil :: curl($url, $param);

        //解析接口数据
        $data = json_decode($return, true);

        //写log
        $log_path = $configs['file_path_flow_api_log'];
        if ($data['resultcode'] != 101) {
            $content = "[createorder_api]url:" . $url . ' return:' . $return . FlowUtil :: $CREATEORDER_API_ERROR[$data['resultcode']];
            FileUtil :: writeContents($log_path, $content);
        }

        //显示给用户的错误信息
        if (in_array($data['resultcode'], array (
                204,
                205,
                206,
                209,
                210
            ))) {
            $data['error_message'] = FlowUtil :: $CREATEORDER_API_ERROR[$data['resultcode']];
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