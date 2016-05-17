<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
#use Symfony\Component\HttpFoundation\Response;
use Wenwen\AppBundle\WebService\Sop\SopUtil;
use SOPx\Auth\V1_1\Util;



/**
 * @Route("/fulcrum_project_survey",requirements={"_scheme"="https"})
 */
class FulcrumProjectSurveyController extends Controller
{

  
    /**
     * @Route("/information/{survey_id}", options={"expose"=true} )
     */
    public function informationAction(Request $request, $survey_id)
    {
        if (! $request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array('fulcrum_research' => $request->query->get('fulcrum_research')));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}")
     * @Template("WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        if (!preg_match('/\A(?:complete)\z/', $answer_status)) {
            throw $this->createNotFoundException('The the answer status  not exist');
        }

        return array();
    }

}
