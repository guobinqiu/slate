<?php
namespace Jili\ApiBundle\Services\Duomai;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Component\OrderBase;
use Jili\ApiBundle\Entity\DuomaiOrder;

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
    public function process(ParameterBagInterface $request, array $data=array()) 
    {
        $configs = $this->configs;

        $category_type = $configs['category_type'];
        $task_name = $configs['name'];
        $task_type = $configs['task_type'];

        $em = $this->em;

        //transaction
        // $em->getConnection()->beginTransaction();
        $status = $request->get('status') ;

        $userid = $request->get('euid');

        if( $status === $configs['status']['UNCERTAIN'] ) {

            $order = $em->getRepository('JiliApiBundle:DuomaiOrder')->init( array(
                'userId'=> $userid,
                'adsId'=>$request->get('ads_id'),
                'siteId'=>$request->get('site_id'),
                'linkId'=> $request->get('link_id'),
                'orderTime'=> $request->get('order_time'),
                'ordersPrice'=> $request->get('orders_price'),
                'ocd'=>$request->get('ocd')
            ));

            $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))
                ->init( array('userid'=>$userid,
                    'orderId'=> $order->getId(),
                    'categoryType'=> Jili\ApiBundle\Entity\AdCategory::ID_DUOMAI ,
                    'taskType' => Jili\ApiBundle\Entity\TaskHistory00\TASK_TYPE_DUOMAI,
                    'task_name'=> $odrer->getAdsName(),
                    'date'=> $order->getCreatedAt(),
                    'point'=>0,
                    'status'=> $order->getStatus()
                ));

            // insert duomai_order, check before insert ? or unique restrict 
            // insert task_history, 

        } elseif ( $status === $configs['status']['CONFIRMED'] 
            || $status === $configs['status']['BALANCED'] 
            || $status === $configs['status']['INVALID'] )  {

            // update duomai_order
            // update task_history
            // $em->getRepository('JiliApiBundle:DuomaiOrder')->update( array() );
            // $em->getRepository('JiliApiBundle:TaskHistory0'. ($userid % 10))->update(  array() );

            if( $status === $configs['status']['BALANCED'] ) {
                // inssert point_history
                // update  user.points
            }
        } else {
            // unknow $status
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

