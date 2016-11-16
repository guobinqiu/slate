<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// 不能加在service的公共代码，比方需要对session，cookie，request等对象进行操作的公共方法可以加到这里
class BaseController extends Controller
{
    protected function redirectWithCookie($url, Cookie $cookie, $status = 302)
    {
        $response = new RedirectResponse($url, $status);
        $response->headers->setCookie($cookie);
        return $response;
    }

    protected function clearCookies(Request $request, Response $response)
    {
        $cookieNames = $request->cookies->keys();
        foreach($cookieNames as $name) {
            $response->headers->clearCookie($name);
        }
    }

    protected function getCurrentUser()
    {
        $user = null;
        $session = $this->getRequest()->getSession();
        if ($session->has('uid')) {
            $user = $this->getDoctrine()->getRepository('WenwenFrontendBundle:User')->find($session->get('uid'));
        }
        return $user;
    }

    /**
     * @return locationInfo = array(
     *                             'status' => true/false,
     *                             'errmsg' => '', // status是false时 的错误信息
     *                             'city' => '没找到对应的城市', // 城市名
     *                             'province' => '没找到对应的省份' // 省份名
     *                             'clientIp' => 'xxx.xxx.xxx.xxx' // ip地址
     *                             )
     */
    protected function getLocationInfoByClientIp(Request $request)
    {
        // 获取clientIp
        $clientIp = $request->getClientIp();
        $ipLocationService = $this->get('app.ip_location_service');
        // 通过IP获取地区信息
        $locationInfo = $ipLocationService->getLocationInfo($clientIp);
        return $locationInfo;
    }


    /**
     * 往session里面添加注册渠道的关键字
     *
     */
    protected function setRegisterRouteInSession(Request $request){
        // parameter里如果有recruite，将其记入session
        $recruitRoute = $request->get('recruit');
        $this->get('logger')->debug(__METHOD__ . ' recruitRoute=' . $recruitRoute);
        if( ! empty($recruitRoute)){
            $this->get('logger')->debug(__METHOD__ . ' found and recorded parameter(into session) recruit=' . $recruitRoute);
            $this->get('session')->set('recruit_route', $recruitRoute);
        }
    }

    /**
     * 从session里面获取注册渠道的关键字
     * 
     *
     */
    protected function getRegisterRouteFromSession(){
        $recruitRoute = $this->get('session')->get('recruit_route');
        $inviteId = $this->get('session')->get('inviteId');
        if( empty($recruitRoute)){
            if( empty($inviteId){
                $recruitRoute = 'organic';
            }
            $recruitRoute = 'friend_invite';
        }
        $this->get('logger')->debug(__METHOD__ . ' registerRoute=' . $recruitRoute);
        return $recruitRoute;
    }

}
