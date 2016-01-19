<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Utility\SopUtil;

/**
 * @Route("/projectSurvey",requirements={"_scheme"="http"})
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
            $this->get('request')->getSession()->set('referer', $this->generateUrl('wenwen_frontend_survey_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid'); //1057737
        $em = $this->getDoctrine()->getManager();

        //create sop JSONP URL
        $sop_config = $this->container->getParameter('sop_frontend');

        //todo: $sop_respondent 如果不存在就创建
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->findOneByUserId($user_id);
        //         $sop_respondent = SopRespondentPeer::retrieveOrInsertByPanelistId($panelist->getId());

        $sop_params = array (
            'app_id' => $sop_config['auth']['app_id'],
            'app_mid' => $sop_respondent->getId(),
            'time' => time()
        );
        $sop_params['sig'] = SopUtil::createSignature($sop_params, $sop_config['auth']['app_secret']);
        $sop_params['sop_callback'] = 'surveylistCallback';

        $arr['url']       = SopUtil::getJsopURL($sop_params, $sop_config['host']);
        //$arr['survey_id'] = $request->getParameter('survey_id');
        $arr['survey_id'] = $request->query->get('survey_id');

        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', $arr);
    }
}
