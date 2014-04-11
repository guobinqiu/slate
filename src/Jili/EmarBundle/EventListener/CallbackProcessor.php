<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\EmarBundle\Entity\EmarOrder;

use Jili\ApiBundle\EventListener\TaskHistory,
    Jili\ApiBundle\EventListener\PointHistory,
    Jili\ApiBundle\EventListener\RebateActivity;

use Jili\ApiBundle\Utility\String,
    Jili\ApiBundle\Component\OrderBase;

/**
 * 
 **/
class CallbackProcessor
{
    private $em;
    private $logger;

    private $container; 

    private $taskLogger;
    private $pointLogger;
    private $rebatePointCaculator;
    private $config;



    public function __construct(LoggerInterface $logger, EntityManager $em )
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * 
     * @param: $data_of_validation : array('advertiserment'=> ) 
     *
     **/
    public function process( Request $request, array $data_of_validation = array()) {
        $logger = $this->logger;
        $em = $this->em;
        #$logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ));

        $config_of_return_codes = $this->getConfig('callback_return_code'); 
        $config_of_order_status = $this->getConfig('order_status'); 
        $task_type = $this->getConfig('task_type') ;
        $category_id =$this->getConfig('category_type')  ;
        $request_status = $request->query->get('status'); 
        $feed_back  = $request->query->get('feed_back');
        $action_id = $request->query->get('action_id');
        $uid = (int) $feed_back;
        $ocd = $request->query->get('unique_id');
        $comm = $request->query->get('commision');

        if( isset( $data_of_validation['advertiserment'] )) {
            $advertiserment = $data_of_validation['advertiserment'] ;
        } else {
          #  $logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ));
            $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->findOneEmarAdvertisermentByActionId( array(
                'intensive_type'=> $category_id,
                'action_id'=> $action_id
            ) );

        }


        if( isset($advertiserment)) {
            $adid = $advertiserment->getid();
            $reward_percent = $advertiserment->getrewardrate();
            $ad_type='local';
            $task_title = $advertiserment->getTitle();
        } else {
            $adid = $action_id;

            $task_title= $request->query->get('action_name');
            $reward_percent = $this->getConfig( 'cps_deafult_rebate');
            $ad_type='emar';
        }

        $happenTime = date_create();

        // insert/update task_historyXX
        $comm = $request->query->get('commision');


        $cps_reward = intval($comm * $reward_percent);


        if( $request_status === $config_of_order_status['hangup'] ) {

            $is_new = false;
            // order from validation.
            if( isset( $data_of_validation['order'] )) {
                $order = $data_of_validation['order'] ;
            } else {
                $order_params = array('user_id'=>$uid,
                    'ad_id'=>$adid,
                    'ad_type'=>$ad_type,
                    'status'=> $this->getParameter('init_one') ,
                    'delete_flag'=> $this->getParameter('init') 
                );
                $order = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneCpsOrderInit($order_params); 
                // is order init by clicked?
                // is order init by callback?
                if( empty($order)) {

                    $order_params['ocd'] = $ocd;
                    $order_params ['status'] = $this->getParameter('init_two');

                    $order = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneCpsOrderJoined( $order_params ); 

                    if( empty($order)) {
#                         $logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ).' new ' );
                        $order = new EmarOrder();
                        $order->setCreatedAt($happenTime);
                        $order->setDeleteFlag($this->getParameter('init'));
                        $order->setUserId($uid);
                        $order->setAdId($adid);
                        $order->setAdType($ad_type);

                        $is_new = true;
                    } else {
                        // init by callback  
#                         $logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ).' init by callback' );
                    } 
                } else {
                    // init by click 
#                     $logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ).' init by click' );
                }
            }

            // update order 
            $order->setHappenedAt($happenTime);
            $order->setReturnedAt($happenTime);

            if( is_null( $order->getOcd() ) ) {
                $order->setOcd($ocd);
            }

            $order->setComm($comm);

            if(!  OrderBase::isCompleted($order) ) {
                $order->setStatus($this->getParameter('init_two'));
            }

            $em->persist($order);
            $em->flush();

            $task_logger_params = array(
                'orderId' => $order->getId(),
                'userid' => $uid,
                'taskType' => $task_type,
                'categoryType' => $category_id,
                'task_name' => $task_title,
                'reward_percent' => $reward_percent,
                'point' => $cps_reward, 
                'date' => $happenTime,
                'status' => $order->getStatus()
            );
            #$logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ).var_export( $task_logger_params, true)  );

            if ( $is_new) {
                $this->taskLogger->init($task_logger_params);
            } else {
                $this->taskLogger->update($task_logger_params);
            }

        } elseif ($request_status === $config_of_order_status['valid'] || $request_status === $config_of_order_status['invalid'] ) {
            $is_order_valid = ($request_status === $config_of_order_status['valid']) ?  true: false;

            if( $is_order_valid) {
                $status = OrderBase::getSuccessStatus();
            } else {
                $status = OrderBase::getFailedStatus();
            }

            $order = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneBy( array('userId'=>$uid, 'adId'=> $adid, 'adType'=>$ad_type, 'ocd'=>$ocd ));
            $order->setHappenedAt($happenTime);
            $order->setConfirmedAt($happenTime);
            $order->setStatus($status);
            $order->setComm($comm);
            $order->setReturnedAt(date_create(date('Y-m-d H:i:s')));
            $em->flush();

            $params = array(
                'userid' => $uid,
                'orderId' => $order->getId(),
                'taskType' => $task_type,
                'reward_percent' => $reward_percent,
                'point' => $cps_reward,
                'date' => $happenTime,
                'status' => $status
            );

            $taskHistory = $this->taskLogger->update($params); 

            if( $is_order_valid) {

#                 $logger->debug('{jarod}'.implode(',', array( __FILE__,__LINE__,'') ).var_export( $taskHistory, true)  );

                $point = $taskHistory->getPoint();

                $user = $em->getRepository('JiliApiBundle:User')->find($uid);
                $user->setPoints(intval($user->getPoints()) +$point);
                $em->persist($user);
                $em->flush();

                $this->pointLogger->get( array( 'userid'=>$uid, 'point'=>$point, 'type'=>$category_id ));
            }
        }

        $return = array('value'=>true, 'code'=> $config_of_return_codes['finished'] );
        return $return;
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

    public function setTaskLogger(TaskHistory $taskLogger) {
        $this->taskLogger = $taskLogger; 
    }

    public function setPointLogger(PointHistory $pointLogger) {
        $this->pointLogger = $pointLogger; 
    }

    public function setRebatePointCaculator( RebateActivity $calc ) {
        $this->rebatePointCaculator = $calc;
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

}
