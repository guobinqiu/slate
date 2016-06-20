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
use Jili\ApiBundle\Form\Type\CheckinConfigType;

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
        // return $this->render('JiliApiBundle:Top:event'.$tmpl_prefix.'.html.twig', $arr);
        return $this->render('WenwenFrontendBundle:Vote:_topEvent'.$tmpl_prefix.'.html.twig', $arr);
    }

    /**
     * @Route("/ranking")
     * @Template
     */
    public function rankingAction()
    {
        $this->container->get('logger')->debug(__METHOD__ . ' - START - ');
        //排行榜 :从文件中读取
        $filename = $this->container->getParameter('file_path_ranking_month');
        $rankingMonth = FileUtil::readCsvContent($filename);
        $filename = $this->container->getParameter('file_path_ranking_year');
        $rankingYear = FileUtil::readCsvContent($filename);
        $arr['rankingMonth'] = $rankingMonth;
        $arr['rankingYear'] = $rankingYear;
        $this->container->get('logger')->debug(__METHOD__ . ' - END - ');
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
        if($cache_proxy->isValid($cache_fn , $cache_duration)) {
            $callboard= $cache_proxy->get($cache_fn);
        }  else {
            $cache_proxy->remove( $cache_fn);
            //最新公告，取9条
            $em = $this->getDoctrine()->getManager();
            $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboardLimit(4);
            $cache_proxy->set( $cache_fn, $callboard);
        }
        $arr['callboard'] = $callboard;

        return $this->render('WenwenFrontendBundle:Callboard:_listHome.html.twig', $arr);
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

        return $this->render('WenwenFrontendBundle:Advertisement:_advShopActivity.html.twig', $arr);
    }

}
