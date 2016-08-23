<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Jili\ApiBundle\Entity\WeiBoUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/auth/weibo")
 */
class WeiBoLoginController extends Controller
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

        if ($code == null) {
            $this->get('logger')->error('Weibo - 用户取消了授权');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        if ($request->query->get('state') != $request->getSession()->get('state')) {
            $this->get('logger')->error('Weibo - The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $token = $this->getAccessToken($code);
        $openId = $this->getOpenId($token);
        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $weiboUser = $em->getRepository('JiliApiBundle:WeiboUser')->findOneBy(array('openId' => $openId));
        $currentTime = new \DateTime();

        if ($weiboUser == null) {
            $em->getConnection()->beginTransaction();
            try {
                $user = new User();
                $user->setNick($userInfo->screen_name);
                $user->setPoints(User::POINT_SIGNUP);
                $user->setIconPath($userInfo->profile_image_url);
                $user->setRegisterDate($currentTime);
                $user->setRegisterCompleteDate($currentTime);
                $user->setLastLoginDate($currentTime);
                $user->setLastLoginIp($request->getClientIp());
                $user->setCreatedRemoteAddr($request->getClientIp());
                $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));
                $em->persist($user);

                $weiboUser = new WeiboUser();
                $weiboUser->setOpenId($openId);
                $weiboUser->setUser($user);
                $em->persist($weiboUser);

                $userProfile = new UserProfile();
                $userProfile->setSex($userInfo->gender == 'f' ? 2 : 1);
                $userProfile->setUser($user);
                $em->persist($userProfile);

                $em->flush();
                $em->getConnection()->commit();
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }
        } else {
            $user = $weiboUser->getUser();
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
}