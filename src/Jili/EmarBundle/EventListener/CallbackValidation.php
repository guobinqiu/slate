<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\String,
    Jili\ApiBundle\Component\OrderBase;

/**
 * 
 **/
class CallbackValidation
{
    private $em;
    private $logger;
    private $config;

    public function __construct(LoggerInterface $logger, EntityManager $em )
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * 
     */
    public function validate(Request $request )
    {
        $logger = $this->logger;
        $em = $this->em;
        $quries = $request->query->all();
        $config_of_return_codes= $this->getConfig('callback_return_code'); 
        $config_of_order_status =$this->getConfig('order_status'); 
        $task_type = $this->getConfig('task_type') ;
        $category_id =$this->getConfig('category_type')  ;

        $config_of_sid = $this->getConfig('sid') ;//: %emar_com.accountid% #: 458631 # sid uSer id
        $config_of_wid = $this->getConfig('wid.91jili_com'); //wid.91jili_com: %emar_com.91jili_com.websiteid% 708089 # wid Website id

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ));

        // data to return
        $data = array();

        //required fields
        $fields_required = array('unique_id', 'action_id', 'sid', 'wid', 'order_no', 'order_time', 'prod_count', 'prod_money', 'comm_type', 'commision', 'status', 'am', 'chkcode', 'fead_back');

        foreach($fields_required as $field) {
            if( ! isset( $quries) ||  empty( $quries[$field] ) ) {
                // false 1
                $this->logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ). $field);
                return array( 'value' =>false, 'code'=>$config_of_return_codes['exception']); 
            }
        }



        // signature validation.
        // MD5(action_id+order_no+prod_money+order_time+DataSecret)
        $action_id = $request->query->get( 'action_id');
        $order_no = $request->query->get( 'order_no');
        $prod_money = $request->query->get( 'prod_money');
        $order_time = $request->query->get('order_time');

        $DataSecret = $this->getConfig('DataSecret');

        $chkcode_expect  = md5( $action_id.$order_no.$prod_money.$order_time.$DataSecret );
        $chkcode_request = $request->query->get('chkcode');

        if( strcmp($chkcode_request, $chkcode_expect ) !== 0) {
            $str = $action_id.$order_no.$prod_money.$order_time.$DataSecret;
            $this->logger->crit('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ). ' invalid signature. expected: '.$chkcode_expect. '; request:'.$chkcode_request .PHP_EOL. $str );
            return array( 'value' =>false, 'code'=>$config_of_return_codes['exception']); 
        }

        //Deprecated: uid adid validation  or postpone to processing.
        // list( $uid, $adid) = String::explodeUidAdid($request->query->get('fead_back')  );

        //todo: sid wid validation
        if( $request->query->get('sid')  !== $config_of_sid ) {
            $this->logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ). ' invalid sid: ' .var_export( $request->query->get('sid'), true) );
            return array( 'value' =>false, 'code'=>$config_of_return_codes['exception']); 
        }

        #$wid = $request->query->get('wid');
        if($request->query->get('wid') !== $config_of_wid ) {
            $this->logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ). ' invalid wid: ' .var_export( $request->query->get('wid')  , true) );
            return array( 'value' =>false, 'code'=>$config_of_return_codes['exception']); 
        }
        
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->findOneEmarAdvertisermentByActionId( array(
            'intensive_type'=> $category_id,
            'action_id'=> $action_id
        ) );

        if( empty($advertiserment) ) {
            $logger->crit(implode(',', array(__CLASS__,__FILE__,__LINE__,'') ).' Unsupport action_id ' . $action_id . ' of category_id '. $category_id);
            return array( 'value' =>false, 'code'=>$config_of_return_codes['exception']); 
        } else {
            $data['advertiserment'] = $advertiserment;
        }

        // status validation
        $request_status = $request->query->get('status'); 
        $this->logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ). ' request status '.$request_status);

        // order status check
        // duplicated??
        // unique( adid, ocd)
        // unique( user,adid, ocd)

        $emarOrder = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneBy(array('adId'=> $advertiserment->getId()  ,'ocd'=> $request->query->get('unique_id'))); 
        #$this->logger->debug('{jarod}'. implode(',', array(__CLASS__,__FILE__,__LINE__,'') ).var_export( $emarOrder, true)   );

        if( empty($emarOrder) ) {

            #    $order_params = array('user_id'=>$uid,
            #        'ad_id'=>$adid,
            #        'status'=> $this->getParameter('init_one') ,
            #        'delete_flag'=> $this->getParameter('init') 
            #    );
            #    $emarOrder = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneCpsOrderInit($order_params); 
            // the 2nd callback triggerd directly ?? 
            if ($request_status === $config_of_order_status['valid'] || $request_status === $config_of_order_status['invalid'] ) {
                  return array( 'value' =>false, 'code'=>$config_of_return_codes['exception'], 'data'=>$data ); 
            }
        } else {
            $data['order'] = $emarOrder;
            $is_completed = OrderBase::isCompleted($emarOrder);
            if( $request_status === $config_of_order_status['hangup'] ) {
                // must not exits
                if( ! is_null( $emarOrder) ) {
                    return array( 'value' =>false, 'code'=>$config_of_return_codes['duplicated'], 'data'=>$data ); 
                    // if( ) {
                    //     // 1 成功，表示网站主成功接收订单，亿起发将不再重复发送。
                    // } else {
                    //     // 0 重复订单，表示网站主已经接收到这个订单，亿起发将不再发送。 ?? 
                    // }
                } else {
                    // 正常
                }
            } elseif ($request_status === $config_of_order_status['valid'] || $request_status === $config_of_order_status['invalid'] ) {
                // must exists &&  not completed  yet
                // the emar_order.findBy(user_id, adid, ocd) 
                // todo:  build an index on ocd/
                if(  ! is_null( $emarOrder) ) {
                    // fatal error ?!
                    // the 2nd callback triggered directly!
                //} else {
                    if( $is_completed ) {
                        // 0 重复订单，表示网站主已经接收到这个订单，亿起发将不再发送。 ?? 
                        return array( 'value' =>false, 'code'=>$config_of_return_codes['duplicated'], $data ); 
                    //} else {
                    // 正常
                    }
                }
            }
        }
        // 正常
        return array('value' => true, 'code'=>'', 'data'=> $data);
    }

    public function setConfig( array $config)
    {
        $this->config = $config;
    }

    private function getConfig($field ) {
        if ( isset( $this->config[$field] ) ) {
            return $this->config[$field];
        } else {
            throw  new ValidationException;
        }
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

    private function getParameter($key) {
        return $this->container_->getParameter($key);
    }
}

