<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HelpController extends Controller
{
    /**
     * @Route("/help", name="help_help", requirements={"_scheme"="http"})
     */
    public function helpAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/help/issue", name="help_issue", requirements={"_scheme"="http"})
     */
    public function issueAction()
    {
        return $this->render('WenwenFrontendBundle:Help:issue.html.twig');
    }

    /**
     * @Route("/help/newGuide", name="help_newGuide", requirements={"_scheme"="http"})
     */
    public function guideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/help/newGuide/detail", name="help_newGuide_detail", requirements={"_scheme"="http"})
     */
    public function guideDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuideDetail.html.twig');
    }

    /**
     * @Route("/help/feedback", name="help_feedback", requirements={"_scheme"="http"})
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/help/feedback/finished", name="help_feedback_finished", requirements={"_scheme"="http"}, options={"expose"=true})
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }
    
    /**
     * @Route("/help/company", name="help_company", requirements={"_scheme"="http"})
     */
    public function companyAction()
    {
        return $this->render('WenwenFrontendBundle:Help:company.html.twig');
    }

    /**
     * @Route("/help/ww", name="help_ww", requirements={"_scheme"="http"})
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:Help:91ww.html.twig');
    }

    /**
     * @Route("/help/regulations", name="help_regulations", requirements={"_scheme"="http"})
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:Help:regulations.html.twig');
    }

    /**
     * @Route("/help/map", name="help_map", requirements={"_scheme"="http"})
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:Help:map.html.twig');
    }

    /**
     * @Route("/help/cooperation", name="help_cooperation", requirements={"_scheme"="http"})
     */
    public function cooperationAction()
    {
        return $this->render('WenwenFrontendBundle:Help:cooperation.html.twig');
    }
}
