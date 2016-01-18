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
 * @Route("/survey",requirements={"_scheme"="http"})
 */
class SurveyController extends Controller
{

    /**
     * @Route("/top")
     * @Template
     */
    public function topAction(Request $request)
    {
    }

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('wenwen_frontend_survey_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid'); //1057737
        $em = $this->getDoctrine()->getManager();

        // todo: 快速問答
        $arr['votes'] = array ();

        // todo: CINT


        // SOP


        // Array
        // (
        //     [host] => partners.surveyon.com.dev.researchpanelasia.com
        //     [auth] => Array
        //         (
        //             [app_id] => 27
        //             [app_secret] => 1436424899-bd6982201fb7ea024d0926aa1b40d541badf9b4a
        //         )
        //     [point] => Array
        //         (
        //             [profile] => 1
        //         )
        //     [api_v1_1_surveys_research_participation_history] => https://console.partners.surveyon.com.dev.researchpanelasia.com/api/v1_1/surveys/research/participation_history
        //     [api_v1_1_fulcrum_surveys_research_participation_history] => https://console.partners.surveyon.com.dev.researchpanelasia.com/api/v1_1/fulcrum/surveys/research/participation_history
        //     [api_v1_1_fulcrum_user_agreement_participation_history] => https://console.partners.surveyon.com.dev.researchpanelasia.com/api/v1_1/fulcrum/surveys/user_agreement/participation_history
        //     [api_v1_1_cint_surveys_research_participation_history] => https://console.partners.surveyon.com.dev.researchpanelasia.com/api/v1_1/cint/surveys/research/participation_history
        // )


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
        $arr['sop_params'] = $sop_params;

        // for preview mode
        $arr['preview'] = $this->container->get('kernel')->getEnvironment() === 'dev' && $request->query->get('preview') === '1';

        $arr['sop_params'] = $sop_params;
        $arr['sop_api_url'] = 'https://' . $sop_config['host'] . '/api/v1_1/surveys/js?' . http_build_query(array (
            'app_id' => $sop_params['app_id'],
            'app_mid' => $sop_params['app_mid'],
            'sig' => $sop_params['sig'],
            'time' => $sop_params['time'],
            'sop_callback' => 'surveylistCallback'
        ));
        //         $arr['sop_api_url'] = 'https://'.$sop_config['host'] . '/api/v1_1/surveys/js?' . http_build_query(array (
        //             'app_id' => $sop_params['app_id'],
        //             'app_mid' => $sop_params['app_mid'],
        //             'sig' => $sop_params['sig'],
        //             'time' => $sop_params['time'],
        //             'sop_callback' => 'surveylistCallback'
        //         ), '', '&amp;');


        $arr['sop_point'] = $sop_config['point']['profile'];

        return $this->render('WenwenFrontendBundle:Survey:index.html.twig', $arr);
    }
}
