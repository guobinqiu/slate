<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Wenwen\AppBundle\WebService\Sop\SopUtil;
use SOPx\Auth\V1_1\Util;

/**
 * @Route("/project_survey")
 */
class ProjectSurveyController extends Controller
{

    /**
     * @Route("/information", name="_project_survey_information", options={"expose"=true})
     * @Template
     */
    public function informationAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', array('research' => $request->query->get('research')));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_project_survey_endlink")
     * @Template
     */
    public function endlinkAction(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $answer_status = $request->get('answer_status');

        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $arr['answer_status'] = $answer_status;
        $arr['survey_id'] = $survey_id;
        return $this->render('WenwenFrontendBundle:ProjectSurvey:complete.html.twig', $arr);
    }
}
