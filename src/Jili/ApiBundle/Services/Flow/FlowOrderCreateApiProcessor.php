<?php
namespace Jili\ApiBundle\Services\Flow;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\FlowUtil;
use Jili\ApiBundle\Utility\CurlUtil;

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
echo "<pre>";
print_r($param);
        $url = $configs['url'] . 'createorder_api.php';//todo
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