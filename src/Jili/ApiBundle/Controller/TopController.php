<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\RebateUtil;
use Jili\ApiBundle\Entity\CheckinAdverList;

/**
 * @Route("/top",requirements={"_scheme"="http"})
 */
class TopController extends Controller
{
    /**
     * @Route("/event/{tmpl_prefix}",requirements={"tmpl_prefix"="signup"}, defaults={"tmpl_prefix"=""})
     * @Template
     */
    public function eventAction($tmpl_prefix='')
    {
        //最新动态 :从文件中读取
        $filename = $this->container->getParameter('file_path_recent_point');
        $recentPoint = FileUtil::readCsvContent($filename);
        $arr['recentPoint'] = $recentPoint;

        if( ! empty($tmpl_prefix)) {
            $tmpl_prefix =  '_'. $tmpl_prefix;
        }
        return $this->render('JiliApiBundle:Top:event'.$tmpl_prefix.'.html.twig', $arr);
    }

    /**
     * @Route("/ranking")
     * @Template
     */
    public function rankingAction()
    {
        //排行榜 :从文件中读取
        $filename = $this->container->getParameter('file_path_ranking_month');
        $rankingMonth = FileUtil::readCsvContent($filename);
        $filename = $this->container->getParameter('file_path_ranking_year');
        $rankingYear = FileUtil::readCsvContent($filename);
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
        $cache_fn= $this->container->getParameter('cache_config.api.top_callboard.key');
        $cache_duration = $this->container->getParameter('cache_config.api.top_callboard.duration');
        $cache_proxy = $this->get('cache.file_handler');
        if($cache_proxy->isValid($cache_fn , $cache_duration) ) {
            $callboard= $cache_proxy->get($cache_fn);
        }  else {
            $cache_proxy->remove( $cache_fn);
            //最新公告，取9条
            $em = $this->getDoctrine()->getManager();
            $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboardLimit(9);
            $cache_proxy->set( $cache_fn, $callboard);
        }
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
        //确认中的米粒数
        $arr['confirmPoints'] = $this->get('session.points')->getConfirm();

        // 是否已经签到
        $taskList = $this->get('session.task_list');
        if( $this->container->getParameter('init_one') === $taskList->get('checkin_visit') ) {
            $arr['userCheckin'] = $this->container->getParameter('init_one');
        }
        //  签到的操作方式
        $userConfigs = $this->get('session.user_configs');
        $arr['checkinOpMethod'] = $userConfigs->getCheckinOpMethod();

        return $this->render('JiliApiBundle:Top:userInfo.html.twig', $arr);
    }

    /**
     * @Route("/checkIn")
     * @Template
     */
    public function checkInAction()
    {
        $taskList = $this->get('session.task_list');
        $arr = array();
        if( $this->container->getParameter('init_one') ===  $taskList->get('checkin_visit') ) {
            //获取签到积分
            $checkInLister = $this->get('check_in.listener');
            $arr['checkinPoint'] = $checkInLister->getCheckinPoint($this->get('request'));

            //获取签到商家
            $arr['arrList'] = $this->checkinList();

            return $this->render('JiliApiBundle:Top:checkIn.html.twig', $arr);
        } else {
            return new Response('<!-- already checked in -->');
        }
    }

    /**
     * @Route("/advertiseBanner")
     * @Template
     */
    public function advertiseBannerAction()
    {
        $cache_fn= $this->container->getParameter('cache_config.api.top_adbanner.key');
        $cache_duration = $this->container->getParameter('cache_config.api.top_adbanner.duration');
        $cache_proxy = $this->get('cache.file_handler');

        if($cache_proxy->isValid($cache_fn , $cache_duration) ) {
            $advertiseBanner= $cache_proxy->get($cache_fn);
        }  else {
            $cache_proxy->remove( $cache_fn);
            //banner,右一
            $em = $this->getDoctrine()->getManager();
            $advertiseBanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoBanner();
            $cache_proxy->set( $cache_fn, $advertiseBanner);
        }
        $arr['advertise_banner'] = $advertiseBanner;
        return $this->render('JiliApiBundle:Top:adBanner.html.twig', $arr);
    }

    /**
     * @Route("/market")
     * @Template
     */
    public function marketAction()
    {
        //中间最下面，商家活动
        $cache_fn= $this->container->getParameter('cache_config.api.top_market.key');
        $cache_duration = $this->container->getParameter('cache_config.api.top_market.duration');
        $cache_proxy = $this->get('cache.file_handler');

        if($cache_proxy->isValid($cache_fn , $cache_duration) ) {
            $market = $cache_proxy->get($cache_fn);
        }  else {
            $cache_proxy->remove( $cache_fn);
            $em = $this->getDoctrine()->getManager();
            $market = $em->getRepository('JiliApiBundle:MarketActivity')->getActivityList($this->container->getParameter('init_eight'));

            //取得活动返利倍数
            $campaign_multiple = $this->container->getParameter('campaign_multiple');
            //取得用户的返利倍数
            $uid = $this->get('request')->getSession()->get('uid');
            if($uid){
                $user = $em->getRepository('JiliApiBundle:User')->find($uid);
                $reward_multiple = $user->getRewardMultiple();
            } else {
                $reward_multiple = 0;
            }

            foreach($market as $key=>$ma){
                //用户关注数
                $resulut = $em->getRepository('JiliFrontendBundle:MarketActivityClickNumber')->getClickNumber($ma['id']);
                $market[$key]['click'] = $resulut['clickNumber'];

                //最高返利
                $reward_rate = RebateUtil :: calculateRebate($reward_multiple, $campaign_multiple, $ma);
                if($ma['incentiveType'] ==2){
                    $market[$key]['reward_rate'] = $reward_rate;
                }
            }
            $cache_proxy->set( $cache_fn, $market);
        }
        $arr['market'] = $market;
        return $this->render('JiliApiBundle:Top:market.html.twig', $arr);
    }

    //签到列表
    private function checkinList()
    {
        $arrList = array();
        $date = date('Y-m-d H:i:s');
        $cal_count = "";
        $campaign_multiple = $this->container->getParameter('campaign_multiple');
        $request = $this->get('request');
        $logger = $this->get('logger');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $reward_multiple = $user->getRewardMultiple();
        $is_auto_checkin =  $request->get('auto_checkin',0);
        $operation = (! $is_auto_checkin) ? CheckinAdverList::ANY_OP_METHOD : CheckinAdverList::AUTO_OP_METHOD; 
        $cal = $em->getRepository('JiliApiBundle:CheckinAdverList')->showCheckinList($uid, $operation);

        $count_for_checkin =  6;
        $cal_count = count($cal);

        if ($cal_count > $count_for_checkin) {
            $calNow = array_rand($cal, $count_for_checkin); //随机取数组中6个键值
            $cal_count = $count_for_checkin;
        } else {
            $calNow = range(0, $cal_count - 1);
        }

        for ($i = 0; $i < $cal_count; $i++) {
            $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
            $cal[$calNow[$i]]['reward_rate'] = $cal[$calNow[$i]]['incentive_rate'] * $cal[$calNow[$i]]['reward_rate'] * $cps_rate;
            $cal[$calNow[$i]]['reward_rate'] = round($cal[$calNow[$i]]['reward_rate'] / 10000, 2);
            $arrList[] = $cal[$calNow[$i]];
        }

        return $arrList;
    }
}
