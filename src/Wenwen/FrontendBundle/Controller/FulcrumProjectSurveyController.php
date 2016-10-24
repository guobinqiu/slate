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
        $user_id = $request->getSession()->get('uid');
        $fulcrum_research = $request->query->get('fulcrum_research');
        $fulcrum_research = $this->get('app.survey_service')->addUrlToken($fulcrum_research, $user_id);

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array(
            'fulcrum_research' => $fulcrum_research
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/complete")
     */
    public function endlinkAction(Request $request, $survey_id)
    {
        $ticket_created = $this->get('app.survey_service')->createPrizeTicket(
            $survey_id,
            $request->query->get('tid'),
            $this->getCurrentUser(),
            $this->container->getParameter('research_survey_status_complete'),
            'fulcrum商业问卷complete' . $survey_id
        );

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig', array(
            'ticket_created' => $ticket_created
        ));
    }
}
