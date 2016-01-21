<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
#use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Utility\SopUtil;



/**
 * @Route("/fulcrum_project_survey",requirements={"_scheme"="https"})
 */
class FulcrumProjectSurveyController extends Controller
{

    /**
     * @Route("/demo" )
     * @Template("WenwenFrontendBundle:FulcrumProjectSurvey:demo.html.twig")
     */
    public function demoAction(Request $request )
    {

        if (! $request->getSession()->has('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('wenwen_frontend_fulcrumprojectsurvey_demo'));
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $user_id = $request->getSession()->get('uid');

        // create sop JSONP URL
        $sop_config     = $this->container->getParameter('sop');
        $em = $this->getDoctrine()->getManager();
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId($user_id);
        $sop_params = array (
            'app_id' => $sop_config['auth']['app_id'],
            'app_mid' => $sop_respondent->getId(),
            'time' => time()
        );
        $sop_params['sig'] = SopUtil::createSignature($sop_params, $sop_config['auth']['app_secret']);
        $sop_params['sop_callback'] = 'surveylistCallback';


        $env =  $this->get('kernel')->getEnvironment();

        return array('sop_params'=> $sop_params,  'env'=>$env);
    }
  
    /**
     * @Route("/information/{survey_id}", options={"expose"=true} )
     * @Template("WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig")
     */
    public function informationAction(Request $request, $survey_id)
    {
        if (! $request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid');

        // create sop JSONP URL
        $sop_config     = $this->container->getParameter('sop');
        $em = $this->getDoctrine()->getManager();
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId($user_id);
        $sop_params = array (
            'app_id' => $sop_config['auth']['app_id'],
            'app_mid' => $sop_respondent->getId(),
            'time' => time()
        );
        $sop_params['sig'] = SopUtil::createSignature($sop_params, $sop_config['auth']['app_secret']);
        $sop_params['sop_callback'] = 'surveylistCallback';

        $url = SopUtil::getJsopURL($sop_params, $sop_config['host']);
        $survey_id = $request->query->get('survey_id');

        return array('url'=> $url, 'survey_id'=> $survey_id);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}")
     * @Template("WenwenFrontendBundle:FulcrumProjectSurvey:endlink.html.twig")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {

        if (!preg_match('/\A(?:complete|screenout|quotafull|error)\z/', $answer_status)) {
            throw $this->createNotFoundException('The the answer status  not exist');

        }

        return array();
    }

    /**
     * @Route("/error")
     * @Template("WenwenFrontendBundle:FulcrumProjectSurvey:error.html.twig")
     */
    public function errorAction(Request $request)
    {
        return array();
    }
}
