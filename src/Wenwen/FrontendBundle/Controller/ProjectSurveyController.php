<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/project_survey")
 */
class ProjectSurveyController extends Controller
{

    /**
     * @Route("/information", name="_project_survey_information", options={"expose"=true})
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
     */
    public function endlinkAction(Request $request)
    {
        $userId = $request->getSession()->get('uid');
        if (!$userId) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $cacheSettings = $this->container->getParameter('cache_settings');
        if ($cacheSettings['enable']) {
            $redis = $this->get('snc_redis.default');
            $redis->del(CacheKeys::getOrderHtmlSurveyListKey($userId));
        }

        return $this->render('WenwenFrontendBundle:ProjectSurvey:endlink.html.twig', array(
            'answer_status' => $request->get('answer_status'),
            'survey_id' => $request->get('survey_id'),
        ));
    }

    /**
     * @Route("/profile_questionnaire/endlink/complete")
     */
    public function profileQuestionnaireEndlinkAction(Request $request) {
        $userId = $request->getSession()->get('uid');
        if (!$userId) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $cacheSettings = $this->container->getParameter('cache_settings');
        if ($cacheSettings['enable']) {
            $redis = $this->get('snc_redis.default');
            $redis->del(CacheKeys::getOrderHtmlSurveyListKey($userId));
        }

        return $this->redirect($this->generateUrl('_homepage'));
    }
}
