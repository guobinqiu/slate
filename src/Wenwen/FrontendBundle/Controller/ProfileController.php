<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Security\Acl\Exception\Exception;
use Jili\ApiBundle\Validator\Constraints\PasswordRegex;
use Wenwen\FrontendBundle\Form\UserIconType;
use Wenwen\FrontendBundle\Form\UserType;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller
{

    /**
     * @Route("/index", name="_profile_index", options={"expose"=true})
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

        $em = $this->getDoctrine()->getManager();
        $arr['user'] = $em->getRepository('JiliApiBundle:User')->find($request->getSession()->get('uid'));

        return $this->render('WenwenFrontendBundle:Profile:account.html.twig', $arr);
    }

    /**
     * @Route("/changePwd", name="_profile_changepwd", options={"expose"=true}, methods={"POST"})
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
            $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_WENWEN);
            $em->flush();

            $password_crypt_type = $this->container->getParameter('signup.crypt_method');
            $password_salt = $this->container->getParameter('signup.salt');
            $password = \Jili\ApiBundle\Utility\PasswordEncoder::encode($password_crypt_type, $pwd, $password_salt);
            $em->getRepository('JiliApiBundle:UserWenwenLogin')->createOne(array (
                'user_id' => $id,
                'password' => $password,
                'crypt_type' => $password_crypt_type,
                'salt' => $password_salt
            ));

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

        //用户密码为5-100个字符，密码至少包含1位字母和1位数字
        $passwordConstraint = new PasswordRegex();
        $errorList = $this->get('validator')->validateValue($pwd, $passwordConstraint);
        if (count($errorList) > 0) {
            return $this->container->getParameter('change_wr_pwd');
        }

        //check old password
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);

        if ($user && $user->isPasswordWenwen()) {
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
            if ($user && !$user->isPwdCorrect($curPwd)) {
                return $this->container->getParameter('change_wr_oldpwd');
            }
        }

        return false;
    }

    /**
     * @Route("/edit", name="_profile_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        //没有登录
        if (!$request->getSession()->has('uid')) {
            $request->getSession()->set('referer', $this->generateUrl('_profile_edit'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $provinces = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
        $cities = $em->getRepository('JiliApiBundle:CityList')->findAll();

        //建立user和userProfile之间的双向关联
        if ($user->getUserProfile() == null) {
            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $user->setUserProfile($userProfile);
        }

        //一个页面有多个表单
        $uploadForm = $this->createForm(new UserIconType());
        $userType = new UserType();
        $editForm = $this->createForm($userType, $user);

        //由于也支持GET请求，所以先要判断一下是不是POST的
        if ($request->getMethod() == 'POST') {
            //仅对editForm进行处理
            if ($request->request->has($userType->getName())) {
                $editForm->bind($request);
                if ($editForm->isValid()) {
                    $em->flush();//保存user的同时级联保存userProfile
                    //$this->get('login.listener')->updateInfoSession($user);
                    $request->getSession()->getFlashBag()->add('success', '个人资料修改成功!');
                    return $this->redirect($this->generateUrl('_profile_edit'));
                }
            }
        }

        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', array(
            'uploadForm' => $uploadForm->createView(),
            'editForm' => $editForm->createView(),
            'user' => $user,
            'userProfile' => $user->getUserProfile(),
            'provinces' => $provinces,
            'cities' => $cities,
        ));
    }

    /**
     * @Route("/upload", name="_profile_upload", methods={"POST"})
     * @link http://symfony.com/doc/current/controller/upload_file.html
     */
    public function uploadAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        //一个页面有多个表单
        $userIconType = new UserIconType();
        $uploadForm = $this->createForm($userIconType, $user);
        $editForm = $this->createForm(new UserType(), $user);

        //仅对uploadForm进行处理
        if ($request->request->has($userIconType->getName())) {
            $uploadForm->bind($request);
            if ($uploadForm->isValid()) {
                $file = $user->getIcon();
                if ($file != null) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $uploadDir = $this->container->getParameter('avatar_directory');
                    $file->move($uploadDir, $fileName);

                    $webRoot = $this->get('kernel')->getRootDir() . '/../web';
                    $newIconUrl = $webRoot . '/' . $uploadDir . '/' . $fileName;

                    if ($user->getIconPath() != null) {
                        $oldIconUrl = $webRoot . '/' . $user->getIconPath();
                    }

                    //先按指定宽度等比缩放
                    $this->zoomImage($newIconUrl, 512);

                    //再按坐标裁切
                    $x = $uploadForm->get('x')->getData();
                    $y = $uploadForm->get('y')->getData();
                    $w = $uploadForm->get('w')->getData();
                    $h = $uploadForm->get('h')->getData();
                    $this->cropImage($newIconUrl, $x, $y, $w, $h);

                    //把iconPath关联到新上传的文件
                    $newIconPath = $uploadDir . '/' . $fileName;
                    $user->setIconPath($newIconPath);
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    //删除上一次上传的文件
                    if (isset($oldIconUrl) && file_exists($oldIconUrl)) {
                        unlink($oldIconUrl);
                    }

                    //$this->get('login.listener')->updateInfoSession($user);
                    return $this->redirect($this->generateUrl('_profile_edit'));
                }
            }
        }

        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', array(
            'uploadForm' => $uploadForm->createView(),
            'editForm' => $editForm->createView(),
            'user' => $user,
        ));
    }

    /**
     * @Route("/withdraw", name="_profile_withdraw", options={"expose"=true}, methods={"POST"})
     */
    public function withdrawAction(Request $request)
    {
        $result['status'] = '0';
        $result['message'] = '抱歉，系统出错，请稍后再尝试注销';

        if (!$request->getSession()->get('uid')) {
            $result['status'] = '1001';
            $result['message'] = '请先登录';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        $reasons = $request->get('reason', array ());
        $reason_text = implode(',', $reasons);
        $csrf_token = $request->get('csrf_token');
        $email = $request->get('email');
        $password = $request->get('password');

        //check csrf_token
        if (!$csrf_token || ($csrf_token != $request->getSession()->get('csrf_token'))) {
            $result['status'] = '1002';
            $result['message'] = '非法访问，请登陆后从账户设置页面正常注销';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        // check user email
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($request->getSession()->get('uid'));
        if (trim($user->getEmail()) != trim($email)) {
            $result['status'] = '1003';
            $result['message'] = '请输入正确的邮箱地址';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        // check user password
        $check_user_service = $this->container->get('login.listener');
        $invalid_user = $check_user_service->checkPassword($user, $password);
        if ($invalid_user) {
            $result['status'] = '1004';
            $result['message'] = '请输入正确的登录密码';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        //doWithdraw
        $withdraw = $this->get('withdraw_handler');
        $user_id = $request->getSession()->get('uid');

        $return = $withdraw->doWithdraw($user_id, $reason_text);
        if ($return) {
            $logout_service = $this->get('logout_service');
            $logout_service->logout($request);
            $result['status'] = 1;
        } else {
            $result['status'] = '1005';
            $result['message'] = '抱歉，系统忙，请稍后再尝试注销';
            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        $result['status'] = '1000';
        $result['message'] = '注销成功';
        $resp = new Response(json_encode($result));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    /**
     * @Route("/withdrawFinish", name="_profile_withdraw_finish", options={"expose"=true})
     */
    public function withdrawFinishAction(Request $request)
    {
        return $this->render('WenwenFrontendBundle:Profile:withdraw_finish.html.twig');
    }

    private function zoomImage($filename, $w) {

        $src_image = imagecreatefromstring(file_get_contents($filename));

        $src_x = 0;
        $src_y = 0;
        $src_w = imagesx($src_image);
        $src_h = imagesy($src_image);

        $dst_x = 0;
        $dst_y = 0;
        $dst_w = $w;
        $dst_h = $w * $src_h / $src_w;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_image, $filename);

        imagedestroy($src_image);
        imagedestroy($dst_image);
    }

    private function cropImage($filename, $x, $y, $w, $h) {
        $src_image = imagecreatefromstring(file_get_contents($filename));
        $src_x = $x;
        $src_y = $y;
        $src_w = $w;
        $src_h = $h;

        $dst_x = 0;
        $dst_y = 0;
        $dst_w = $src_w;
        $dst_h = $src_h;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);

        imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($dst_image, $filename);

        imagedestroy($src_image);
        imagedestroy($dst_image);
    }
}
