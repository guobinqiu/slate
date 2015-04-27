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

#`        $logger->debug('jarod '. implode(':', array(__LINE__, __LINE__,  '$status: ') ). var_export($status, true));
 #       $logger->debug('jarod '. implode(':', array(__LINE__, __LINE__,  '$configs: ') ). var_export($configs, true));
        $logger->debug('jarod '. implode(':', array(__LINE__, __LINE__,  '$requests: ') ). var_export($request, true));

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
                    'ocd'=>$request->get('order_sn')
                ));

                $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))
                    ->init( array('userid'=>$userid,
                        'orderId'=> $order->getId(),
                        'categoryType'=> AdCategory::ID_DUOMAI ,
                        'taskType' => TaskHistory00::TASK_TYPE_DUOMAI,
                        'task_name'=> $configs['name'], #$odrer->getAdsName(),
                        'date'=> $order->getCreatedAt(),
                        'point'=>0,
                        'status'=> $order->getStatus()
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
        $status_order = '';
        $order_params =  array(
            'userId'=> $userid,
            'adsId'=>$request->get('ads_id'),
            'adsName'=>$request->get('ads_name'),
            'siteId'=>$request->get('site_id'),
            'linkId'=> $request->get('link_id'),
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', $request->get('order_time')),
            'ocd' => $request->get('order_sn'),
            'ordersPrice'=> $request->get('orders_price'),
            'commission' => $request->get('siter_commission'),
            'status' => $status
        );

        if ( $status_int === $configs['status']['CONFIRMED']  ) {
            $order_params['status'] = OrderBase::getPendingStatus();
            $order_params['confirmedAt'] = new \DateTime();
        } else if($statut_int === $configs['status']['BALANCED'] ) {
            $order_params['status'] = OrderBase::getSuccessStatus();
            $order_params['balancedAt']= new \DateTime();
        } else if ( $status_int === $configs['status']['INVALID'] ) {
            $order_params['status'] =OrderBase::getFailedStatus();
            $order_params['deactivatedAt'] = new \DateTime();
        } else {
            $this->logger->crit('[duomai][callback][processor] invalid request status');
            return  $result;
        }

        // update duomai_order
        $em->getRepository('JiliApiBundle:DuomaiOrder')->update($order_params);

        // by select emar_order ? 

        // update task_history
        #            $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))
        #                ->update( array('userid'=>$userid,
        #                    'orderId'=> $order->getId(),
        #                    'categoryType'=> Jili\ApiBundle\Entity\AdCategory::ID_DUOMAI ,
        #                    'taskType' => Jili\ApiBundle\Entity\TaskHistory00\TASK_TYPE_DUOMAI,
        #                    'task_name'=> $config['name'], #$odrer->getAdsName(),
        #                    'date'=> $order->getCreatedAt(),
        #                    'point'=>0,
        #                    'status'=> $order->getStatus()
        #                ));
        #
        // $em->getRepository('JiliApiBundle:DuomaiOrder')->update( array() );
        // $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))->update(  array() );

        if( $status_int  === $configs['status']['BALANCED'] ) {
            // inssert point_history
            // update  user.points
        }

        // 0 -> 1,2 

        // 0 -> -1 
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

