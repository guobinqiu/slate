<?php

namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Jili\ApiBundle\Entity\WeiBoUser;
use Jili\FrontendBundle\Form\Type\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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
        $weiboUser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneBy(array('openId' => $openId));

        if ($weiboUser == null) {
            $weiboUser = new WeiboUser();
            $weiboUser->setOpenId($openId);
            $weiboUser->setNickname($userInfo->screen_name);
            $weiboUser->setPhoto($userInfo->profile_image_url);
            $weiboUser->setGender($userInfo->gender == 'f' ? 2 : 1);
            $em->persist($weiboUser);
            $em->flush();

            return $this->redirect($this->generateUrl('weibo_bind', array('openId' => $openId)));
        }

        $user = $weiboUser->getUser();
        $user->setLastLoginDate(new \DateTime());
        $user->setLastLoginIp($request->getClientIp());
        $em->flush();

        $request->getSession()->set('uid', $user->getId());
        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * @Route("/bind", name="weibo_bind", methods={"GET", "POST"})
     */
    public function bindAction(Request $request)
    {
        $form = $this->createForm(new LoginType());

        $openId = $request->query->get('openId');
        $em = $this->getDoctrine()->getManager();
        $weiboUser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneBy(array('openId' => $openId));

        $params = array(
            'openId' => $openId,
            'bind_route' => 'weibo_bind',
            'unbind_route' => 'weibo_unbind',
            'nickname' => $weiboUser->getNickname(),
            'photo' => $weiboUser->getPhoto(),
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

                $weiboUser->setUser($user);
                $em->flush();

                $request->getSession()->set('uid', $user->getId());
                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        $params['form'] = $form->createView();
        return $this->render('WenwenFrontendBundle:User:bind.html.twig', $params);
    }

    /**
     * @Route("/unbind", name="weibo_unbind", methods={"GET", "POST"})
     */
    public function unbindAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $openId = $request->query->get('openId');
        $weiboUser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneBy(array('openId' => $openId));

        $currentTime = new \DateTime();
        $em->getConnection()->beginTransaction();
        try {
            $user = new User();
            $user->setNick($weiboUser->getNickname());
            $user->setPoints(User::POINT_SIGNUP);
            $user->setIconPath($weiboUser->getPhoto());
            $user->setRegisterDate($currentTime);
            $user->setRegisterCompleteDate($currentTime);
            $user->setLastLoginDate($currentTime);
            $user->setLastLoginIp($request->getClientIp());
            $user->setCreatedRemoteAddr($request->getClientIp());
            $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));
            $em->persist($user);

            $userProfile = new UserProfile();
            $userProfile->setSex($weiboUser->getGender());
            $userProfile->setUser($user);
            $em->persist($userProfile);

            $weiboUser->setUser($user);

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