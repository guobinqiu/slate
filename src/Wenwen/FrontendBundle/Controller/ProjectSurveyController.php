<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/project_survey")
 */
class ProjectSurveyController extends BaseController
{
    /**
     * @Route("/information", name="_project_survey_information")
     */
    public function informationAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $research = $request->query->get('research');
        $participation = $this->get('app.survey_sop_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $research['survey_id'],
            SurveyStatus::STATUS_INIT,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        $notifiable = $this->get('app.survey_sop_service')->isNotifiableSurvey($research['survey_id']);
        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array('research' => $research, 'notifiable' => $notifiable));
    }

    /**
     * @Route("/forward", name="_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $research = $request->query->get('research');
        $participation = $this->get('app.survey_sop_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $research['survey_id'],
            SurveyStatus::STATUS_FORWARD,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        $research = $this->get('app.survey_sop_service')->addSurveyUrlToken($research, $this->getCurrentUserId());
        return $this->redirect($research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        $point = $this->get('app.survey_sop_service')->processSurveyEndlink(
            $survey_id,
            $tid,
            $app_mid,
            $answer_status,
            $request->getClientIp()
        );
        return $this->redirect($this->generateUrl('_project_survey_endpage', array(
            'answer_status' => $answer_status,
            'survey_id' => $survey_id,
            'point' => $point,
        )));
    }

    /**
     * @Route("/endpage", name="_project_survey_endpage")
     */
    public function endlinkPageAction(Request $request) {
        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $request->query->get('answer_status'),
            'survey_id' => $request->query->get('survey_id'),
            'point' => $request->query->get('point'),
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink/complete")
     */
    public function profileQuestionnaireEndlinkCompleteAction(Request $request)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        $this->get('app.survey_sop_service')->processProfilingEndlink($app_mid, $tid);
        return $this->render('WenwenFrontendBundle:ProjectSurvey:profiling_endlink.html.twig');
    }

    /**
     * @Route("/profile_questionnaire/endlink/quit")
     */
    public function profileQuestionnaireEndlinkQuitAction()
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }
}
