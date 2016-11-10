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
}