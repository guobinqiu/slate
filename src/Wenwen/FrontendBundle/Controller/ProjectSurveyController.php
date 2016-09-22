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
        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array('research' => $request->query->get('research')));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     */
    public function endlinkAction(Request $request)
    {
        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $request->get('answer_status'),
            'survey_id' => $request->get('survey_id'),
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink")
     */
    public function profileQuestionnaireEndlinkAction() {
        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * 供外部系统调用的endlink
     *
     * @Route("/outer/endlink/{answer_status}")
     */
    public function outerEndlinkAction(Request $request) {
        $answer_status = $request->get('answer_status');

        if (!in_array($answer_status, array('complete', 'screenout', 'quotafull'))) {
            throw new \InvalidArgumentException('Wrong status');
        }

        return $this->render('WenwenFrontendBundle:ProjectSurvey:outer_endlink.html.twig', array(
            'answer_status' => $request->get('answer_status'),
        ));
    }
}
