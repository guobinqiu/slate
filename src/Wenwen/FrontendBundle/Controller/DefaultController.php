<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/help", name="_default_help", requirements={"_scheme"="http"})
     */
    public function helpAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/help/issue", name="_default_help_issue", requirements={"_scheme"="http"})
     */
    public function issueAction()
    {
        return $this->render('WenwenFrontendBundle:Help:issue.html.twig');
    }

    /**
     * @Route("/help/newGuide", name="_default_help_newGuide", requirements={"_scheme"="http"})
     */
    public function guideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/help/newGuide/detail", name="_default_help_newGuide_detail", requirements={"_scheme"="http"})
     */
    public function guideDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuideDetail.html.twig');
    }

    /**
     * @Route("/help/feedback", name="_default_help_feedback", requirements={"_scheme"="http"})
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/help/feedback/finished", name="_default_help_feedback_finished", requirements={"_scheme"="http"}, options={"expose"=true})
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }
    
    /**
     * @Route("/help/company", name="_default_help_company", requirements={"_scheme"="http"})
     */
    public function companyAction()
    {
        return $this->render('WenwenFrontendBundle:Help:company.html.twig');
    }

    /**
     * @Route("/help/ww", name="_default_help_ww", requirements={"_scheme"="http"})
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:Help:91ww.html.twig');
    }

    /**
     * @Route("/help/regulations", name="_default_help_regulations", requirements={"_scheme"="http"})
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:Help:regulations.html.twig');
    }

    /**
     * @Route("/help/map", name="_default_help_map", requirements={"_scheme"="http"})
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:Help:map.html.twig');
    }

    /**
     * @Route("/help/cooperation", name="_default_help_cooperation", requirements={"_scheme"="http"})
     */
    public function cooperationAction()
    {
        return $this->render('WenwenFrontendBundle:Help:cooperation.html.twig');
    }





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
}
