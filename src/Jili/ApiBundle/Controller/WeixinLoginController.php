<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Jili\ApiBundle\Entity\WeixinUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/auth/weixin")
 */
class WeixinLoginController extends Controller
{
    /**
     * @Route("/login", name="weixin_login", methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $request->getSession()->set('state', $state);

        $params = array(
            'appid' => $this->container->getParameter('weixin_appid'),
            'redirect_uri' => $this->container->getParameter('weixin_callback'),
            'response_type' => 'code',
            'scope' => 'snsapi_login',
            'state' => $state,
        );

        $url = 'https://open.weixin.qq.com/connect/qrconnect?' . http_build_query($params);
        return $this->redirect($url);
    }

    /**
     * @Route("/callback", name="weixin_login_callback", methods={"GET"})
     */
    public function loginCallbackAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($code == null) {
            $this->get('logger')->error('Weixin - 用户取消了授权');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->error('Weixin - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $msg = $this->getAccessToken($code);
        $token = $msg->access_token;
        $openId = $msg->openid;

        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $weixinUser = $em->getRepository('JiliApiBundle:WeixinUser')->findOneBy(array('openId' => $openId));
        $currentTime = new \DateTime();

        if ($weixinUser == null) {
            $em->getConnection()->beginTransaction();
            try {
                $user = new User();
                $user->setNick($userInfo->nickname);
                $user->setPoints(User::POINT_SIGNUP);
                $user->setIconPath($userInfo->headimgurl);
                $user->setRegisterDate($currentTime);
                $user->setRegisterCompleteDate($currentTime);
                $user->setLastLoginDate($currentTime);
                $user->setLastLoginIp($request->getClientIp());
                $user->setCreatedRemoteAddr($request->getClientIp());
                $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));
                $em->persist($user);

                $weixinUser = new WeixinUser();
                $weixinUser->setOpenId($openId);
                $weixinUser->setUser($user);
                $em->persist($weixinUser);

                $userProfile = new UserProfile();
                $userProfile->setSex($userInfo->sex);
                $userProfile->setUser($user);
                $em->persist($userProfile);

                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        } else {
            $user = $weixinUser->getUser();
            $user->setLastLoginDate($currentTime);
            $user->setLastLoginIp($request->getClientIp());
            $em->flush();
        }

        $session = $request->getSession();
        $session->set('uid', $user->getId());

        return $this->redirect($this->generateUrl('_homepage'));
    }

    private function getAccessToken($code)
    {
        $params = array(
            'appid' => $this->container->getParameter('weixin_appid'),
            'secret' => $this->container->getParameter('weixin_appkey'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        );

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($params);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody = $res->getBody();

//        正确的返回
//        {
//            "access_token":"ACCESS_TOKEN",
//            "expires_in":7200,
//            "refresh_token":"REFRESH_TOKEN",
//            "openid":"OPENID",
//            "scope":"SCOPE",
//            "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
//        }
//        错误的返回
//        {"errcode":40029,"errmsg":"invalid code"}
        $msg = json_decode($resBody);

        if (isset($msg->errcode)) {
            throw new \RuntimeException('Weixin - ' . $msg->errmsg);
        }

        return $msg;
    }

    private function getUserInfo($token, $openId) {
        $queryParams = array(
            'access_token' => $token,
            'openid' => $openId,
        );

        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($queryParams);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody= $res->getBody();
        $msg = json_decode($resBody);

        if (isset($msg->errcode)) {
            throw new \RuntimeException('Weixin - ' . $msg->errmsg);
        }

        return $msg;
    }
}