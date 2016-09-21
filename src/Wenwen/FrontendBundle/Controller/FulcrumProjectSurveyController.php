<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/fulcrum_project_survey")
 */
class FulcrumProjectSurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information", options={"expose"=true} )
     */
    public function informationAction(Request $request)
    {
        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array('fulcrum_research' => $request->query->get('fulcrum_research')));
    }

    /**
     * @Route("/endlink/{survey_id}/complete")
     */
    public function endlinkAction()
    {
        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig');
    }

}
