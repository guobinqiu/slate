<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SOPx\Auth\V1_1\Util;


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
        $html_survey_list = [];
        try {
            $surveyService = $this->get('surveyService');
            if( in_array($this->container->get('kernel')->getEnvironment(), array('dev','test'))){
                // for dummy mode (won't access sop's server at dev or test mode)
                // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
                $surveyService->setDummy(true);
            }
            $html_survey_list = $surveyService->getOrderedHtmlServeyList($user_id);
        } catch (\Exception $e) {
            //echo $e->getMessage();
            $this->container->get('logger')->error($e->getMessage());
        }
        return $this->render('WenwenFrontendBundle:Survey:index.html.twig', array('html_survey_list' => $html_survey_list));
    }

    /**
     * @Route("/top", name="_survey_top")
     */
    public function topAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        if (!$user_id) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_survey_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        // 处理ssi和sop的排序，排序列表里存的是一个个通过模板渲染出来的html片段，每种模板分别对应一类问卷
        $html_survey_list = [];
        try {
            $surveyService = $this->get('surveyService');
            if( in_array($this->container->get('kernel')->getEnvironment(), array('dev','test'))){
                // for dummy mode (won't access sop's server at dev or test mode)
                // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
                $surveyService->setDummy(true);
            }
            $html_survey_list = $surveyService->getOrderedHtmlServeyList($user_id, 2); //第2个参数指定显示多少个，默认是全部
        } catch (\Exception $e) {
            //echo $e->getMessage();
            $this->container->get('logger')->error($e->getMessage());
        }
        return $this->render('WenwenFrontendBundle:Survey:_sopSurveyListHome.html.twig', array('html_survey_list' => $html_survey_list));
    }

}
