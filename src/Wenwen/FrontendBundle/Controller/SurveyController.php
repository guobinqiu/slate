<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/survey")
 */
class SurveyController extends Controller
{
    /**
     * @Route("/index", name="_survey_index")
     */
    public function indexAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        if (!$user_id) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_survey_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // 处理ssi和sop的排序，排序列表里存的是一个个通过模板渲染出来的html片段，每种模板分别对应一类问卷
        $surveyService = $this->get('app.survey_service');
        $env = $this->container->get('kernel')->getEnvironment();
        if (in_array($env, array('dev','test'))) {
            // for dummy mode (won't access sop's server at dev or test mode)
            // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
            $surveyService->setDummy(true);
        }
        $html_survey_list = $this->getOrderedHtmlSurveyList($user_id);

        return $this->render('WenwenFrontendBundle:Survey:index.html.twig', array('html_survey_list' => $html_survey_list));
    }

    /**
     * @Route("/top", name="_survey_top")
     */
    public function topAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        if (!$user_id) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_survey_top'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // 处理ssi和sop的排序，排序列表里存的是一个个通过模板渲染出来的html片段，每种模板分别对应一类问卷
        $surveyService = $this->get('app.survey_service');
        $env = $this->container->get('kernel')->getEnvironment();
        if (in_array($env, array('dev','test'))) {
            // for dummy mode (won't access sop's server at dev or test mode)
            // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
            $surveyService->setDummy(true);
        }
        $html_survey_list = $this->getOrderedHtmlSurveyList($user_id);

        return $this->render('WenwenFrontendBundle:Survey:_sopSurveyListHome.html.twig', array('html_survey_list' => $html_survey_list));
    }

    /**
     * 读取问卷列表先走缓存
     *
     * @param $user_id
     * @return array
     */
    private function getOrderedHtmlSurveyList($user_id, $redisEnabled = true) {
        $surveyService = $this->get('app.survey_service');
        
        if (!$redisEnabled) {
            return $surveyService->getOrderedHtmlSurveyList($user_id);
        }

        $redis = $this->get('snc_redis.default');
        $cacheKey = CacheKeys::getOrderHtmlSurveyListKey($user_id);
        $cacheVal = $redis->get($cacheKey);

        if (is_null($cacheVal)) {
            $html_survey_list = $surveyService->getOrderedHtmlSurveyList($user_id);
            if (!empty($html_survey_list)) {
                $redis->set($cacheKey, serialize($html_survey_list));
                $redis->expire($cacheKey, 60 * 60 * 8); //缓存8小时
            }
            return $html_survey_list;
        }

        return unserialize($cacheVal);
    }
}
