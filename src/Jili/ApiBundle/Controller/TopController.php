<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\ApiBundle\Utility\FileUtil;

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

}
