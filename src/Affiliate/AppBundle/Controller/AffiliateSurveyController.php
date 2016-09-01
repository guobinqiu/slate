<?php
namespace Affiliate\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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

    	$param = array(
    		'answer_status' => $rtn['status'],
            'point' => $rtn['point'],
            'secret' => $rtn['secret']
    		);
        return $this->render('AffiliateAppBundle::endpage.html.twig', $param);
    }

}
