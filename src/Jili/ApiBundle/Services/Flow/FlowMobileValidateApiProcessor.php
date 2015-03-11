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
class FlowMobileValidateApiProcessor {

    private $em;
    private $configs;
    private $logger;

    function __construct($configs) {
        $this->configs = $configs;
    }

    public function process($mobile) {
        $configs = $this->configs;

        $url = $configs['url'] . 'mobile_validateV1.php';//todo
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