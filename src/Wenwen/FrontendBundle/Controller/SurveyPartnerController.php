<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/survey_partner")
 */
class SurveyPartnerController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information/{surveyPartnerId}/", name="survey_partner_information")
     * 显示问卷信息页面
     */
    public function informationAction(Request $request, $surveyPartnerId)
    {
        // 获取用户id
        $user = $this->getCurrentUser();

        // 根据Ip获取该用户的地区信息
        $locationInfo = $this->getLocationInfoByClientIp($request);

        // 检查并更新用户的参与状态
        $surveyPartnerService = $this->get('app.survey_partner_service');
        $rtn = $surveyPartnerService->processInformation($user, $surveyPartnerId, $locationInfo);
        
        if('success' == $rtn['status']){
            // 返回状态为participable时，显示information页面
            $params = array();
            $params['title'] = $rtn['title'];
            $params['content'] = $rtn['content'];
            $params['loi'] = $rtn['loi'];
            $params['completePoint'] = $rtn['completePoint'];
            $params['difficulty'] = $rtn['difficulty'];
            $params['url'] = $this->generateUrl('survey_partner_redirect', array('surveyPartnerId' => $surveyPartnerId));
            return $this->render('WenwenFrontendBundle:SurveyPartner:information.html.twig', $params);
        } else if('participated' == $rtn['status']){
            // 返回状态为unparticipable时，跳转至endpage页面，显示已经参与过了，请返回首页
            $params = array();
            $params['answerStatus'] = 'participated'; // 已经参与过了
            $params['surveyPartnerId'] = $surveyPartnerId;
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        } else {
            // 返回状态为unparticipable时，跳转至endpage页面，显示已经参与过了，请返回首页
            // Todo 跳转至errorPage
            $params = array();
            $params['answerStatus'] = 'error'; // 系统错误
            $params['surveyPartnerId'] = $surveyPartnerId;
            $params['title'] = $surveyId;
            $params['key'] = 'SYSERR';
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        }
    }

    /**
     * @Route("/redirect_to_survey/{surveyPartnerId}/", name="survey_partner_redirect")
     * 重定向至问卷url
     */
    public function redirectToSurveyAction(Request $request, $surveyPartnerId)
    {
        // 获取用户
        $user = $this->getCurrentUser();

        // 根据Ip获取该用户的地区信息
        $locationInfo = $this->getLocationInfoByClientIp($request);

        // 检查并更新用户的参与状态
        $surveyPartnerService = $this->get('app.survey_partner_service');
        $rtn = $surveyPartnerService->redirectToSurvey($user, $surveyPartnerId, $locationInfo);

        if('success' == $rtn['status']){
            return $this->redirect($rtn['surveyUrl']);
        } else if('participated' == $rtn['status']){
            // 返回状态为unparticipable时，跳转至endpage页面，显示已经参与过了，请返回首页
            $params = array();
            $params['answerStatus'] = 'participated'; // 已经参与过了
            $params['surveyPartnerId'] = $surveyPartnerId;
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        } else if('notallowed' == $rtn['status']){
            // 返回状态为unparticipable时，跳转至endpage页面，显示已经参与过了，请返回首页
            $params = array();
            $params['answerStatus'] = 'notallowed'; // 已经参与过了
            $params['surveyPartnerId'] = $surveyPartnerId;
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        } else {
            // 返回状态为failure时，跳转至errorpage页面
            $params = array();
            $params['answerStatus'] = 'error'; // 系统错误
            $params['surveyPartnerId'] = $surveyPartnerId;
            $params['title'] = $surveyPartnerId;
            $params['key'] = 'SYSERR';
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        }
    }

    /**
     * @Route("/endlink/{answerStatus}/{partnerName}/{surveyId}/{uid}/{key}/", name="survey_partner_endlink")
     * 供第三方系统回传数据用的url
     */
    public function endlinkAction(Request $request, $answerStatus, $surveyId, $partnerName, $uid, $key)
    {
        $this->get('logger')->INFO(__METHOD__ . ' clientIp = ' . $request->getClientIp() . ' uri= ' . $request->getUri());
        $surveyPartnerService = $this->get('app.survey_partner_service');
        // 检查request的IP，非指定IP来的request不予响应，
        // 暂时限制为TripleS的IP
        $validIp = $surveyPartnerService->isValidEndlinkIp($request->getClientIp());
        if(! $validIp) {
            // redirect to error page
            $params = array();
            $params['surveyId'] = $surveyId;
            $params['key'] = $key;
            return new Response('Request not allowed.');
        }

        $rtn = $surveyPartnerService->processEndlink($uid, $answerStatus, $surveyId, $partnerName, $key);
        if($rtn['status'] == 'success'){
            $params = array();
            $params['answerStatus'] = $rtn['answerStatus'];
            $params['title'] = $rtn['title'];
            $params['rewardedPoint'] = $rtn['rewardedPoint'];
            $params['key'] = $rtn['key'];
            $params['ticketCreated'] = $rtn['ticketCreated'];
            return $this->redirect($this->generateUrl('survey_partner_endpage', $params));
        } else {
            // redirect to error page
            $params = array();
            $params['surveyId'] = $surveyId;
            $params['key'] = $key;
            return new Response('Can not process request.');
        }
    }

    /**
     * @Route("/endpage/", name="survey_partner_endpage")
     * 系统处理正常时展现给用户看的页面
     */
    public function endPageAction(Request $request)
    {
        $params = array();
        $params['answerStatus'] = $request->get('answerStatus');
        $params['title'] = $request->get('title');
        $params['rewardedPoint'] = $request->get('rewardedPoint');
        $params['key'] = $request->get('key');
        $params['ticketCreated'] = $request->get('ticketCreated');
        $this->get('logger')->debug(__METHOD__ . ' ' . json_encode($params));
        return $this->render('WenwenFrontendBundle:SurveyPartner:endpage.html.twig', $params);
    }

}
