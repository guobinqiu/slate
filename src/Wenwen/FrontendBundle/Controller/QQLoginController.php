<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Wenwen\FrontendBundle\Entity\QQUser;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Form\LoginType;
use Wenwen\FrontendBundle\Form\UserProfileType;

/**
 * @Route("/auth/qq")
 */
class QQLoginController extends BaseController
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

        if (empty($code)) {
            $this->get('logger')->info('QQ - 用户取消了授权');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->info('QQ - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $token = $this->getAccessToken($code);
        $openId = $this->getOpenId($token);
        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $qqUser = $em->getRepository('WenwenFrontendBundle:QQUser')->findOneBy(array('openId' => $openId));

        if ($qqUser == null) {
            $qqUser = new QQUser();
            $qqUser->setOpenId($openId);
            $qqUser->setNickname($userInfo->nickname);
            $qqUser->setPhoto($userInfo->figureurl_qq_1);
            $qqUser->setGender($userInfo->gender == '女' ? 2 : 1);
            $em->persist($qqUser);
            $em->flush();

            return $this->redirect($this->generateUrl('qq_bind', array('openId' => $openId)));
        } else if ($qqUser->getUser() == null) {
            return $this->redirect($this->generateUrl('qq_bind', array('openId' => $openId)));
        } else {
            $user = $qqUser->getUser();
            $user->setLastLoginDate(new \DateTime());
            $user->setLastLoginIp($request->getClientIp());

            $userTrack = $user->getUserTrack();
            if ($userTrack) {
                //$userTrack->setLastFingerprint(null);
                //$userTrack->setCurrentFingerprint(null);
                $userTrack->setSignInCount($userTrack->getSignInCount() + 1);
                $userTrack->setLastSignInAt($userTrack->getCurrentSignInAt());
                $userTrack->setCurrentSignInAt(new \DateTime());
                $userTrack->setLastSignInIp($userTrack->getCurrentSignInIp());
                $userTrack->setCurrentSignInIp($request->getClientIp());
                $userTrack->setOauth('qq');
            } else {
                $userTrack = new UserTrack();
                //$userTrack->setLastFingerprint(null);
                //$userTrack->setCurrentFingerprint(null);
                $userTrack->setSignInCount(1);
                $userTrack->setLastSignInAt(null);
                $userTrack->setCurrentSignInAt(new \DateTime());
                $userTrack->setLastSignInIp(null);
                $userTrack->setCurrentSignInIp($request->getClientIp());
                $userTrack->setOauth('qq');

                $userTrack->setUser($user);
                $user->setUserTrack($userTrack);
                $em->persist($user);
            }

            $em->flush();

            $request->getSession()->set('uid', $user->getId());

            $forever = time() + 3600 * 24 * 365 * 10;
            $cookie = new Cookie('uid', $user->getId(), $forever);
            return $this->redirectWithCookie($this->generateUrl('_homepage'), $cookie);
        }
    }

    /**
     * @Route("/bind", name="qq_bind", methods={"GET", "POST"})
     */
    public function bindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userForm = $this->createForm(new UserProfileType());

        $em = $this->getDoctrine()->getManager();
        $qqUser = $em->getRepository('WenwenFrontendBundle:QQUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        $params = array(
            'openId' => $openId,
            'bind_route' => 'qq_bind',
            'unbind_route' => 'qq_unbind',
            'nickname' => $qqUser->getNickname(),
            'photo' => $qqUser->getPhoto(),
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

                $qqUser->setUser($user);
                $em->flush();

                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        $params['loginForm'] = $loginForm->createView();
        return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
    }

    /**
     * @Route("/unbind", name="qq_unbind", methods={"GET", "POST"})
     */
    public function unbindAction(Request $request)
    {
        $openId = $request->query->get('openId');

        $loginForm = $this->createForm(new LoginType());
        $userProfile = new UserProfile();
        $userForm = $this->createForm(new UserProfileType(), $userProfile);

        $em = $this->getDoctrine()->getManager();
        $qqUser = $em->getRepository('WenwenFrontendBundle:QQUser')->findOneBy(array('openId' => $openId));

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        if ($request->getMethod() == 'POST') {
            $userForm->bind($request);
            if ($userForm->isValid()) {
                $user = $qqUser->getUser();
                if ($user == null) {
                    $allowRewardInviter = false;
                    $fingerprint = $userForm->get('fingerprint')->getData();
                    if (!$request->cookies->has('uid')) {
                        $userTrack = $em->getRepository('WenwenFrontendBundle:UserTrack')->findOneBy(array('currentFingerprint' => $fingerprint));
                        if ($userTrack == null) {
                            $allowRewardInviter = true;
                        }
                    }

                    $user = $userService->autoRegister(
                        $qqUser,
                        $userProfile,
                        $request->getClientIp(),
                        $request->headers->get('USER_AGENT'),
                        $request->getSession()->get('inviteId'),
                        $allowRewardInviter
                    );

                    $userTrack = new UserTrack();
                    $userTrack->setLastFingerprint(null);
                    $userTrack->setCurrentFingerprint($fingerprint);
                    $userTrack->setSignInCount(1);
                    $userTrack->setLastSignInAt(null);
                    $userTrack->setCurrentSignInAt(new \DateTime());
                    $userTrack->setLastSignInIp(null);
                    $userTrack->setCurrentSignInIp($request->getClientIp());
                    $userTrack->setOauth('qq');
                    $userTrack->setRegisterRoute($this->getRegisterRouteFromSession());

                    $userTrack->setUser($user);
                    $user->setUserTrack($userTrack);

                    $em->persist($user);
                    $em->flush();

                    $this->pushBasicProfile($user);// 推送用户基本属性
                }
                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        }

        return $this->render('WenwenFrontendBundle:User:bind.html.twig', array(
            'openId' => $openId,
            'bind_route' => 'qq_bind',
            'unbind_route' => 'qq_unbind',
            'nickname' => $qqUser->getNickname(),
            'photo' => $qqUser->getPhoto(),
            'provinces' => $provinces,
            'cities' => $cities,
            'loginForm' => $loginForm->createView(),
            'userForm' => $userForm->createView(),
        ));
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