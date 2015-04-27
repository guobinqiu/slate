<?php
namespace Jili\ApiBundle\Services\Duomai;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Component\OrderBase;
use Jili\ApiBundle\Entity\DuomaiOrder;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;

/**
 *
 **/
class DuomaiRequestProcessor {
    private $configs;
    private $em;
    private $logger;
    private $send_mail;

    function __construct($configs) {
        $this->configs = $configs;
    }

    /**
     * 接收到如上信息，请通过接口反馈0或者1 给我们，默认情况下 ，接口没有收到数据。直接访问的情况下 请输出 -1 或者 不输出
     * 1 表示此次推送成功 并且订单已成功入库。
     * 0 表示推送成功 但订单已存在。
     * -1 表示推送失败。
     */
    public function process(ParameterBag $request, array $data=array()) 
    {
        $configs = $this->configs;
        $result = array (
            'value' => false,
            'code' => $configs['response']['FAILED'],
            'message'=>'',
            'data'=>[]
        );

        $configs = $this->configs;
        $em = $this->em;

        $status = $request->get('status');
        $userid = $request->get('euid');

        $logger = $this->logger;


        $status_int = (int) $status;
        if( $status_int === $configs['status']['UNCERTAIN'] ) {

            try{
                $em->getConnection()->beginTransaction();
                $order = $em->getRepository('JiliApiBundle:DuomaiOrder')->init( array(
                    'userId'=> $userid,
                    'adsId'=>$request->get('ads_id'),
                    'adsName'=>$request->get('ads_name'),
                    'siteId'=>$request->get('site_id'),
                    'linkId'=> $request->get('link_id'),
                    'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', $request->get('order_time')),
                    'ordersPrice'=> $request->get('orders_price'),
                    'orderSn'=>$request->get('order_sn'),
                    'ocd'=>$request->get('id'),
                    'commission'=>$request->get('siter_commission')
                ));


                $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))
                    ->init( array('userid'=>$userid,
                        'orderId'=> $order->getId(),
                        'categoryType'=> AdCategory::ID_DUOMAI ,
                        'taskType' => TaskHistory00::TASK_TYPE_DUOMAI,
                        'date'=> $order->getCreatedAt(),
                        'status'=> $order->getStatus(),
                        'task_name'=> $configs['name'],
                        'reward_percent'=>$configs['cps_deafult_rebate'] ,
                        'point'=> intval($order->getComm() * $configs['cps_deafult_rebate'] )
                    ));

                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $this->logger->crit('[duomai][callback]'. $e->getMessage());
                $em->getConnection()->rollback();
                return $result;
            }
            $result['value'] = true;
            $result['code']= $configs['response']['SUCCESS'];
            return $result;
        } 
        # other status
        $order_params =  array(
            'userId'=> $userid,
            'adsId'=>$request->get('ads_id'),
            'siteId'=>$request->get('site_id'),
            'linkId'=> $request->get('link_id'),
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', $request->get('order_time')),
            'ocd' => $request->get('id'),
            'ordersPrice'=> $request->get('orders_price'),
            'orderSn'=> $request->get('order_sn'),
            'commission' => $request->get('siter_commission')
        );

        if ( $status_int === $configs['status']['CONFIRMED']  ) {
            $order_params['status'] = OrderBase::getPendingStatus();
            $order_params['confirmedAt'] = new \DateTime();
        } else if($status_int === $configs['status']['BALANCED'] ) {
            $order_params['status'] = OrderBase::getSuccessStatus();
            $order_params['balancedAt']= new \DateTime();
        } else if ( $status_int === $configs['status']['INVALID'] ) {
            $order_params['status'] =OrderBase::getFailedStatus();
            $order_params['deactivatedAt'] = new \DateTime();
        } else {
            $this->logger->crit('[duomai][callback][processor] invalid request status');
            return  $result;
        }

        // by select emar_order ? 
        $task_params = array(
            'userId'=> $userid, 
            'orderId'=> $data['exists_order_id'],
            'categoryType'=> AdCategory::ID_DUOMAI ,
            'taskType' => TaskHistory00::TASK_TYPE_DUOMAI,
            'point'=> intval ($request->get('siter_commission') * $configs['cps_default_rebate']),
            'rewardPercent' => $configs['cps_default_rebate'],
            'status' => $order_params['status'],
            'statusPrevious'=> $data['exists_order_status']
        );

        try{
            $em->getConnection()->beginTransaction();
            // update duomai_order
            $order_update_result = $em->getRepository('JiliApiBundle:DuomaiOrder')->update($order_params);

            // update task_history
            $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))
                ->update($task_params);

            if( $status_int  === $configs['status']['BALANCED'] ) {
                // inssert point_history
                $em->getRepository('JiliApiBundle:PointHistory0'. ($userid % 10))
                    ->get( array( 'userid'=>$userid, 'point'=>$task_params['point'] ,'type'=> $task_params['categoryType'] ) );
                // update  user.points
                $em->getRepository('JiliApiBundle:User')->updatePointById(array('id'=> $userid, 'points'=> $task_params['point']));
            }
            $em->getConnection()->commit();
        } catch (\Exception $e) {
                $this->logger->crit('[duomai][callback]'. $e->getMessage());
                $em->getConnection()->rollback();
                return $result;
            }

        // 0 -> 1,2 

        // 0 -> -1 
        $result['value'] = true;
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

    public function setSendMail($send_mail)
    {
        $this->send_mail = $send_mail;
    }
}

