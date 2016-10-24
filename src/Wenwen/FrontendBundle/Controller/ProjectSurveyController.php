<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/project_survey")
 */
class ProjectSurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information", name="_project_survey_information", options={"expose"=true})
     */
    public function informationAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        $research = $request->query->get('research');
        $research = $this->get('app.survey_service')->addSurveyUrlToken($research, $user_id);

        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array(
            'research' => $research
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        $ticket_created = $this->get('app.survey_service')->createSurveyPrizeTicket(
            $survey_id,
            $request->query->get('tid'),
            $this->getCurrentUser(),
            $answer_status,
            'sop商业问卷'
        );

        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $answer_status,
            'survey_id' => $survey_id,
            'ticket_created' => $ticket_created
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink/complete")
     */
    public function profileQuestionnaireEndlinkCompleteAction(Request $request)
    {
        $ticket_created = $this->get('app.survey_service')->createProfilingPrizeTicket(
            $this->getCurrentUser(),
            $request->query->get('tid'),
            'sop属性问卷'
        );

        return $this->render('WenwenFrontendBundle:ProjectSurvey:profiling_endlink.html.twig', array(
            'ticket_created' => $ticket_created
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink/quit")
     */
    public function profileQuestionnaireEndlinkQuitAction()
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }
}
