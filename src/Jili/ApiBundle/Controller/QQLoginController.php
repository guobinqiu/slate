<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\QQUser;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/auth/qq")
 */
class QQLoginController extends Controller
{
    /**
     * @Route("/login", name="qq_login", methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $request->getSession()->set('state', $state);
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->container->getParameter('qq_appid'),
            'redirect_uri' => $this->container->getParameter('qq_callback'),
            'state' => $state,
        );
        $url = 'https://graph.qq.com/oauth2.0/authorize?' . http_build_query($params);
        return $this->redirect($url);
    }

    /**
     * @Route("/callback", name="qq_login_callback", methods={"GET"})
     */
    public function loginCallbackAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($code == null) {
            $this->get('logger')->error('QQ - 用户取消了授权');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->error('QQ - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $token = $this->getAccessToken($code);
        $openId = $this->getOpenId($token);
        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $qqUser = $em->getRepository('JiliApiBundle:QQUser')->findOneBy(array('openId' => $openId));
        $currentTime = new \DateTime();

        if ($qqUser == null) {
            $em->getConnection()->beginTransaction();
            try {
                $user = new User();
                $user->setNick($userInfo->nickname);
                $user->setPoints(User::POINT_SIGNUP);
                $user->setIconPath($userInfo->figureurl_qq_1);
                $user->setRegisterDate($currentTime);
                $user->setRegisterCompleteDate($currentTime);
                $user->setLastLoginDate($currentTime);
                $user->setLastLoginIp($request->getClientIp());
                $user->setCreatedRemoteAddr($request->getClientIp());
                $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));
                $em->persist($user);

                $qqUser = new QQUser();
                $qqUser->setOpenId($openId);
                $qqUser->setUser($user);
                $em->persist($qqUser);

                $userProfile = new UserProfile();
                $userProfile->setSex($userInfo->gender == '女' ? 2 : 1);
                $userProfile->setUser($user);
                $em->persist($userProfile);

                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        } else {
            $user = $qqUser->getUser();
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
            'grant_type' => 'authorization_code',
            'client_id' => $this->container->getParameter('qq_appid'),
            'client_secret' => $this->container->getParameter('qq_appkey'),
            'code' => $code,
            'redirect_uri' => $this->container->getParameter('qq_callback'),
        );
        $url = 'https://graph.qq.com/oauth2.0/token?' . http_build_query($params);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody = $res->getBody();

        //有错误返回callback(...)
        //strpos这个方法tmd太不爽，找到了会返回int 0，找不到会返回boolean false，false又等于0，那到底算找到还是没找到呢
        //http://php.net/manual/en/function.strpos.php
        if (strpos($resBody, 'callback') !== false) {
            $lpos = strpos($resBody, "(");
            $rpos = strrpos($resBody, ")");
            $resBody = substr($resBody, $lpos + 1, $rpos - $lpos - 1);
            $msg = json_decode($resBody);
            if (isset($msg->errcode)) {
                throw new \RuntimeException('QQ - ' . $msg->error_description);
            }
        }

        //没有错误返回类似access_token=CEF4918E9614F9C1307DCA3FFC32BE55&expires_in=7776000&refresh_token=A6E855EB50CCB6F14BB37D87D413550B
        $params = array();
        parse_str($resBody, $params);

        return $params['access_token'];
    }

    private function getOpenId($token) {
        $url = 'https://graph.qq.com/oauth2.0/me?access_token=' . $token;
        $res = $this->get('app.http_client')->get($url)->send();

        //callback( {"client_id":"YOUR_APPID","openid":"YOUR_OPENID"} );
        $resBody = $res->getBody();
        if (strpos($resBody, "callback") !== false) {
            $lpos = strpos($resBody, "(");
            $rpos = strrpos($resBody, ")");
            $resBody = substr($resBody, $lpos + 1, $rpos - $lpos - 1);
        }

        $msg = json_decode($resBody);

        if (isset($msg->error)) {
            throw new \RuntimeException('QQ - ' . $msg->error_description);
        }

        return $msg->openid;
    }

    private function getUserInfo($token, $openId) {
        $params = array(
            'access_token' => $token,
            'oauth_consumer_key' => $this->container->getParameter('qq_appid'),
            'openid' => $openId,
        );
        $url = 'https://graph.qq.com/user/get_user_info?' . http_build_query($params);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody = $res->getBody();
        return json_decode($resBody);
    }
}
