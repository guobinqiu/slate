<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Jili\ApiBundle\Entity\WeixinUser;
use Jili\FrontendBundle\Form\Type\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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

        if ($weixinUser == null) {
            $weixinUser = new WeixinUser();
            $weixinUser->setOpenId($openId);
            $weixinUser->setNickname($userInfo->nickname);
            $weixinUser->setPhoto($userInfo->headimgurl);
            $weixinUser->setGender($userInfo->sex);
            $em->persist($weixinUser);
            $em->flush();

            return $this->redirect($this->generateUrl('weixin_bind', array('openId' => $openId)));
        } else if ($weixinUser->getUser() == null) {
            return $this->redirect($this->generateUrl('weixin_bind', array('openId' => $openId)));
        } else {
            $user = $weixinUser->getUser();
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($request->getClientIp());
            $em->flush();

            $request->getSession()->set('uid', $user->getId());
            return $this->redirect($this->generateUrl('_homepage'));
        }
    }

    /**
     * @Route("/bind", name="weixin_bind", methods={"GET", "POST"})
     */
    public function bindAction(Request $request)
    {
        $form = $this->createForm(new LoginType());

        $openId = $request->query->get('openId');
        $em = $this->getDoctrine()->getManager();
        $weixinUser = $em->getRepository('JiliApiBundle:WeixinUser')->findOneBy(array('openId' => $openId));

        $params = array(
            'openId' => $openId,
            'bind_route' => 'weixin_bind',
            'unbind_route' => 'weixin_unbind',
            'nickname' => $weixinUser->getNickname(),
            'photo' => $weixinUser->getPhoto(),
        );

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $user = $em->getRepository('JiliApiBundle:User')->findOneBy(array('email' => $formData['email']));

                if ($user == null || !$user->isPwdCorrect($formData['password'])) {
                    $form->addError(new FormError('邮箱或密码错误'));
                    $params['form'] = $form->createView();
                    return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
                }

                if (!$user->emailIsConfirmed()) {
                    $form->addError(new FormError('邮箱尚未激活'));
                    $params['form'] = $form->createView();
                    return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
                }

                $weixinUser->setUser($user);
                $em->flush();

                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        $params['form'] = $form->createView();
        return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
    }

    /**
     * @Route("/unbind", name="weixin_unbind", methods={"GET", "POST"})
     */
    public function unbindAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $openId = $request->query->get('openId');
        $weixinUser = $em->getRepository('JiliApiBundle:WeixinUser')->findOneBy(array('openId' => $openId));

        $currentTime = new \DateTime();
        $em->getConnection()->beginTransaction();
        try {
            $user = new User();
            $user->setNick($weixinUser->getNickname());
            $user->setPoints(User::POINT_SIGNUP);
            $user->setIconPath($weixinUser->getPhoto());
            $user->setRegisterDate($currentTime);
            $user->setRegisterCompleteDate($currentTime);
            $user->setLastLoginDate($currentTime);
            $user->setLastLoginIp($request->getClientIp());
            $user->setCreatedRemoteAddr($request->getClientIp());
            $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));
            $em->persist($user);

            $userProfile = new UserProfile();
            $userProfile->setSex($weixinUser->getGender());
            $userProfile->setUser($user);
            $em->persist($userProfile);

            $weixinUser->setUser($user);

            $em->flush();
            $em->getConnection()->commit();

            $request->getSession()->set('uid', $user->getId());
            return $this->redirect($this->generateUrl('_homepage'));

        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
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
        $params = array(
            'access_token' => $token,
            'openid' => $openId,
        );

        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($params);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody = $res->getBody();
        $msg = json_decode($resBody);

        if (isset($msg->errcode)) {
            throw new \RuntimeException('Weixin - ' . $msg->errmsg);
        }

        return $msg;
    }
}