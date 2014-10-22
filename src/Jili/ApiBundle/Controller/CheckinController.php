<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\CheckinAdverList;
use Jili\ApiBundle\Entity\CheckinUserList;
use Jili\ApiBundle\Entity\CheckinClickList;
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

use Jili\FrontendBundle\Entity\MarketActivityClickNumber;

class CheckinController extends Controller
{
    /**
	 * @Route("/clickCount",name="_checkin_clickCount", options={"expose"=true})
	 */
    public function clickCountAction()
    {
        $culTimes = $this->container->getParameter('init');
        $date = date('Y-m-d');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $culTimes = $em->getRepository('JiliApiBundle:CheckinUserList')->countUserList($uid,$date);
        return new Response($culTimes);

    }

    /**
     * 防止重复签到,判断是否点击checkin_user_list记录返回
	 * @Route("/issetClick",name="_checkin_issetClick",  options={"expose"=true})
	 */
    public function issetClickAction() 
    {
        $code = '';
        $date = date("Y-m-d");
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $clickAdid = $request->query->get('cid');
        $em = $this->getDoctrine()->getManager();
        $issetClickShop = $em->getRepository('JiliApiBundle:CheckinUserList')->issetClickShop($uid,$date,$clickAdid);
        if(empty($issetClickShop)){
            $code = $this->container->getParameter('init_one');
        }
        return new Response($code);

    }

    /**
     * 增加己签到商家计数, 确认并发放签到积分
	 * @Route("/clickInsert",name="_checkin_clickInsert", options={"expose"=true})
	 */
    public function clickInsertAction()
    {
        $point = $this->container->getParameter('init');
        $code = $this->container->getParameter('init');
        $clickTimes = $this->container->getParameter('init_one');
        $date = date('Y-m-d');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $cid = $request->query->get('cid');
        $aid = $request->query->get('aid');
        $click = $em->getRepository('JiliApiBundle:CheckinClickList')->issetUserInfo($uid,$date);
        $cflag = $this->issetClickShop($uid,$date,$cid);
        if($cflag){
            $this->insertUserList($uid,$date,$cid);
        }
        $iflag = $this->issetUserClick($uid,$date);
        if($iflag){
            $this->insertClickList($uid,$date);
        }else{
            $culTimes = $em->getRepository('JiliApiBundle:CheckinUserList')->countUserList($uid,$date);
            $this->updateClickList($click[0]['id'],$culTimes);
        }
        if($click){
            $clickStatus = $em->getRepository('JiliApiBundle:CheckinClickList')->find($click[0]['id']);
            if($clickStatus->getOpenShopTimes() == 3){
                //获取签到积分
                $checkInLister = $this->get('check_in.listener');
                $nowPoint = $checkInLister->getCheckinPoint($this->get('request'));
                if($this->issetPoints($uid))
                    $this->updatePoint($uid,$nowPoint);
                $code = $this->container->getParameter('init_one');
                $point = $nowPoint;

                // remove from session cache.
                $taskList = $this->get('session.task_list');
                $keys = array('checkin_visit','checkin_point');
                $taskList->remove($keys);
            }
        }
        $url = $em->getRepository('JiliApiBundle:Advertiserment')->getRedirect($uid,$aid);
        return new Response(json_encode(array('code'=>$code,'url'=>$url,'point'=>$point)));
    }

    /**
     * 返回商家的URL. type =1 , 直接查Advertiserment表; type=2查商家活动表market_activity。 
	 * @Route("/location",name="_checkin_location", options={"expose"=true})
	 */
    public function locationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        if(!$uid){
            $uid = '';
        }
        $markId = $request->query->get('markid');
        $aid = $request->query->get('aid');
        $type = $request->query->get('type');
        switch($type) {
        case '1':
            $firstUrl = $em->getRepository('JiliApiBundle:Advertiserment')->getRedirect($uid,$aid);
            $lastUrl = '';
            break;
        case '2':
            $busiAct = $em->getRepository('JiliApiBundle:MarketActivity')->existMarket($markId);
            if(empty($busiAct)){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            $firstUrl = $em->getRepository('JiliApiBundle:Advertiserment')->getRedirect($uid,$busiAct[0]['aid']);
            $lastUrl = $busiAct[0]['activityUrl'];

            //用户点击保存 用户关注数
            $amcn = $em->getRepository('JiliFrontendBundle:MarketActivityClickNumber')->findByMarketActivityId($markId);
            if($amcn){
                $amcn[0]->setClickNumber($amcn[0]->getClickNumber() + 1);
            }else{
                $amcn[0] = new MarketActivityClickNumber();
                $amcn[0]->setMarketActivityId($markId);
                $amcn[0]->setClickNumber(1);
            }
            $em->persist($amcn[0]);
            $em->flush();

            break;
        default:
            # code...
            break;
        }

        return $this->render('JiliApiBundle:Checkin:info.html.twig',
                array('firstUrl'=>$firstUrl,'lastUrl'=>$lastUrl,'type'=>$type,'email'=>'','code'=>''));
    }

    /**
     *  返回商城的URL. aid 为advertiserment表的id.
	 * @Route("/checkinInfo",name="_checkin_checkinInfo")
	 */
    public function checkinInfoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $id = $request->query->get('aid');
        $yixun = $em->getRepository('JiliApiBundle:Advertiserment')->getRedirect($uid,$id);
        $url = 'http://www.91jili.com/shopping/list/'.$uid;
        return $this->render('JiliApiBundle:Checkin:info.html.twig',
                array('yixun'=>$yixun,'url'=>$url));
    }


    /**
     * 记录用户点击过的商家。
     */
    public function insertUserList($uid,$date,$clickAdid)
    {
        $em = $this->getDoctrine()->getManager();
        $ciu = new CheckinUserList();
        $ciu->setUserId($uid);
        $ciu->setClickDate($date);
        $ciu->setOpenShopId($clickAdid);
        $em->persist($ciu);
        $em->flush();
    }

    /**
     * 判断是否有checkin_user_list记录
     */
    public function issetClickShop($uid,$date,$clickAdid)
    {
        $em = $this->getDoctrine()->getManager();
        $issetClickShop = $em->getRepository('JiliApiBundle:CheckinUserList')->issetClickShop($uid,$date,$clickAdid);
        if(empty($issetClickShop)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 记录用户点击数
     */
    public function insertClickList($uid,$date)
    {
        $em = $this->getDoctrine()->getManager();
        $ccl = new CheckinClickList();
        $ccl->setUserId($uid);
        $ccl->setClickDate($date);
        $ccl->setOpenShopTimes($this->container->getParameter('init_one'));
        $em->persist($ccl);
        $em->flush();
    }

    //判断是否有checkin_click_list记录
    public function issetUserClick($uid,$date)
    {
        $em = $this->getDoctrine()->getManager();
        $issetClick = $em->getRepository('JiliApiBundle:CheckinClickList')->issetUserInfo($uid,$date);
        if(empty($issetClick)){
            return true;
        }else{
            return false;
        }
    }

    public function updateClickList($ucId,$times)
    {
        $em = $this->getDoctrine()->getManager();
        $ccl = $em->getRepository('JiliApiBundle:CheckinClickList')->find($ucId);
        $ccl->setOpenShopTimes($times);
        if($times > $this->container->getParameter('init_two'))
            $ccl->setStatus($this->container->getParameter('init_one'));
        $em->persist($ccl);
        $em->flush();

    }

    public function updatePoint($userId,$point)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        $oldPoint = $user->getPoints();
        $user->setPoints(intval($oldPoint+$point));
        $em->persist($user);
        $em->flush();
        $this->getPoint($userId,$point,16);
        $parms = array(
            'orderId' => $this->container->getParameter('init'),
            'userid' => $userId,
            'task_type' => $this->container->getParameter('init_four'),
            'categoryId' => 16,
            'taskName' => '每天签到获取米粒',
            'point' => $point,
            'date' => date('Y-m-d H:i:s'),
            'status' => $this->container->getParameter('init_one')
          );
        $this->getTaskHistory($parms);
    }


    private function getPoint($userid,$point,$type)
    {
        if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
        }else{
            $uid = $userid;
        }
        switch($uid){
            case 0:
                  $po = new PointHistory00();
                  break;
            case 1:
                  $po = new PointHistory01();
                  break;
            case 2:
                  $po = new PointHistory02();
                  break;
            case 3:
                  $po = new PointHistory03();
                  break;
            case 4:
                  $po = new PointHistory04();
                  break;
            case 5:
                  $po = new PointHistory05();
                  break;
            case 6:
                  $po = new PointHistory06();
                  break;
            case 7:
                  $po = new PointHistory07();
                  break;
            case 8:
                  $po = new PointHistory08();
                  break;
            case 9:
                  $po = new PointHistory09();
                  break;
        }
        $em = $this->getDoctrine()->getManager();
        $po->setUserId($userid);
        $po->setPointChangeNum($point);
        $po->setReason($type);
        $em->persist($po);
        $em->flush();
    }

    public function issetPoints($userid)
    {
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $task = $em->getRepository('JiliApiBundle:PointHistory00');
                  break;
            case 1:
                  $task = $em->getRepository('JiliApiBundle:PointHistory01');
                  break;
            case 2:
                  $task = $em->getRepository('JiliApiBundle:PointHistory02');
                  break;
            case 3:
                  $task = $em->getRepository('JiliApiBundle:PointHistory03');
                  break;
            case 4:
                  $task = $em->getRepository('JiliApiBundle:PointHistory04');
                  break;
            case 5:
                  $task = $em->getRepository('JiliApiBundle:PointHistory05');
                  break;
            case 6:
                  $task = $em->getRepository('JiliApiBundle:PointHistory06');
                  break;
            case 7:
                  $task = $em->getRepository('JiliApiBundle:PointHistory07');
                  break;
            case 8:
                  $task = $em->getRepository('JiliApiBundle:PointHistory08');
                  break;
            case 9:
                  $task = $em->getRepository('JiliApiBundle:PointHistory09');
                  break;
      }
      $task_order = $task->issetInsert($userid);
      if(empty($task_order)){
              return true;
      }else{
              return false;
      }

    }

    public function getTaskHistory($parms=array())
    {
    extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $po = new TaskHistory00();
                  break;
            case 1:
                  $po = new TaskHistory01();
                  break;
            case 2:
                  $po = new TaskHistory02();
                  break;
            case 3:
                  $po = new TaskHistory03();
                  break;
            case 4:
                  $po = new TaskHistory04();
                  break;
            case 5:
                  $po = new TaskHistory05();
                  break;
            case 6:
                  $po = new TaskHistory06();
                  break;
            case 7:
                  $po = new TaskHistory07();
                  break;
            case 8:
                  $po = new TaskHistory08();
                  break;
            case 9:
                  $po = new TaskHistory09();
                  break;
      }
      $em = $this->getDoctrine()->getManager();
      $po->setOrderId($orderId);
      $po->setUserId($userid);
      $po->setTaskType($task_type);
      $po->setCategoryType($categoryId);
      $po->setTaskName($taskName);
      $po->setPoint($point);
      $po->setDate(date_create($date));
      $po->setStatus($status);
      $em->persist($po);
      $em->flush();
    }

    /**
     * @Route("/userCheckin", name="_checkin_userCheckIn",  options={"expose"=true})
     * @Method("GET")
     */
    public function userCheckinAction() 
    {
        $session = $this->get('session');
        $request = $this->get('request');
        $return = array();

        //check login
        if (!$session->has('uid')) {
            $return['statusCode'] = 404;
            $return['userCheckin'] = NULL;
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        // ajax request only
        //check mothod
        if (!$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = '请求方法不对';
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }
       
        // 是否已经签到
        $taskList = $this->get('session.task_list');
        if( $this->container->getParameter('init_one') === $taskList->get('checkin_visit') ) {
            $return['userCheckin'] = $this->container->getParameter('init_one');
        } else {
            $return['userCheckin'] = false;
        }
        $return['statusCode'] = 200;
        $response = new JsonResponse();
        $response->setData($return);
        return $response;
    }
}
