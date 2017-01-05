<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/survey_gmo")
 */
class SurveyGmoController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information", name="survey_gmo_information")
     */
    public function informationAction(Request $request)
    {
        $research = $request->query->get('research');
        $user = $this->getCurrentUser();
//        $this->get('app.survey_sop_service')->createParticipationByUserId(
//            $user->getId(),
//            $research['research_id'],
//            SurveyStatus::STATUS_INIT,
//            $request->getClientIp()
//        );
        return $this->render('WenwenFrontendBundle:SurveyGmo:information.html.twig', array('research' => $research));
    }

    /**
     * @Route("/forward", name="survey_gmo_forward")
     */
    public function forwardAction(Request $request)
    {
        $research = $request->query->get('research');
        $user = $this->getCurrentUser();
//        $this->get('app.survey_sop_service')->createParticipationByUserId(
//            $user->getId(),
//            $research['survey_id'],
//            SurveyStatus::STATUS_FORWARD,
//            $request->getClientIp()
//        );
        return $this->redirect($research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="survey_gmo_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {

    }

    /**
     * @Route("/endpage", name="survey_gmo_endpage")
     */
    public function endlinkPageAction(Request $request) {

    }
}
