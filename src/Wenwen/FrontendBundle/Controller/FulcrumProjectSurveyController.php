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
class FulcrumProjectSurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information", name="_fulcrum_project_survey_information", options={"expose"=true})
     */
    public function informationAction(Request $request)
    {
        $fulcrum_research = $request->query->get('fulcrum_research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.survey_fulcrum_service')->createParticipationHistory(
            $app_mid,
            $fulcrum_research['survey_id'],
            SurveyStatus::STATUS_INIT,
            SurveyStatus::UNANSWERED,
            $request->getClientIp()
        );
        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array('fulcrum_research' => $fulcrum_research));
    }

    /**
     * @Route("/forward", name="_fulcrum_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        $fulcrum_research = $request->query->get('fulcrum_research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.survey_fulcrum_service')->createParticipationHistory(
            $app_mid,
            $fulcrum_research['survey_id'],
            SurveyStatus::STATUS_FORWARD,
            SurveyStatus::UNANSWERED,
            $request->getClientIp()
        );
        $fulcrum_research = $this->get('app.survey_fulcrum_service')->addSurveyUrlToken($fulcrum_research, $user->getId());
        return $this->redirect($fulcrum_research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/complete", name="_fulcrum_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        $user = $this->getCurrentUser();
        $app_mid2 = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        if ($app_mid != $app_mid2) {
            throw new \InvalidArgumentException("fulcrum app_mid: {$app_mid} doesn't match its user_id: {$user->getId()}");
        }
        $this->get('app.survey_fulcrum_service')->processSurveyEndlink(
            $survey_id,
            $tid,
            $user,
            SurveyStatus::STATUS_COMPLETE,
            $app_mid,
            $request->getClientIp()
        );
        $point = $this->get('app.survey_fulcrum_service')->getSurveyPoint($user->getId(), $survey_id);
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
