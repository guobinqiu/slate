<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HelpController extends Controller
{
    /**
     * @Route("/help", name="help_help")
     */
    public function helpAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/help/issue", name="help_issue")
     */
    public function issueAction()
    {
        return $this->render('WenwenFrontendBundle:Help:issue.html.twig');
    }

    /**
     * @Route("/help/newGuide", name="help_newGuide")
     */
    public function guideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/help/newGuide/detail", name="help_newGuide_detail")
     */
    public function guideDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuideDetail.html.twig');
    }

    /**
     * @Route("/help/feedback", name="help_feedback")
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/help/feedback/finished", name="help_feedback_finished", options={"expose"=true})
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }
    
    /**
     * @Route("/help/company", name="help_company")
     */
    public function companyAction()
    {
        return $this->render('WenwenFrontendBundle:Help:company.html.twig');
    }

    /**
     * @Route("/help/ww", name="help_ww")
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:Help:91ww.html.twig');
    }

    /**
     * @Route("/help/regulations", name="help_regulations")
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:Help:regulations.html.twig');
    }

    /**
     * @Route("/help/map", name="help_map")
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:Help:map.html.twig');
    }

    /**
     * @Route("/help/cooperation", name="help_cooperation")
     */
    public function cooperationAction()
    {
        return $this->render('WenwenFrontendBundle:Help:cooperation.html.twig');
    }
}
