<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// 不能加在service的公共代码，比方需要对session，cookie，request等对象进行操作的公共方法可以加到这里
class BaseController extends Controller
{
    protected function setCookie($name, $value, $expire = 0)
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie($name, $value, $expire));
        $response->send();
    }

    protected function clearCookies()
    {
        $response = new Response();
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->clearCookie($cookie->getName());
        }
        $response->send();
    }

    /**
     * UserAuthenticationEventListener粒度太粗，作用于类的所有方法。如果仅希望针对某些方法进行认证过滤可以调用此方法
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function loginAuthenticate(Request $request)
    {
        if (!$request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
    }

    /**
     * 被判定为在浏览器上留下过登录痕迹的用户就算多次注册，其邀请人也不能够获得积分
     *
     * @param Request $request
     * @return bool
     */
    protected function allowRewardInviter(Request $request)
    {
        return !$request->cookies->has('uid');
    }
}