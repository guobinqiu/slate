<?php
namespace Jili\ApiBundle\Services\Bangwoya;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBagInterface;

/**
 *
 **/
class BangwoyaRequestValidation {

    private $em;
    private $configs;
    private $logger;

    function __construct($configs) {
        $this->configs = $configs;
    }

    /**
     *
     * @return void
     **/
    public function validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp) {

        $result = array (
            'valid_flag' => true,
            'code' => ''
        );

        $configs = $this->configs;

        //1.缺少参数(检查参数是否传递,nonceStr是否为32位)
        $nonceStlength = strlen($nonceStr);
        if (empty ($partnerid) || empty ($vmoney) || empty ($tid) || $nonceStlength != 32) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][1]['errorno'];
            return $result;
        }

        //2.密码验证不通过$nonceStr= md5($safec ode.$partnerid.$vmoney.$tid)
        $password = md5($configs['key'] . $partnerid . $vmoney . $tid);
        if (strtolower($nonceStr) != $password) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][2]['errorno'];
            return $result;
        }

        //3.tid重复
        $o = $this->em->getRepository("JiliApiBundle:BangwoyaOrder")->findOneByTid($tid);
        if (!is_null($o)) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][3]['errorno'];
            return $result;
        }

        //4.vmoney超过最大限额
        if ($vmoney > $configs['upper_limit']) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][4]['errorno'];
            return $result;
        }

        //5.partnerid不存在
        $user = $this->em->getRepository("JiliApiBundle:User")->findOneById($partnerid);
        if (is_null($user)) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][5]['errorno'];
            return $result;
        }

        //6.非法IP 程序里获得ip地址的地方
        if ($clientIp != $configs['client_ip']) {
            $result['valid_flag'] = false;
            $result['code'] = $configs['validations'][6]['errorno'];
            return $result;
        }

        //7.指令收到，充值需要等待

        return $result;
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

}