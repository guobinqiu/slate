<?php

namespace Wenwen\FrontendBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\Entity\SurveyListJob;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/survey")
 */
class SurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/index", name="_survey_index")
     */
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * @Route("/top", name="_survey_top")
     */
    public function topAction(Request $request)
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }

    
}
