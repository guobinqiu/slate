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
        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array(
            'research' => $request->query->get('research')
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request)
    {
        $anwerStatus = $request->get('answer_status');

        // 获得一次抽奖机会
        $this->get('app.survey_service')->createPrizeTicketForResearchSurvey(
            $this->getCurrentUser(),
            $anwerStatus,
            'sop商业问卷' . $anwerStatus
        );

        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $anwerStatus,
            'survey_id' => $request->get('survey_id'),
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink/complete")
     */
    public function profileQuestionnaireEndlinkCompleteAction()
    {
        // 获得一次抽奖机会
        $this->get('app.prize_service')->createPrizeTicket(
            $this->getCurrentUser(),
            PrizeItem::TYPE_SMALL,
            'sop属性问卷complete'
        );

        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * @Route("/profile_questionnaire/endlink/quit")
     */
    public function profileQuestionnaireEndlinkQuitAction()
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }
}
