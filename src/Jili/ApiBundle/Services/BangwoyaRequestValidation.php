<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBagInterface;

/**
 *
 **/
class BangwoyaRequestValidation {

    /**
       * @var array
       */
    protected $configs;

    /**
    * @var \Doctrine\ORM\EntityManager
    */
    protected $em;

    /**
    * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
    */
    protected $logger;

    function __construct($configs) {
        $this->configs = $configs;
    }

    /**
     *
     * @return void
     **/
    public function validate($tid, $partnerid, $vmoney, $nonceStr) {

        $result = array (
            'valid_flag' => true,
            'code' => ''
        );

        //1.缺少参数(检查参数是否传递,nonceStr是否为32位)
        $nonceStlength = strlen($nonceStr);
        if (empty ($partnerid) || empty ($vmoney) || empty ($tid) || $nonceStlength != 32) {
            $result['valid_flag'] = false;
            $result['code'] = '1001';
            return $result;
        }

        //2.密码验证不通过$nonceStr= md5($safec ode.$partnerid.$vmoney.$tid)
        $password = md5($this->configs['key'] . $partnerid . $vmoney . $tid);
        echo "password=>".$password."\r\n";
        if (strtolower($nonceStr) != $password) {
            $result['valid_flag'] = false;
            $result['code'] = '1002';
            return $result;
        }

        //3.tid重复
        $o = $this->em->getRepository("JiliApiBundle:BangwoyaOrder")->findOneByTid($tid);
        if (!is_null($o)) {
            $result['valid_flag'] = false;
            $result['code'] = '1003';
            return $result;
        }

        //4.vmoney超过最大限额

        //5.partnerid不存在
        $user = $this->em->getRepository("JiliApiBundle:User")->findOneById($partnerid);
        if (is_null($user)) {
            $result['valid_flag'] = false;
            $result['code'] = '1005';
            return $result;
        }

        //6.非法IP 程序里获得ip地址的地方

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