<?php

namespace Wenwen\FrontendBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\WeixinUser;
use JMS\JobQueueBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Form\LoginType;
use Wenwen\FrontendBundle\Form\UserProfileType;

/**
 * @Route("/auth/weixin")
 */
class WeixinLoginController extends BaseController
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

        if (!isset($code)) {
            $this->get('logger')->info('Weixin - 用户取消了授权');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->info('Weixin - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $msg = $this->getAccessToken($code);
        $token = $msg->access_token;
        $openId = $msg->openid;
        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $weixinUser = $em->getRepository('WenwenFrontendBundle:WeixinUser')->findOneBy(array('openId' => $openId));

        if ($weixinUser == null) {
            $weixinUser = new WeixinUser();
            $weixinUser->setOpenId($openId);
            $weixinUser->setNickname($userInfo->nickname);
            $weixinUser->setPhoto($userInfo->headimgurl);
            $weixinUser->setGender($userInfo->sex);
            $weixinUser->setUnionId($userInfo->unionid);
            $em->persist($weixinUser);
            $em->flush();

            return $this->redirect($this->generateUrl('weixin_bind', array('openId' => $openId)));
        } else if ($weixinUser->getUser() == null) {
            return $this->redirect($this->generateUrl('weixin_bind', array('openId' => $openId)));
        } else {
            if ($weixinUser->getUnionId() == null) {
                $weixinUser->setUnionId($userInfo->unionid);
            }
            $user = $weixinUser->getUser();
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($request->getClientIp());
            $em->flush();

            $request->getSession()->set('uid', $user->getId());

            $response = new RedirectResponse($this->generateUrl('_homepage'));
            $response->headers->setCookie(new Cookie('uid', $user->getId(), time() + 3600 * 24 * 365 * 10));
            $response->send();

            return $response;
        }
    }

    /**
     * @Route("/bind", name="weixin_bind", methods={"GET", "POST"})
     */
    public function bindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userForm = $this->createForm(new UserProfileType());

        $em = $this->getDoctrine()->getManager();
        $weixinUser = $em->getRepository('WenwenFrontendBundle:WeixinUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        $params = array(
            'openId' => $openId,
            'bind_route' => 'weixin_bind',
            'unbind_route' => 'weixin_unbind',
            'nickname' => $weixinUser->getNickname(),
            'photo' => $weixinUser->getPhoto(),
            'provinces' => $provinces,
            'cities' => $cities,
            'userForm' => $userForm->createView(),
        );

        $ipLocationService = $this->get('app.ip_location_service');
        $locationId = $ipLocationService->getLocationId($request->getClientIp());

        if($locationId['status']){
            $params['currentProvinceId'] = $locationId['provinceId'];
            $params['currentCityId'] = $locationId['cityId'];
        }

        if ($request->getMethod() == 'POST') {
            $loginForm->bind($request);

            if ($loginForm->isValid()) {
                $formData = $loginForm->getData();
                $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('email' => $formData['email']));

                if ($user == null || !$user->isPwdCorrect($formData['password'])) {
                    $loginForm->addError(new FormError('邮箱或密码不正确'));
                    $params['loginForm'] = $loginForm->createView();
                    return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
                }

                if (!$user->emailIsConfirmed()) {
                    $loginForm->addError(new FormError('邮箱尚未激活'));
                    $params['loginForm'] = $loginForm->createView();
                    return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
                }

                $weixinUser->setUser($user);
                $em->flush();

                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        $params['loginForm'] = $loginForm->createView();
        return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
    }

    /**
     * @Route("/unbind", name="weixin_unbind", methods={"GET", "POST"})
     */
    public function unbindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userProfile = new UserProfile();
        $userForm = $this->createForm(new UserProfileType(), $userProfile);

        $em = $this->getDoctrine()->getManager();
        $weixinUser = $em->getRepository('WenwenFrontendBundle:WeixinUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        if ($request->getMethod() == 'POST') {
            $userForm->bind($request);
            if ($userForm->isValid()) {
                $user = $weixinUser->getUser();
                if ($user == null) {
                    $user = $userService->autoRegister(
                        $weixinUser,
                        $userProfile,
                        $request->getClientIp(),
                        $request->headers->get('USER_AGENT'),
                        $request->getSession()->get('inviteId'),
                        $this->allowRewardInviter($request)
                    );
                    $this->pushBasicProfile($user, $em);
                }
                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        }

        return $this->render('WenwenFrontendBundle:User:bind.html.twig', array(
            'openId' => $openId,
            'bind_route' => 'weixin_bind',
            'unbind_route' => 'weixin_unbind',
            'nickname' => $weixinUser->getNickname(),
            'photo' => $weixinUser->getPhoto(),
            'provinces' => $provinces,
            'cities' => $cities,
            'loginForm' => $loginForm->createView(),
            'userForm' => $userForm->createView(),
        ));
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

    private function pushBasicProfile(User $user, EntityManager $em)
    {
        $args = array(
            '--user_id=' . $user->getId(),
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
        $em->persist($job);
        $em->flush();
    }
}