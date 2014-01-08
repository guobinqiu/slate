<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\OfferwowOrder,
    Jili\ApiBundle\Entity\LimitAdResult,
    Jili\ApiBundle\Entity\RateAdResult;

use Jili\ApiBundle\Component\OrderBase;
use Jili\ApiBundle\Util\String;

/**
 * 
 **/
class OfferwowRequestProcessor
{
    private $em;
    private $logger;
    private $parameterBag;
    private $container_; 

    private $task_logger;
    private $point_logger;

    public function __construct(LoggerInterface $logger, EntityManager $em/*, ParameterBagInterface $parameterBag*/ )
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /*
     * @abstract     immediate_status:
     *         HANGUP_SUSPEND: 0 # ：非即时返利活动,处于待审核状态；
     *         INSTANT_PASSED: 1 #：即时返利活动，需发放奖励给会员；
     *         HANGUP_PASSED: 2 #：非即时返利活动，审核通过，重新回传，发放奖励给会员；
     *         HANGUP_REFUSED: 3 #：非即时返利活动，审核不通过，重新回传，不发放奖励；
     **/
    public function process(  Request $request, array $config) {

        $immediate_status = $config['immediate_status'];
        $order_status= OrderBase::getStatusList();
        $category_type = $config['category_type'];
        $task_name = $config['name'];
        $task_type = $config['task_type'];

        $eventid = $request->query->get('eventid');
        $uid = $request->query->get('memberid');
        $point  = (int) $request->query->get('point', 0);


        $immediate_request = (int) $request->query->get('immediate');
        $happen_time = date_create();


        $em = $this->em;

        // init log.
        if ($immediate_status['HANGUP_SUSPEND'] ===  $immediate_request ) {

            // todo: init logs.... 
            $order = $em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid($eventid );
            $is_new = false;

            if( is_null( $order) && ! OrderBase::isCompleted($order)) {
                $is_new = true;
                // init offerorder & task history 
                $order = new OfferwowOrder();
                // update offerorder 
                $order->setUserid($uid); // order 
                $order->setEventid($eventid); // order 
                $order->setStatus($this->getParameter('init_two')); //clicked
                $order->setHappenedAt( $happen_time );
                $order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
                $order->setDeleteFlag(0);
            }

            $order->setReturnedAt($happen_time);
            $em->persist($order);
            $em->flush();

            $params = array(
                'userid' => $uid,
                'orderId' => $order->getId(),
                'taskType' => $task_type,
                'categoryType' => $category_type,
                'reward_percent' => 0,
                'task_name'=>$task_name,
                'point' => $point,
                'date' => $happen_time,
                'status' => $order->getStatus()
            );

            if($is_new) {
                $this->initTaskHistory($params);             
            } else { 
                //repeat request allowd on immeidate==0 & offerwow_order.status==2
                $this->updateTaskHistory($params);             
            }

        } elseif( $immediate_status['INSTANT_PASSED'] === $immediate_request ) {
            // init offerorder & task history 
            $order = new OfferwowOrder();
            // update offerorder 
            $order->setUserid($uid); // order 
            $order->setEventid($eventid); // order 
            $order->setHappenedAt( $happen_time );
            $order->setStatus($this->getParameter('init_three')); //clicked
            $order->setReturnedAt($happen_time);
            $order->setCreatedAt($happen_time);
            $order->setDeleteFlag(0);
            $em->persist($order);
            //TODO: rallback required.
            $em->flush();

            // update user point & point history 

            $params = array(
                'userid' => $uid,
                'orderId' => $order->getId(),
                'taskType' => $task_type,
                'categoryType' => $category_type,
                'reward_percent' => 0,
                'task_name'=>$task_name,
                'point' => $point,
                'date' => $happen_time,
                'status' => $order->getStatus()
            );
            $this->initTaskHistory($params);             
             
            $user = $em->getRepository('JiliApiBundle:User')->find($uid);
            $user->setPoints(intval($user->getPoints()) +$point);
            $em->persist($user);
            $em->flush();
            // updte point_history
            $this->getPointHistory($user->getId(), $point, $category_type );
            
        } elseif ($immediate_status['HANGUP_REFUSED'] === $immediate_request ) {

            $order = $em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid($eventid );

            $order->setConfirmedAt(date_create(date('Y-m-d H:i:s')));
            $order->setStatus($this->getParameter('init_four'));
            $em->persist($order);
            $em->flush();

            $params = array(
              'userid' => $uid,
              'orderId' => $order->getId(),
              'taskType' => $task_type,
              'categoryType' => $category_type,
              'point' => $point,
              'date' => date_create(),
              'status' => $this->getParameter('init_four')
            );
            $this->updateTaskHistory($params);  

        } elseif ( $immediate_status['HANGUP_PASSED'] ===   $immediate_request ) {

            $order = $em->getRepository('JiliApiBundle:OfferwowOrder')->findOneByEventid($eventid );
            $order->setConfirmedAt(date_create(date('Y-m-d H:i:s')));
            $order->setStatus($this->getParameter('init_three'));
            $em->persist($order);
            $em->flush();

            $params = array(
              'userid' => $uid,
              'orderId' => $order->getId(),
              'taskType' => $task_type,
              'categoryType' => $category_type,
              'point' => $point,
              'date' => date_create(),
              'status' => $this->getParameter('init_four')
            );

            $this->updateTaskHistory($params);  

            $user = $em->getRepository('JiliApiBundle:User')->find($uid);
            $user->setPoints(intval($user->getPoints()) +$point);
            $em->persist($user);
            $em->flush();

            // updte point_history
            $this->getPointHistory($user->getId(), $point, $category_type );

        } else {

        }
    }
   

    private function updateTaskHistory($params=array()){
        extract($params);
        $this->task_logger->update($params);
    }

    private function initTaskHistory($params=array()){
        extract($params);
        $this->task_logger->init($params);
    }

    private function TaskHistory($params=array()){
        extract($params);
        $this->task_logger->update($params);
    }

    public function selectTaskPercent($userid,$orderId){
       return $this->task_logger->selectPercent( array('user_id'=>$userid,'order_id'=>$orderId) );
    }

    private function getPointHistory($userid,$point,$type){
        $this->point_logger->get( compact( 'userid', 'point', 'type' ));
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

    public function setTaskLogger(TaskHistory $task_logger) {
        $this->task_logger = $task_logger; 
    }

    public function setPointLogger(PointHistory $point_logger) {
        $this->point_logger = $point_logger; 
    }
}


