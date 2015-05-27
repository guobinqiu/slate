<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Repository\AdwOrderRepository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\ApiBundle\Entity\AdwApiReturn;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\GameLog;
use Jili\ApiBundle\Entity\PagOrder;
use Jili\ApiBundle\Entity\PointHistory00;
use Jili\ApiBundle\Entity\PointHistory01;
use Jili\ApiBundle\Entity\PointHistory02;
use Jili\ApiBundle\Entity\PointHistory03;
use Jili\ApiBundle\Entity\PointHistory04;
use Jili\ApiBundle\Entity\PointHistory05;
use Jili\ApiBundle\Entity\PointHistory06;
use Jili\ApiBundle\Entity\PointHistory07;
use Jili\ApiBundle\Entity\PointHistory08;
use Jili\ApiBundle\Entity\PointHistory09;
use Jili\ApiBundle\Entity\TaskHistory00;
use Jili\ApiBundle\Entity\TaskHistory01;
use Jili\ApiBundle\Entity\TaskHistory02;
use Jili\ApiBundle\Entity\TaskHistory03;
use Jili\ApiBundle\Entity\TaskHistory04;
use Jili\ApiBundle\Entity\TaskHistory05;
use Jili\ApiBundle\Entity\TaskHistory06;
use Jili\ApiBundle\Entity\TaskHistory07;
use Jili\ApiBundle\Entity\TaskHistory08;
use Jili\ApiBundle\Entity\TaskHistory09;

class ApiController extends Controller
{
    private function getTime($date,$time)
    {
        $arrayDate[] = substr($date,0,4);
        $arrayDate[] = substr($date,4,2);
        $arrayDate[] = substr($date,6,2);
        $arrayTime[] = substr($time,0,2);
        $arrayTime[] = substr($time,2,2);
        $arrayTime[] = substr($time,4,2);
        $join[] = implode("/",$arrayDate);
        $join[] = implode(":",$arrayTime);
        return implode(" ",$join);
    }

    /**
	 * @Route("/getAdwInfo", name="_api_getAdwInfo")
	 */
    public function getAdwInfoAction()
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $adwapi = new AdwApiReturn();
        $adwapi->setContent($request->getRequestUri());
        $em->persist($adwapi);
        $em->flush();
        $code = array('code'=>'','msg'=>'');
    $issetOrderOcd = array();
        $uid = $request->query->get('userinfo');
        $adid = $request->query->get('extinfo');
        $date = $request->query->get('date');
        $time = $request->query->get('time');
        $happenTime = $this->getTime($date,$time);
        $comm = $request->query->get('comm');
        $type = $request->query->get('type');
        $ocd = $request->query->get('ocd');
        $totalPrice = $request->query->get('totalPrice');

        $order = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid);

        if($order){
            if($type==1){
                $issetStauts = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid,$this->container->getParameter('init_two'));
                if($issetStauts){
                    $code = 5;
                }else{

                    $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
                    $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->getAdwAdverList($advertiserment->getIncentiveType(),$adid);
                    //
                    $at = \Datetime::createFromFormat( 'YmdHis', $date.$time);
                    $point = $this->get('rebate_point.caculator')->calcPointByCategory($advertiserment[0]['incentive'], $advertiserment[0]['incentiveType'], $at);

                    $issetOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($order[0]['id']);
                    $issetOrder->setComm($comm);
                    $issetOrder->setIncentive($point);
                    $issetOrder->setHappenTime(date_create($happenTime));
                    $issetOrder->setOrderStatus($this->container->getParameter('init_two'));
                    $issetOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
                    $em->flush();

                $parms = array(
                      'userid' => $uid,
                      'orderId' => $issetOrder->getId(),
                      'taskType' => $this->container->getParameter('init_one'),
                      'reward_percent' => 0,
                      'point' => $point,
                      'ocd_date' => date('Y-m-d H:i:s'),
                      'date' => $happenTime,
                      'status' => $issetOrder->getOrderStatus()
                    );
                $this->updateTaskHistory($parms);
                    $code = 1;
                }
            }else{//cps
                // Use the rebate if  the advertisement.id found by adid. Or use the the default one. 
                $rewardRate = $this->container->getParameter('cps_default_rebate'); 
                if( $adid > 0 ) {
                    $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
                    if($advertiserment) {
                        $rewardRate = $advertiserment->getRewardRate();
                    }
                }
                
                $users = $em->getRepository('JiliApiBundle:User')->find($uid);
                $user_rate = $users->getRewardMultiple();
                $campaign_multiple = $this->container->getParameter('campaign_multiple');

                // Send more points to user  
                $rate =  $user_rate > $campaign_multiple ? $user_rate : $campaign_multiple;
                $reward_percent = $rewardRate*$rate;
                $cps_reward = intval($comm*$reward_percent);

                $issetCpsInfo = $em->getRepository('JiliApiBundle:AdwOrder')->getCpsInfo($uid,$adid);
                if($issetCpsInfo[0]['ocd']){ // 
                    foreach ($issetCpsInfo as $key => $value) {
                        $issetOrderOcd[] = $value['ocd'];
                    }
                    if(in_array($ocd, $issetOrderOcd)){
                        $code = 3;
                    }else{
                        $cpsOrder = new AdwOrder();
                        $cpsOrder->setUserId($uid);
                        $cpsOrder->setAdId($adid);
                        $cpsOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
                        $cpsOrder->setHappenTime(date_create($happenTime));
                        $cpsOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
                        $cpsOrder->setIncentiveType($type);
                        $cpsOrder->setIncentive($cps_reward);
                        $cpsOrder->setOcd($ocd);
                        $cpsOrder->setComm($comm);
                        $cpsOrder->setOrderPrice($totalPrice);
                        $cpsOrder->setOrderStatus($this->container->getParameter('init_two'));
                        $cpsOrder->setDeleteFlag($this->container->getParameter('init'));
                        $em->persist($cpsOrder);
                        $em->flush();
                        $parms = array(
                            'orderId' => $cpsOrder->getId(),
                            'userid' => $uid,
                            'task_type' => $this->container->getParameter('init_one'),
                            'categoryId' => $this->container->getParameter('init_two'),
                            'taskName' => $issetCpsInfo[0]['title'],
                            'reward_percent' => $reward_percent,
                            'point' => $cps_reward,
                            'ocd_date' => date('Y-m-d H:i:s'),
                            'date' => $happenTime,
                            'status' => $cpsOrder->getOrderStatus()
                        );
                        $this->getTaskHistory($parms);
                        $code = 1 ;
                    }
                }else{ // no related title for the CPA order 
                    $cpsOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($issetCpsInfo[0]['id']);
                    $cpsOrder->setComm($comm);
                    $cpsOrder->setOcd($ocd);
                    $cpsOrder->setOrderPrice($totalPrice);
                    $cpsOrder->setIncentive(intval($cps_reward));
                    $cpsOrder->setHappenTime(date_create($happenTime));
                    $cpsOrder->setOrderStatus($this->container->getParameter('init_two'));
                    $cpsOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
                    $em->flush();

                    $parms = array(
                        'userid' => $uid,
                        'orderId' => $order[0]['id'],
                        'taskType' => $this->container->getParameter('init_one'),
                        'reward_percent' => $reward_percent,
                        'point' => $cps_reward,
                        'ocd_date' => date('Y-m-d H:i:s'),
                        'date' => $happenTime,
                        'status' => $this->container->getParameter('init_two')
                    );
                    $this->updateTaskHistory($parms);
                    $code = 1;
                }
            }
        }else{
            $code = 2;
        }
        return new Response($code);
    }

    /**
     * @Route("/getGamePoint", name="_api_getGamePoint")
     */
    public function getGamePointAction()
    {
        if($_SERVER['REMOTE_ADDR']=='101.227.252.89' || $_SERVER['REMOTE_ADDR']=='112.65.174.206'){
            $request = $this->get('request');
            $point_uid = $request->request->get('point_uid');
            $point = $request->request->get('game_point');
            $date = $request->request->get('game_date');
            $time = $request->request->get('game_time');
            $score = $request->request->get('game_score');
            if(!$score) $score = 0;
            $type = $request->request->get('game_type');
            if(!$type) $type = 0;
            $massPoint = $request->request->get('mass_point');
            if(!$massPoint) $massPoint = 0;
            $goalPoint = $request->request->get('goal_point');
            if(!$goalPoint) $goalPoint = 0;
            $rankingPoint = $request->request->get('ranking_point');
            if(!$rankingPoint) $rankingPoint = 0;
            $attendancePoint = $request->request->get('attendance_point');
            if(!$attendancePoint) $attendancePoint = 0;
            $total = $massPoint + $goalPoint + $rankingPoint + $attendancePoint;
            $em = $this->getDoctrine()->getManager();
            if($point_uid && $point && $date && $time){
                $isset_user = $em->getRepository('JiliApiBundle:User')->find($point_uid);
                if($isset_user){
                      if($point == $total){
                            $rs = $em->getRepository('JiliApiBundle:GameLog')->getGameInfo($point_uid,$date);
                            if(empty($rs)){
                                  $gamelog = new GameLog();
                                  $gamelog->setPointUid($point_uid);
                                  $gamelog->setGamePoint($point);
                                  $gamelog->setGameDate($date);
                                  $gamelog->setGameTime($time);
                                  $gamelog->setGameScore($score);
                                  $gamelog->setGameType($type);
                                  $gamelog->setMassPoint($massPoint);
                                  $gamelog->setGoalPoint($goalPoint);
                                  $gamelog->setRankingPoint($rankingPoint);
                                  $gamelog->setAttendancePoint($attendancePoint);
                                  $em->persist($gamelog);
                                  $em->flush();
                                  if($massPoint && $massPoint>0){
                                      $parms = array(
                                        'orderId' => 0,
                                        'userid' => $point_uid,
                                        'task_type' => $this->container->getParameter('init_three'),
                                        'categoryId' => $this->container->getParameter('init_four'),
                                        'taskName' => $this->container->getParameter('game_type'),
                                        'reward_percent' => 0,
                                        'point' => $massPoint,
                                        'ocd_date' => date('Y-m-d H:i:s'),
                                        'date' => $this->getDateTime($time),
                                        'status' => $this->container->getParameter('init_one')
                                      );
                                      $this->getTaskHistory($parms);
                                      $this->getPoint($point_uid,$massPoint,$this->container->getParameter('init_four'));
                                  }
                                  if($goalPoint && $goalPoint>0){
                                     $parms = array(
                                        'orderId' => 0,
                                        'userid' => $point_uid,
                                        'task_type' => $this->container->getParameter('init_three'),
                                        'categoryId' => $this->container->getParameter('init_five'),
                                        'taskName' => $this->container->getParameter('game_type'),
                                        'reward_percent' => 0,
                                        'point' => $goalPoint,
                                        'ocd_date' => date('Y-m-d H:i:s'),
                                        'date' => $this->getDateTime($time),
                                        'status' => $this->container->getParameter('init_one')
                                      );
                                      $this->getTaskHistory($parms);
                                      $this->getPoint($point_uid,$goalPoint,$this->container->getParameter('init_five'));
                                  }
                                  if($rankingPoint && $rankingPoint>0){
                                      $parms = array(
                                        'orderId' => 0,
                                        'userid' => $point_uid,
                                        'task_type' => $this->container->getParameter('init_three'),
                                        'categoryId' => $this->container->getParameter('init_six'),
                                        'taskName' => $this->container->getParameter('game_type'),
                                        'reward_percent' => 0,
                                        'point' => $rankingPoint,
                                        'ocd_date' => date('Y-m-d H:i:s'),
                                        'date' => $this->getDateTime($time),
                                        'status' => $this->container->getParameter('init_one')
                                      );
                                      $this->getTaskHistory($parms);
                                      $this->getPoint($point_uid,$rankingPoint,$this->container->getParameter('init_six'));
                                  }
                                  if($attendancePoint && $attendancePoint>0){
                                     $parms = array(
                                        'orderId' => 0,
                                        'userid' => $point_uid,
                                        'task_type' => $this->container->getParameter('init_three'),
                                        'categoryId' => $this->container->getParameter('init_seven'),
                                        'taskName' => $this->container->getParameter('game_type'),
                                        'reward_percent' => 0,
                                        'point' => $attendancePoint,
                                        'ocd_date' => date('Y-m-d H:i:s'),
                                        'date' => $this->getDateTime($time),
                                        'status' => $this->container->getParameter('init_one')
                                      );
                                      $this->getTaskHistory($parms);
                                      $this->getPoint($point_uid,$attendancePoint,$this->container->getParameter('init_seven'));
                                  }
                                  $user = $em->getRepository('JiliApiBundle:User')->find($point_uid);
                                  $user->setPoints(intval($user->getPoints()+$point));
                                  $em->persist($user);
                                  $em->flush();
                                  $code = 'OK';
                            }else
                                  $code = 'RB';

                      }else
                            $code = 'PF';
                }else
                      $code = 'NM';
            }else
                $code = 'PF';
        }else{
            $code = '';
        }
        return new Response($code);

    }

    private function getDateTime($date2)
    {
        if(strlen($date2)==12){
            $arrayDate[] = '20'.substr($date2,0,2);
            $arrayDate[] = substr($date2,2,2);
            $arrayDate[] = substr($date2,4,2);
            $arrayTime[] = substr($date2,6,2);
            $arrayTime[] = substr($date2,8,2);
            $arrayTime[] = substr($date2,10,2);
        }else{
            $arrayDate[] =  substr($date2,0,4);
            $arrayDate[] = substr($date2,4,2);
            $arrayDate[] = substr($date2,6,2);
            $arrayTime[] = substr($date2,8,2);
            $arrayTime[] = substr($date2,10,2);
            $arrayTime[] = substr($date2,12,2);
        }
        $join[] = implode("-",$arrayDate);
        $join[] = implode(":",$arrayTime);
        return implode(" ",$join);
    }

     /**
     * @Route("/getGameAd", name="_api_getGameAd")
     */
    public function getGameAdAction()
    {
        if($_SERVER['REMOTE_ADDR']=='101.227.252.16' || $_SERVER['REMOTE_ADDR']=='112.65.174.206'){
            $request = $this->get('request');
            $session_id = $request->request->get('session_id');
            $point_uid = $request->request->get('point_uid');
            $point_pid = $request->request->get('point_pid');
            $date = $request->request->get('date');
            $date2 = $request->request->get('date2');
            $price = $request->request->get('price');
            $status = $request->request->get('status');
            $amounts = $request->request->get('amounts');
            $point = $request->request->get('point');
            $em = $this->getDoctrine()->getManager();
            if($session_id && $point_uid && $point_pid && $date && $date2 && $price && $status!='' && $amounts && $point){
                    $confireTime = $this->getDateTime($date);
                    $pag = $em->getRepository('JiliApiBundle:PagOrder')->findBySessionId($session_id);
                    if(empty($pag)){
                        $pagorder = new PagOrder();
                        $pagorder->setSessionId($session_id);
                        $pagorder->setPointUid($point_uid);
                        $pagorder->setPointPid($point_pid);
                        $pagorder->setDate($date);
                        $pagorder->setDate2($date2);
                        $pagorder->setPrice($price);
                        $pagorder->setStatus($status);
                        $pagorder->setAmounts($amounts);
                        $pagorder->setPoint($point);
                        $em->persist($pagorder);
                        $em->flush();
                        $parms = array(
                          'orderId' => $pagorder->getId(),
                          'userid' => $point_uid,
                          'task_type' => $this->container->getParameter('init_two'),
                          'categoryId' => $this->container->getParameter('init_three'),
                          'taskName' => $this->container->getParameter('game_type'),
                          'reward_percent' => 0,
                          'point' => $point,
                          'ocd_date' => date('Y-m-d H:i:s'),
                          'date' => $confireTime,
                          'status' => $status
                        );
                        $this->getTaskHistory($parms);
                        if($status==1){
                          $this->getPoint($point_uid,$point,$this->container->getParameter('init_three'));
                          $user = $em->getRepository('JiliApiBundle:User')->find($point_uid);
                          $user->setPoints(intval($user->getPoints()+$point));
                          $em->persist($user);
                          $em->flush();
                        }
                        $code = 'yes';

                    }else{
                        if($status == $pag[0]->getStatus()){
                          $code = '';
                        }else{
                          if($pag[0]->getStatus()==1){
                            $code = '';
                          }else{
                            $pag[0]->setDate($date);
                            $pag[0]->setPoint($point);
                            $pag[0]->setStatus($status);
                            $em->persist($pag[0]);
                            $em->flush();
                            $parms = array(
                              'userid' => $point_uid,
                              'orderId' => $pag[0]->getId(),
                              'taskType' => $this->container->getParameter('init_two'),
                              'reward_percent' => 0,
                              'point' => $point,
                              'ocd_date' => date('Y-m-d H:i:s'),
                              'date' => $confireTime,
                              'status' => $status
                            );
                            $this->updateTaskHistory($parms);
                            if($status==1){
                              $this->getPoint($point_uid,$point,$this->container->getParameter('init_three'));
                              $user = $em->getRepository('JiliApiBundle:User')->find($point_uid);
                              $user->setPoints(intval($user->getPoints()+$point));
                              $em->persist($user);
                              $em->flush();
                            }
                            $code = 'yes';
                          }
                        }
                    }

            }else{
              $code = '';
            }
        }else{
            $code = '';
        }
        return new Response($code);

    }


    private function getPoint($userid,$point,$type)
    {
        $point_history_class = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userid % 10) ;
        $po = new $point_history_class ;
        $em = $this->getDoctrine()->getManager();
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();
    }


    public function updateTaskHistory($parms=array())
    {
      extract($parms);
      $em = $this->getDoctrine()->getManager();
      $task =  $em->getRepository('JiliApiBundle:TaskHistory0'. ( $userid % 10 ) );
      $task_order = $task->getFindOrderId($orderId,$taskType);
      $po = $task->findById($task_order[0]['id']);

      $po[0]->setOcdCreatedDate(date_create($ocd_date));
      $po[0]->setDate(date_create($date));
      $po[0]->setRewardPercent($reward_percent);
      $po[0]->setPoint($point);
      $po[0]->setStatus($status);
      $em->persist($po[0]);
      $em->flush();
    }


    public function getTaskHistory($parms=array())
    {
        extract($parms);

        $task_history_class = 'Jili\ApiBundle\Entity\TaskHistory0'. ( $userid % 10);
        $po = new $task_history_class;

        $em = $this->getDoctrine()->getManager();
        $po->setOrderId($orderId);
        $po->setUserId($userid);
        $po->setTaskType($task_type);
        $po->setCategoryType($categoryId);
        $po->setTaskName($taskName);
        $po->setRewardPercent($reward_percent);
        $po->setPoint($point);
        $po->setOcdCreatedDate(date_create($ocd_date));
        $po->setDate(date_create($date));
        $po->setStatus($status);
        $em->persist($po);
        $em->flush();
    }


    /**
     * @Route("/check/email", name="_api_check_email",requirements={"_scheme"="https"})
     * @Method({"POST"});
     */
    public function isEmailDuplicated()
    {
        $result = '0';
        $email = $this->get('request')->get('email','');

        if( strlen($email) > 0) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
            if($user) {
                $result = '1';
            }
        }
        $resp = new Response($result);
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;
    }

    /**
     * @Route("/getavatar/{uid}", defaults={"uid"=0})
     * @Template();
     */
    public function getAvatarAction($uid)
    {
        $result = '';

        if( true || $_SERVER['REMOTE_ADDR']=='101.227.252.89' || $_SERVER['REMOTE_ADDR']=='112.65.174.206' || $_SERVER['REMOTE_ADDR']=='127.0.0.1'  ){

            $request = $this->get('request');
            $uid = $request->get('uid');

            if( is_numeric($uid) && (int) $uid > 0 ) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('JiliApiBundle:User')->findOneById($uid);
                if($user) {
                    $icon_path = $user->getIconPath();
                    if( strlen(trim($icon_path)) > 0) {
                        $result  =$request->getScheme(). '://'.$request->getHost().$request->getBaseUrl() .'/'.$icon_path;
                    }
                }
            }
        }
        $resp = new Response($result  );
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;
    }
}
