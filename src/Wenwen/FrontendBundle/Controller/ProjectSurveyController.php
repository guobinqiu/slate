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
class ProjectSurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information", name="_project_survey_information", options={"expose"=true})
     */
    public function informationAction(Request $request)
    {
        $research = $request->query->get('research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.sop_survey_service')->createStatusHistory($app_mid, $research['survey_id'], SurveyStatus::STATUS_INIT);
        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array('research' => $research));
    }

    /**
     * @Route("/forward", name="_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        $research = $request->query->get('research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.sop_survey_service')->createStatusHistory($app_mid, $research['survey_id'], SurveyStatus::STATUS_FORWARD);
        return $this->redirect($research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        if (!SurveyStatus::isValid($answer_status)) {
            throw new \InvalidArgumentException("sop invalid answer status: {$answer_status}");
        }
        $user = $this->getCurrentUser();
        $app_mid2 = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        if ($app_mid != $app_mid2) {
            throw new \InvalidArgumentException("sop app_mid: {$app_mid} doesn't match its user_id: {$user->getId()}");
        }
        $this->get('app.sop_survey_service')->processSurveyEndlink($survey_id, $tid, $user, $answer_status, $app_mid);
        $point = $this->get('app.sop_survey_service')->getResearchSurveyPoint($app_mid, $survey_id);
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
        $this->get('app.sop_survey_service')->processProfilingEndlink(
            $this->getCurrentUser(),
            $request->query->get('tid')
        );
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
