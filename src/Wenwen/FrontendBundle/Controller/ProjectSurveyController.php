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
 * @Route("/project_survey",requirements={"_scheme"="https"})
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
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_survey_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();

        //create sop JSONP URL
        $sop_config = $this->container->getParameter('sop');

        //sop_respondent 如果不存在就创建
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId($user_id);

        $sop_params = array (
            'app_id' => $sop_config['auth']['app_id'],
            'app_mid' => $sop_respondent->getId(),
            'time' => time()
        );
        $sop_params['sig'] = Util::createSignature($sop_params, $sop_config['auth']['app_secret']);
        $sop_params['sop_callback'] = 'surveylistCallback';

        $arr['url'] = SopUtil::getJsopURL($sop_params, $sop_config['host']);
        $arr['survey_id'] = $request->query->get('survey_id');

        // for preview mode
        $arr['preview'] = $this->container->get('kernel')->getEnvironment() === 'dev' && $request->query->get('preview') === '1';

        return $this->render('WenwenFrontendBundle:ProjectSurvey:information.html.twig', $arr);
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
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_project_survey_endlink', array (
                'survey_id' => $survey_id,
                'answer_status' => $answer_status
            )));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if (!preg_match('/\A(?:complete|screenout|quotafull)\z/', $answer_status)) {

            $errorMessage = "尊敬的用户，您好！
编号为 r" . $survey_id . " 的问卷出现了一些小问题，此时无法确认您的回答是否有效。
请联系我们的客服，告知问卷编号（r" . $survey_id . "）和您的账号，我们的客服会尽最快的速度答复您。
给您添麻烦了，请继续关注91问问，谢谢！";

            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array (
                'errorMessage' => $errorMessage
            ));
        }

        $arr['answer_status'] = $answer_status;
        return $this->render('WenwenFrontendBundle:ProjectSurvey:complete.html.twig', $arr);
    }
}
