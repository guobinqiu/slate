<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

class CheckinController extends Controller
{
    /**
	 * @Route("/clickCount",name="_checkin_clickCount")
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
	 * @Route("/issetClick",name="_checkin_issetClick")
	 */
    public function issetClickAction() {//判断是否点击checkin_user_list记录返回
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
	 * @Route("/clickInsert",name="_checkin_clickInsert")
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
        $url = $this->advInfo($uid,$aid);
        return new Response(json_encode(array('code'=>$code,'url'=>$url,'point'=>$point)));
    }

    /**
	 * @Route("/location",name="_checkin_location")
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
        switch ($type) {
            case '1':
                $firstUrl = $this->advInfo($uid,$aid);
                $lastUrl = "";
                break;
            case '2':
                $busiAct = $em->getRepository('JiliApiBundle:MarketActivity')->existMarket($markId);
                if(empty($busiAct)){
                    return $this->redirect($this->generateUrl('_default_error'));
                }
                $firstUrl = $this->advInfo($uid,$busiAct[0]['aid']);
                $lastUrl = $busiAct[0]['activityUrl'];
                break;
            default:
                # code...
                break;
        }

        //用户点击保存
        $ma_click = new MarketActivityClickList();
        $ma_click->setUserId($uid);
        $ma_click->setMarketActivityId($markId);
        $ma_click->setCreateTime(date_create(date('Y-m-d H:i:s')));
        $em->persist($ma_click);
        $em->flush();
        return $this->render('JiliApiBundle:Checkin:info.html.twig',
                array('firstUrl'=>$firstUrl,'lastUrl'=>$lastUrl,'type'=>$type,'email'=>'','code'=>''));
    }

    /**
	 * @Route("/checkinInfo",name="_checkin_checkinInfo")
	 */
    public function checkinInfoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $id = $request->query->get('aid');
        $yixun = $this->advInfo($uid,$id);
        $url = "http://www.91jili.com/shopping/list/".$uid;
        return $this->render('JiliApiBundle:Checkin:info.html.twig',
                array('yixun'=>$yixun,'url'=>$url));
    }

    public function advInfo($uid,$aid)
    {
        $em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
        $adw_info = $advertiserment->getImageurl();
        $adw_info = explode("u=",$adw_info);
        $new_url = trim($adw_info[0])."u=".$uid.trim($adw_info[1]).$aid;
        return trim($new_url);
    }

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

    //判断是否有checkin_user_list记录
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


}
