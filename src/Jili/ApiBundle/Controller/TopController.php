<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/top",requirements={"_scheme"="http"})
 */
class TopController extends Controller
{
    /**
     * @Route("/index")
     * @Method({ "GET", "POST"})
     * @Template
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');
       
        $cookies = $request->cookies;
        $session = $request->getSession();

        if ($cookies->has('jili_uid') && $cookies->has('jili_nick')) {
            $this->get('request')->getSession()->set('uid', $cookies->get('jili_uid'));
            $this->get('request')->getSession()->set('nick', $cookies->get('jili_nick'));
        }

        //首页登录
        $code = '';
        $email = $request->get('email');
        $pwd = $request->get('pwd');
        $arr['email'] = $email;
        $code = $this->get('login.listener')->login($this->get('request'),$email,$pwd);

# $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')), var_export($code , true));
# $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')), var_export($session->get('referer'), true));

        if($code == "ok"){
            return $this->redirect($this->generateUrl('_homepage'));
        }

        //newbie page
        if( $this->get('login.listener')->isNewbie() )  {
            if( $session->get('is_newbie_passed', false) === false ) {
                $arr['is_newbie_passed'] = false;
                $session->set('is_newbie_passed', true) ;
            }
        }
        $arr['code'] = $code;
        return $arr;
    }

    /**
     * @Route("/event")
     * @Template
     */
    public function eventAction()
    {
        //最新动态 :从文件中读取
        $filename = $this->container->getParameter('file_path_recent_point');
        $recentPoint = $this->readFileContent($filename);
        $arr['recentPoint'] = $recentPoint;
        return $this->render('JiliApiBundle:Top:event.html.twig', $arr);
    }

    /**
     * @Route("/ranking")
     * @Template
     */
    public function rankingAction()
    {
        //排行榜 :从文件中读取
        $filename = $this->container->getParameter('file_path_ranking_month');
        $rankingMonth = $this->readFileContent($filename);
        $filename = $this->container->getParameter('file_path_ranking_year');
        $rankingYear = $this->readFileContent($filename);
        $arr['rankingMonth'] = $rankingMonth;
        $arr['rankingYear'] = $rankingYear;
        return $this->render('JiliApiBundle:Top:ranking.html.twig', $arr);
    }

    /**
     * @Route("/callboard")
     * @Template
     */
    public function callboardAction()
    {
        //最新公告，取6条
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboardLimit(6);
        $arr['callboard'] = $callboard;
        return $this->render('JiliApiBundle:Top:callboard.html.twig', $arr);
    }

    /**
     * @Route("/userInfo")
     * @Template
     */
    public function userInfoAction()
    {
        //个人中心
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;

        //确认中的米粒数
        $task =  $em->getRepository('JiliApiBundle:TaskHistory0'. ( $id % 10 ) );
        $arr['confirmPoints'] = $task->getConfirmPoints($id);

        return $this->render('JiliApiBundle:Top:userInfo.html.twig', $arr);
    }

    /**
     * @Route("/task")
     * @Template
     */
    public function taskAction()
    {
        //任务列表
        $arr = $this->getUndoTaskList();
        return $this->render('JiliApiBundle:Top:task.html.twig', $arr);
    }

    /**
     * @Route("/myTask")
     * @Template
     */
    public function myTaskAction()
    {
        $logger =  $this->get('logger');
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')));

        //任务列表
        $arr['myTask'] = $this->getUndoTaskList();

        //确认中的任务
        $arr['confirmTask'] = $this->getMyTaskList(1);

        //已完成的任务
        $arr['finishTask'] = $this->getMyTaskList(2);

        return $this->render('JiliApiBundle:Top:myTask.html.twig', $arr);
    }

    /**
     * @Route("/checkIn")
     * @Template
     */
    public function checkInAction()
    {
        //获取签到商家
        $arr['arrList'] = $this->checkinList();
        //获取签到积分
        $checkInLister = $this->get('check_in.listener');
        $arr['checkinPoint'] = $checkInLister->getCheckinPoint($this->get('request'));
        return $this->render('JiliApiBundle:Top:checkIn.html.twig', $arr);
    }

    /**
     * @Route("/advertiseBanner")
     * @Template
     */
    public function advertiseBannerAction()
    {
        //banner,右一
        $em = $this->getDoctrine()->getManager();
        $advertiseBanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoBanner();
        $arr['advertise_banner'] = $advertiseBanner;
        return $this->render('JiliApiBundle:Top:adBanner.html.twig', $arr);
    }

    /**
     * @Route("/topCheckIn")
     * @Template
     */
    public function topCheckInAction()
    {

        //return $this->render('JiliApiBundle:Top:myTask.html.twig', $arr);
    }

    private function getUndoTaskList() {
        //可以做的任务，签到+游戏+91问问+购物+cpa
        if( $this->get('session')->has('uid')) {
            $taskList = $this->get('task_list');
            $taskList->setRequest($this->get('request'));
            $arr = $taskList->compose();
        }

        //advertiserment check
        $arr['adCheck'] = $this->getAdCheckInfo();

        return $arr;
    }

    /**
     * @Route("/adCheck")
     * @Template
     */
    public function adCheckAction()
    {
        //advertiserment check
        $adCheck = $this->getAdCheckInfo();
        return new Response($adCheck);
    }

    /**
     * @Route("/market")
     * @Template
     */
    public function marketAction()
    {
        //中间最下面，商家活动
        $em = $this->getDoctrine()->getManager();
        $market = $em->getRepository('JiliApiBundle:MarketActivity')->getActivityList($this->container->getParameter('init_eight'));
        $arr['market'] = $market;
        return $this->render('JiliApiBundle:Top:market.html.twig', $arr);
    }

    private function getAdCheckInfo(){
    	//advertiserment check
        $adCheck = "";
        $filename = $this->container->getParameter('file_path_advertiserment_check');
        if (file_exists($filename)) {
            $file_handle = fopen($filename, "r");
            if ($file_handle) {
               if(filesize ($filename)){
                    $adCheck = fread($file_handle, filesize ($filename));
               }
            }
            fclose($file_handle);
        }
        return $adCheck;
    }

    private function getMyTaskList($type) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');

        $option = array('status' => $type ,'offset'=>'','limit'=>'');

        $adtaste = $this->selTaskHistory($id,$option);

        foreach ($adtaste as $key => $value) {
            if($value['orderStatus'] == 1 && $value['type'] ==1){
                unset($adtaste[$key]);
            }
        }

        return $adtaste;
    }

    private function selTaskHistory($userid, $option){
      $em = $this->getDoctrine()->getManager();

      $logger  = $this->get('logger');

      $task = $em->getRepository('JiliApiBundle:TaskHistory0'. ( $userid % 10) );
      $po = $task->getUseradtaste($userid, $option);

      foreach ($po as $key => $value) {
            if($value['type']==1 ) {
                $adUrl = $task->getUserAdwId($value['orderId']);
                if( is_array($adUrl) && count($adUrl) > 0) {
                    $po[$key]['adid'] = $adUrl[0]['adid'];
                } else {
                    $po[$key]['adid'] = '';
                }
            }else{
                $po[$key]['adid'] = '';
            }
        }
        return $po;
    }

    //签到列表
    private function checkinList(){
        $arrList = array();
        $date = date('Y-m-d H:i:s');
        $cal_count = "";
        $campaign_multiple = $this->container->getParameter('campaign_multiple');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $reward_multiple = $user->getRewardMultiple();
        $cal = $em->getRepository('JiliApiBundle:CheckinAdverList')->showCheckinList($uid);
        if (count($cal) > 6) {
            $cal_count = 6;
            $calNow = array_rand($cal, 6); //随机取数组中6个键值
        } else {
            $cal_count = count($cal);
            for ($i = 0; $i < count($cal); $i++) {
                $calNow[$i] = $i;
            }
        }
        for ($i = 0; $i < $cal_count; $i++) {
            $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
            $cal[$calNow[$i]]['reward_rate'] = $cal[$calNow[$i]]['incentive_rate'] * $cal[$calNow[$i]]['reward_rate'] * $cps_rate;
            $cal[$calNow[$i]]['reward_rate'] = round($cal[$calNow[$i]]['reward_rate'] / 10000, 2);
            $arrList[] = $cal[$calNow[$i]];
        }
        return $arrList;

    }

    private function readFileContent($filename) {

        $contents = null;
        if (!file_exists($filename)) {
            //die("指定文件不存在，操作中断!");
            return $contents;
        }

        //读文件内容
        $file_handle = fopen($filename, "r");
        if (!$file_handle) {
            //die("指定文件不能打开，操作中断!");
            return $contents;
        }

        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            if ($line) {
                $item = explode(",", trim($line));
                $contents[] = $item;
            }
        }

        fclose($file_handle);

        return $contents;
    }

}
