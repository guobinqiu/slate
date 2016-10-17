<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController
{
    /**
     * @Route("/jiliNotice", name="_jili_notice")
     * @Template
     */
    public function jiliNoticeAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:oldJili.html.twig');
    }

    /**
     * @Route("/maintain91ww", name="maintain91ww")
     * @Template
     */
    public function maintain91wwAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:maintain91ww.html.twig');
    }

    /**
     * @Route("/maintain91jili", name="maintain91jili")
     * @Template
     */
    public function maintain91jiliAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:maintain91jili.html.twig');
    }

    /**
     * @Route("/luckyDraw", name="_luckyDraw")
     * @Template
     */
    public function luckyDrawAction()
    {
        return $this->render('WenwenFrontendBundle:User:luckyDraw.html.twig');
    }

	/**
     * @Route("/eventCenter", name="_eventCenter")
     * @Template
     */
    public function eventCenterAction()
    {
        return $this->render('WenwenFrontendBundle:User:eventCenter.html.twig');
    }
}
