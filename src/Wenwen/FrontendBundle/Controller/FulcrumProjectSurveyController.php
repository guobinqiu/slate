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
        $sop_custom_token = uniqid();
        $request->getSession()->set('sop_custom_token', $sop_custom_token);

        $fulcrum_research = $request->query->get('fulcrum_research');
        $fulcrum_research['url'] = $this->get('app.survey_service')
            ->urlAddExtraParameters($fulcrum_research['url'], array('sop_custom_token' => $sop_custom_token));

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array(
            'fulcrum_research' => $fulcrum_research
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/complete")
     */
    public function endlinkAction(Request $request)
    {
        $tid = $request->query->get('tid');
        $sop_custom_token = $request->getSession()->get('sop_custom_token');
        if ($sop_custom_token == $tid) {
            // 获得一次抽奖机会
            $this->get('app.prize_service')->createPrizeTicketForResearchSurvey(
                $this->getCurrentUser(),
                $this->container->getParameter('research_survey_status_complete'),
                'fulcrum商业问卷complete'
            );

            //防止通过反复刷页面来进行作弊
            $request->getSession()->set('sop_custom_token', uniqid());
        }

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig');
    }
}
