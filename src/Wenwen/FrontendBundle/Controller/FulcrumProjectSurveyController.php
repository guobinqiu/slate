<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/fulcrum_project_survey")
 */
class FulcrumProjectSurveyController extends Controller
{
    /**
     * @Route("/information", options={"expose"=true} )
     */
    public function informationAction(Request $request)
    {
        if (! $request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        return $this->render('WenwenFrontendBundle:FulcrumProjectSurvey:information.html.twig', array('fulcrum_research' => $request->query->get('fulcrum_research')));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}")
     */
    public function endlinkAction(Request $request)
    {
        $userId = $request->getSession()->get('uid');
        if (!$userId) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $redis = $this->get('snc_redis.default');
        $redis->del(CacheKeys::getOrderHtmlSurveyListKey($userId));

        return $this->render('WenwenFrontendBundle:ProjectSurveyFulcrum:endlink.html.twig');
    }

}
