<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\WeiboUser;
use JMS\JobQueueBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Form\LoginType;
use Wenwen\FrontendBundle\Form\UserProfileType;

/**
 * @Route("/auth/weibo")
 */
class WeiboLoginController extends BaseController
{
    /**
     * @Route("/login", name="weibo_login", methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $request->getSession()->set('state', $state);
        $params = array(
            'client_id' => $this->container->getParameter('weibo_appid'),
            'redirect_uri' => $this->container->getParameter('weibo_callback'),
            'state' => $state,
        );
        $url = 'https://api.weibo.com/oauth2/authorize?' . http_build_query($params);
        return $this->redirect($url);
    }

    /**
     * @Route("/callback", name="weibo_login_callback", methods={"GET"})
     */
    public function loginCallbackAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->info('Weibo - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $token = $this->getAccessToken($code);
        $openId = $this->getOpenId($token);
        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $weiboUser = $em->getRepository('WenwenFrontendBundle:WeiboUser')->findOneBy(array('openId' => $openId));

        if ($weiboUser == null) {
            $weiboUser = new WeiboUser();
            $weiboUser->setOpenId($openId);
            $weiboUser->setNickname($userInfo->screen_name);
            $weiboUser->setPhoto($userInfo->profile_image_url);
            $weiboUser->setGender($userInfo->gender == 'f' ? 2 : 1);
            $em->persist($weiboUser);
            $em->flush();

            return $this->redirect($this->generateUrl('weibo_bind', array('openId' => $openId)));
        } else if ($weiboUser->getUser() == null) {
            return $this->redirect($this->generateUrl('weibo_bind', array('openId' => $openId)));
        } else {
            $user = $weiboUser->getUser();
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($request->getClientIp());

            $userTrack = $user->getUserTrack();
            $userTrack->setLastFingerprint(null);
            $userTrack->setCurrentFingerprint(null);
            $userTrack->setSignInCount($userTrack->getSignInCount() + 1);
            $userTrack->setLastSignInAt($userTrack->getCurrentSignInAt());
            $userTrack->setCurrentSignInAt(new \DateTime());
            $userTrack->setLastSignInIp($userTrack->getCurrentSignInIp());
            $userTrack->setCurrentSignInIp($request->getClientIp());
            $userTrack->setOauth('weibo');

            $em->flush();

            $request->getSession()->set('uid', $user->getId());

            $forever = time() + 3600 * 24 * 365 * 10;
            $cookie = new Cookie('uid', $user->getId(), $forever);
            return $this->redirectWithCookie($this->generateUrl('_homepage'), $cookie);
        }
    }

    /**
     * @Route("/bind", name="weibo_bind", methods={"GET", "POST"})
     */
    public function bindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userForm = $this->createForm(new UserProfileType());

        $em = $this->getDoctrine()->getManager();
        $weiboUser = $em->getRepository('WenwenFrontendBundle:WeiboUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        $params = array(
            'openId' => $openId,
            'bind_route' => 'weibo_bind',
            'unbind_route' => 'weibo_unbind',
            'nickname' => $weiboUser->getNickname(),
            'photo' => $weiboUser->getPhoto(),
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

                $weiboUser->setUser($user);
                $em->flush();

                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        $params['loginForm'] = $loginForm->createView();
        return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
    }

    /**
     * @Route("/unbind", name="weibo_unbind", methods={"GET", "POST"})
     */
    public function unbindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userProfile = new UserProfile();
        $userForm = $this->createForm(new UserProfileType(), $userProfile);

        $em = $this->getDoctrine()->getManager();
        $weiboUser = $em->getRepository('WenwenFrontendBundle:WeiboUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        if ($request->getMethod() == 'POST') {
            $userForm->bind($request);
            if ($userForm->isValid()) {
                $user = $weiboUser->getUser();
                if ($user == null) {
                    $user = $userService->autoRegister(
                        $weiboUser,
                        $userProfile,
                        $request->getClientIp(),
                        $request->headers->get('USER_AGENT'),
                        $request->getSession()->get('inviteId'),
                        !$request->cookies->has('uid')
                    );
                    $this->pushBasicProfile($user);// 推送用户基本属性
                }
                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        }

        return $this->render('WenwenFrontendBundle:User:bind.html.twig', array(
            'openId' => $openId,
            'bind_route' => 'weibo_bind',
            'unbind_route' => 'weibo_unbind',
            'nickname' => $weiboUser->getNickname(),
            'photo' => $weiboUser->getPhoto(),
            'provinces' => $provinces,
            'cities' => $cities,
            'loginForm' => $loginForm->createView(),
            'userForm' => $userForm->createView(),
        ));
    }

    private function getAccessToken($code)
    {
        $url = 'https://api.weibo.com/oauth2/access_token';
        $request = $this->get('app.http_client')->post($url);
        $request->addPostFields(array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->container->getParameter('weibo_appid'),
            'client_secret' => $this->container->getParameter('weibo_appkey'),
            'code' => $code,
            'redirect_uri' => $this->container->getParameter('weibo_callback'),
        ));
        $res = $request->send();
//        返回数据
//        {
//            "access_token": "ACCESS_TOKEN",
//            "expires_in": 1234,
//            "remind_in":"798114",
//            "uid":"12341234"
//        }
        $resBody = $res->getBody();
        return json_decode($resBody)->access_token;
    }

    private function getOpenId($token) {
        $url = 'https://api.weibo.com/oauth2/get_token_info';
        $request = $this->get('app.http_client')->post($url);
        $request->setPostField('access_token', $token);
        $res = $request->send();
//        返回数据
//        {
//            "uid": 1073880650,
//            "appkey": 1352222456,
//            "scope": null,
//            "create_at": 1352267591,
//            "expire_in": 157679471
//        }
        $resBody = $res->getBody();
        return json_decode($resBody)->uid;
    }

    private function getUserInfo($token, $openId) {
        $params = array(
            'access_token' => $token,
            'uid' => $openId,
        );
        $url = 'https://api.weibo.com/2/users/show.json?' . http_build_query($params);
        $res = $this->get('app.http_client')->get($url)->send();
        $resBody = $res->getBody();
        return json_decode($resBody);
    }

    private function pushBasicProfile(User $user)
    {
        $args = array(
            '--user_id=' . $user->getId(),
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
    }
}