<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/fulcrum_project_survey")
 */
class FulcrumProjectSurveyController extends BaseController
{
    /**
     * @Route("/information", name="_fulcrum_project_survey_information")
     */
    public function informationAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $fulcrum_research = $request->query->get('fulcrum_research');
        $participation = $this->get('app.survey_fulcrum_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $fulcrum_research['survey_id'],
            SurveyStatus::STATUS_INIT,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array('fulcrum_research' => $fulcrum_research));
    }

    /**
     * @Route("/forward", name="_fulcrum_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $fulcrum_research = $request->query->get('fulcrum_research');
        $participation = $this->get('app.survey_fulcrum_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $fulcrum_research['survey_id'],
            SurveyStatus::STATUS_FORWARD,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        $fulcrum_research = $this->get('app.survey_fulcrum_service')->addSurveyUrlToken($fulcrum_research, $this->getCurrentUserId());
        return $this->redirect($fulcrum_research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/complete", name="_fulcrum_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        $point = $this->get('app.survey_fulcrum_service')->processSurveyEndlink(
            $survey_id,
            $tid,
            $app_mid,
            SurveyStatus::STATUS_COMPLETE,
            $request->getClientIp()
        );
        return $this->redirect($this->generateUrl('_fulcrum_project_survey_endpage', array(
            'survey_id' => $survey_id,
            'point' => $point,
        )));
    }

    /**
     * @Route("/endpage", name="_fulcrum_project_survey_endpage")
     */
    public function endlinkPageAction(Request $request) {
        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig', array(
            'survey_id' => $request->query->get('survey_id'),
            'point' => $request->query->get('point'),
        ));
    }
}
