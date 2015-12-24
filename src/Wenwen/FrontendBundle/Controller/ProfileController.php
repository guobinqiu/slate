<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Jili\ApiBundle\Utility\ValidateUtil;

/**
 * @Route("/profile",requirements={"_scheme"="https"})
 */
class ProfileController extends Controller
{

    /**
     * @Route("/index", name="_profile_index", options={"expose"=true})
     * @Template
     */
    public function indexAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_profile_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $csrfProvider = new DefaultCsrfProvider('SECRET');
        $csrf_token = $csrfProvider->generateCsrfToken('profile');
        $request->getSession()->set('csrf_token', $csrf_token);
        $arr['csrf_token'] = $csrf_token;
        return $this->render('WenwenFrontendBundle:Personal:account.html.twig', $arr);
    }

    /**
     * @Route("/changePwd", name="_profile_changepwd", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function changePwdAction()
    {
        $request = $this->get('request');
        $result['status'] = 0;

        if (!$request->getSession()->get('uid')) {
            $result['message'] = 'Need login';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        $curPwd = $request->get('curPwd');
        $pwd = $request->get('pwd');
        $pwdRepeat = $request->get('pwdRepeat');
        $csrf_token = $request->get('csrf_token');

        //check csrf_token
        if (!$csrf_token || ($csrf_token != $request->getSession()->get('csrf_token'))) {
            $result['message'] = 'Access Forbidden';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        //check input
        $id = $request->getSession()->get('uid');
        $error_message = $this->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        if ($error_message) {
            $result['message'] = $error_message;
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        //update user password
        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $user->setPwd($pwd);
            $em->flush();

            $result['status'] = 1;
            $result['message'] = $this->container->getParameter('forget_su_pwd');
        } catch (Exception $e) {
            $logger = $this->get('logger');
            $logger->error('{ProfileController:changePwdAction}' . $e->getMessage() . "user id: " . $id);

            $result['message'] = $this->container->getParameter('update_password_fail');
        }

        $resp = new Response(json_encode($result));
        $resp->headers->set('Content-Type', 'application/json');
        return $resp;
    }

    public function checkPassword($curPwd, $pwd, $pwdRepeat, $id)
    {
        //check empty
        if (!$curPwd) {
            return $this->container->getParameter('change_en_oldpwd');
        }

        //check empty
        if (!$pwd || !$pwdRepeat) {
            return $this->container->getParameter('change_en_newpwd');
        }

        //2次输入的用户密码不相同
        if ($pwd != $pwdRepeat) {
            return $this->container->getParameter('change_unsame_pwd');
        }

        //用户密码为6-20个字符，不能含特殊符号
        if (!ValidateUtil::validatePassword($pwd)) {
            return $this->container->getParameter('change_wr_pwd');
        }

        //check old password
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);

        if ($user->isPasswordWenwen()) {
            // check wenwen password
            $wenwenLogin = $em->getRepository('JiliApiBundle:UserWenwenLogin')->findOneByUser($user);
            if (!$wenwenLogin || !$wenwenLogin->getLoginPasswordCryptType()) {
                return $this->container->getParameter('change_wr_oldpwd');
            }
            if (!$wenwenLogin->isPwdCorrect($curPwd)) {
                return $this->container->getParameter('change_wr_oldpwd');
            }
        } else {
            // check jili password
            if (!$user->isPwdCorrect($curPwd)) {
                return $this->container->getParameter('change_wr_oldpwd');
            }
        }
        return false;
    }
}
