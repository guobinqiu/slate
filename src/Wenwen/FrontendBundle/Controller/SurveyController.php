<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SOPx\Auth\V1_1\Util;
use VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;

/**
 * @Route("/survey",requirements={"_scheme"="http"})
 */
class SurveyController extends Controller
{
    /**
     * @Route("/index", name="_survey_index")
     * @Template
     */
    public function indexAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_survey_index'));

            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();

        // 快速問答
        $arr['votes'] = $em->getRepository('JiliApiBundle:Vote')->retrieveUnanswered($user_id);

        // SSI respondent
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        $ssi_res = array();
        if ($ssi_respondent) {
            $ssi_res['needPrescreening'] = $ssi_respondent->needPrescreening();
            $ssi_res['isActive'] = $ssi_respondent->isActive();
            if ($ssi_res['isActive']) {
                $dbh = $em->getConnection();
                $arr['ssi_surveys'] = SsiProjectRespondentQuery::retrieveSurveysForRespondent($dbh, $ssi_respondent->getId());
            }
        }
        $arr['ssi_respondent'] = $ssi_respondent;
        $arr['ssi_res'] = $ssi_res;

        // SOP
        $sop_config = $this->container->getParameter('sop');

        //sop_respondent 如果不存在就创建
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId($user_id);

        $sop_params = array(
            'app_id' => $sop_config['auth']['app_id'],
            'app_mid' => $sop_respondent->getId(),
            'time' => time(),
        );
        $sop_params['sig'] = Util::createSignature($sop_params, $sop_config['auth']['app_secret']);

        $arr['sop_params'] = $sop_params;

        $arr['sop_api_url'] = 'https://'.$sop_config['host'].'/api/v1_1/surveys/js?'.http_build_query(array(
            'app_id' => $sop_params['app_id'],
            'app_mid' => $sop_params['app_mid'],
            'sig' => $sop_params['sig'],
            'time' => $sop_params['time'],
            'sop_callback' => 'surveylistCallback',
        ));

        $arr['sop_point'] = $sop_config['point']['profile'];

        // for preview mode
        $arr['preview'] = $this->container->get('kernel')->getEnvironment() === 'dev' && $request->query->get('preview') === '1';

        return $this->render('WenwenFrontendBundle:Survey:index.html.twig', $arr);
    }
}
