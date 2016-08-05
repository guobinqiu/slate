<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * 问卷代理
 * 对还没有注册的用户提供回答问卷的机会
 */
class FreeSurveyController extends Controller
{

    public function indexAction(Request $request)
    {
        $partnerId = $request->get('partner_id');
        $projectId = $request->get('project_id');
        $freeSurveyService = $this->get('app.free_survey_service');

        $status = $freeSurveyService->validateProjectStatus($partnerId, $projectId);

        if(false == $status){
        	// 这个partnerId 的 projectId 不处于开放状态中或者这个项目并不存在
        	// 渲染一个页面告知项目不在执行中
        	return $this->redirect('http://www.sina.com.cn');
        }

        $redirectURL = $freeSurveyService->getSurveyURL($partnerId, $projectId);

        return $this->redirect($redirectURL);
    }

    public function endpageAction(Request $request)
    {
    	// 设置endpage时要有status参数
    	// status: complete/screenout/quotafull/error
    	$status = $request->get('status');

    	$msg = "亲，出错啦";
    	if('complete' == $status){
    		$msg = "亲，恭喜你回答完成咯";
    	}
    	if('screenout' == $status){
    		$msg = "亲，很遗憾，你不符合这次调查的要求哦";
    	}
    	if('quotafull' == $status){
    		$msg = "亲，很遗憾，你来晚啦，调查参与名额已满";
    	}
    	if('error' == $status){
    		$msg = "亲，你可能已经参与过这个调查了";
    	}

    	$param = array(
    		'answer_status' => $status,
    		'msg' => $msg
    		);
        return $this->render('WenwenFrontendBundle:Free/Survey:endpage.html.twig', $param);
    }
}
