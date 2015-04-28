<?php
namespace Jili\ApiBundle\Services\Duomai;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\ValidatorInterface;
use Jili\ApiBundle\Validator\Constraints\DuomaiApiOrdersPushChecksum;

/**
 *
 **/
class DuomaiRequestValidation {
    private $configs;
    private $em;
    private $logger;
    private $validator;

    function __construct($configs) {
        $this->configs = $configs;
    }

    /**
     *
     **/
    public function validate(ParameterBag $request,  $client_ip = '') 
    {
        $configs = $this->configs;
        $result = array (
            'value' => false,
            'code' => $configs['response']['FAILED'],
            'message'=>'',
            'data'=>[]
        );

        // 在订单状态为 1 或者为 2 的时候  order_price 表示确认的订单金额，siter_commission 表示
        if( '0' === $request->getAlnum('ads_id') && '测试活动'=== $request->get('ads_name') && '0' === $request->getAlnum('site_id') 
            && '0' === $request->getDigits('link_id')&& 0 === strlen( $request->get('euid') ) && '0'===$request->getAlnum('order_sn') 
            &&  '0000-00-00 00:00:00' === $request->getAlnum('order_time')&&  '0.00' === $request->getAlnum('orders_price') 
            &&'0.00' === $request->getAlnum('siter_commission') && '0'=== $request->getAlnum('status') )  {
                $result['message'] = '审核接口';
                return  $result;
            }


        $required_keys = array('ads_id', 'ads_name','site_id', 'link_id','euid','order_sn','orders_price', 'siter_commission','status');

        foreach ($required_keys as $key ) {
            if( ! $request->has($key)  ) {
                $result['message'] = 'Lack parameters';
                return  $result;
            }
        }

        $data = array('hash'=> $configs['site_hash'], 'request'=> $request->all() );

        $errors =  $this->validator->validate( $data, new DuomaiApiOrdersPushChecksum() );

        if(count($errors) > 0  ) { 
            $error_message = $errors[0]->getMessage();
            $this->logger->debug('[duomaiApi]:'. $error_message );
            $result['message'] = $error_message;
            return  $result;
        }

        // UNIQUE KEY
        // 订单是否已经存在
        // 请求的状态是否已经是过时的 
        $ocd = $request->get('id');
        // 订单状态  -1 无效 0 未确认 1 确认 2 结算
        $status = $request->get('status');
        $order_exists = $this->em->getRepository('JiliApiBundle:DuomaiOrder')->findOneByOcd( $ocd) ;
        $status_int = (int) $status;

        if( $order_exists ) {
            if( ($order_exists->isBalanced() || $order_exists->isInvalid() 
                || ($status_int === $configs['status']['UNCERTAIN'] && ( $order_exists->isPending() ||  $order_exists->isConfirmed()) ) 
                || ($status_int === $configs['status']['CONFIRMED'] && $order_exists->isConfirmed()) ) )
            {
                // 0 表示推送成功 但订单已存在。
                $result['code']= $configs['response']['SUCCESS_DUPLICATED'];
                return $result;
            }


            $result['data']['exists_order_id' ] =  $order_exists->getId();
            $result['data']['exists_order_status' ] =  $order_exists->getStatus();
        }

        $result['value']= true;
        $result['code']= $configs['response']['SUCCESS'];

        return $result;
    }

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
        return $this;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

   public function setValidator(ValidatorInterface $validator) {
        $this->validator = $validator;
        return $this;
    }
}
