<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\Entity\PrizeItem;

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
        $surveyId = $research['survey_id'];
        $userId = $this->getCurrentUser()->getId();
        $key = $userId . '_' . $surveyId;
        $token = md5(uniqid());
        $redis = $this->container->get('snc_redis.default');
        $redis->set($key, $token);

        $research['url'] = $this->get('app.survey_service')
            ->urlAddExtraParameters($research['url'], array('sop_custom_token' => $token));

        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array(
            'research' => $research
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request)
    {
        $anwerStatus = $request->query->get('answer_status');
        $tid = $request->query->get('tid');
        $surveyId = $request->get('survey_id');
        $userId = $this->getCurrentUser()->getId();
        $key = $userId . '_' . $surveyId;

        if ($sop_custom_token == $tid) {
            // 获得一次抽奖机会
            $this->get('app.prize_service')->createPrizeTicketForResearchSurvey(
                $this->getCurrentUser(),
                $anwerStatus,
                'sop商业问卷' . $anwerStatus
            );

            //防止通过反复刷页面来进行作弊
            $request->getSession()->set('sop_custom_token', uniqid());
        }

        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $anwerStatus,
            'survey_id' => ,
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
            PrizeItem::TYPE_BIG,
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
