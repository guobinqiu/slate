<?php
namespace Affiliate\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * 问卷代理
 * 对还没有注册的用户提供回答问卷的机会
 */
class AffiliateSurveyController extends Controller
{

    public function showSurveyAction(Request $request, $affiliateProjectId = null)
    {

        $adminProjectService = $this->get('app.admin_project_service');

        $rtn = $adminProjectService->validateProjectStatus($affiliateProjectId);

        if('failure' == $rtn['status']){
        	// 这个projectId 不处于开放状态中或者这个项目并不存在
        	// 渲染一个页面告知项目不在执行中
            $param = array(
                'answer_status' => 'other'
            );
        	return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
        }

        $affiliateSurveyService = $this->get('app.affiliate_survey_service');
        $affiliateProjectLocationService = $this->get('app.af_location_service');
        // $affiliateProjectLocationService->setDummy(true, "宁波市", "浙江省");
        
        // 判断用户Location与项目中的Location是否一致       

        $checkResult=$affiliateProjectLocationService->confirmLocation($request->getClientIp(),$affiliateProjectId);
        if(!$checkResult){
            $param = array(
                'answer_status' => 'screenout'
            );
            return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
        }

        $redirectURL = $affiliateSurveyService->getSurveyURL($affiliateProjectId);
        
        if(is_null($redirectURL)){
            $param = array(
                'answer_status' => 'other'
            );
            return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
        }

        return $this->redirect($redirectURL);
    }

    public function showEndpageAction(Request $request)
    {
    	// 设置endpage时要有status参数
    	// status: complete/screenout/quotafull/error
    	$status = $request->get('status');
        $ukey = $request->get('uniq_key');

        $affiliateSurveyService = $this->get('app.affiliate_survey_service');
        $rtn = $affiliateSurveyService->processEndpage($status, $ukey);

        // 在session里记录该问卷的完成奖励point数
        // 通过这里完成的注册，可以在常规奖励的基础上额外奖励这个point
        $session = $request->getSession();
        $session->set('complete_point', $rtn['complete_point']);

    	$param = array(
    		'answer_status' => $rtn['status'], // 该次问卷的完成状态
            'ukey' => $rtn['ukey'], // 该次问卷的链接的唯一标识
            'complete_point' => $rtn['complete_point'] // 0 point表示没有额外奖励
    		);
        return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
    }

}
