<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\ApiBundle\Utility\FileUtil;

/**
 * @Route("/top")
 */
class TopController extends Controller
{
    /**
     * @Route("/event")
     */
    public function eventAction()
    {
        $latestNews = $this->get('app.latest_news_service')->getLatestNews();
        return $this->render('WenwenFrontendBundle:Vote:_topEvent.html.twig', array('latestNews' => $latestNews));
    }

    /**
     * @Route("/callboard")
     * @Template
     */
    public function callboardAction()
    {
        // 公告的数据表也就是数百条级别的，不用做缓存机制了，以后要做也用redis之类的内存缓存
        // 目前一共就60条不到的记录
        //最新公告，取9条
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboardLimit(4);

        $arr['callboard'] = $callboard;

        return $this->render('WenwenFrontendBundle:Callboard:_listHome.html.twig', $arr);
    }

}
