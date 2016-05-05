<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
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
     * @Route("/maintain", name="maintain")
     * @Template
     */
    public function maintainAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:maintain.html.twig');
    }
}
