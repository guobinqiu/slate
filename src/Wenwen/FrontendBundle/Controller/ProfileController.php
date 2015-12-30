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
use Wenwen\FrontendBundle\Form\ProfileEditType;

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
        return $this->render('WenwenFrontendBundle:Profile:account.html.twig', $arr);
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

    /**
     * @Route("/edit", name="_profile_edit",requirements={"_scheme"="https"})
     */
    public function editAction(Request $request)
    {
        //没有登录
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_profile_edit'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $completed = $request->query->get('completed');
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        $form = $this->createForm(new ProfileEditType(), $user);

        //获取默认值
        $arr = $this->getDefaultValue($user);
        $arr['form'] = $form->createView();
        $arr['completed'] = $completed;
        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', $arr);
    }

    /**
     * @Route("/editCommit", name="_profile_edit_commit",requirements={"_scheme"="https"})
     */
    public function editCommitAction(Request $request)
    {
        //没有登录
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_profile_edit'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $form = $this->createForm(new ProfileEditType());
        $form->bind($request);

        $values = $form->getData();
        $params = $request->request->all();

        $em = $this->getDoctrine()->getManager();
        $user_id = $request->getSession()->get('uid');
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        // user not exist
        if (!$user) {
            // 跳转到网站首页
            return $this->container->getParameter('wenwen_frontend_home_home');
        }

        //check birthday
        $birthday_error = '';
        if (!ValidateUtil::validatePeriod($params['birthday'], date('Y-m-d'))) {
            $birthday_error = $this->container->getParameter('birthday_error');
        }

        // set user value (当页面出错时，需要保留用户已经所选择的属性，其他值跟随form绑定)
        $user->setProvince($params['province']);
        $user->setCity($params['city']);
        $user->setIncome($params['income']);
        $user->setProfession($params['profession']);
        $user->setIndustryCode($params['industry_code']);
        $user->setWorkSectionCode($params['work_section_code']);
        $user->setEducation($params['education']);
        if (isset($params['hobby'])) {
            $user->setHobby(implode(',', $params['hobby']));
        } else {
            $user->setHobby(null);
        }

        //没有错误
        if (empty($birthday_error) && $form->isValid()) {

            // set other user value
            $user->setNick($params['nick']);
            $user->setBirthday($params['birthday']);
            $user->setTel($params['tel']);
            $user->setSex($params['sex']);
            $user->setPersonalDes($params['personalDes']);
            $user->setFavMusic($params['favMusic']);
            $user->setMonthlyWish($params['monthlyWish']);

            // update user info
            $em->persist($user);
            $em->flush();

            $this->get('login.listener')->updateInfoSession($user);

            return $this->redirect($this->generateUrl('_profile_edit', array (
                'completed' => 1
            )));
        }

        //form invalid，有错误
        $arr = $this->getDefaultValue($user);
        $arr['birthday_error'] = $birthday_error;
        $arr['form'] = $form->createView();
        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', $arr);
    }

    /**
     * 获取默认值
     */
    public function getDefaultValue($user)
    {
        $em = $this->getDoctrine()->getManager();

        $data['user'] = $user;

        //省
        $data['province'] = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();

        //收入
        $income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
        unset($income[0]);
        unset($income[1]);
        unset($income[2]);
        unset($income[3]);
        $data['income'] = $income;

        //兴趣爱好
        $data['hobbyList'] = $em->getRepository('JiliApiBundle:HobbyList')->findAll();

        //用户爱好
        if ($user->getHobby()) {
            $data['userProHobby'] = explode(",", $user->getHobby());
        } else {
            $data['userProHobby'] = null;
        }

        //职业
        $data['profession'] = $this->container->getParameter('job_code');

        //行业
        $data['industry_code'] = $this->container->getParameter('industry_code');

        //部门
        $data['work_section_code'] = $this->container->getParameter('work_section_code');

        //教育
        $data['education'] = $this->container->getParameter('graduation_code');

        return $data;
    }

    /**
     * @Route("/upload", name="_profile_upload",requirements={"_scheme"="https"})
     */
    public function uploadAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        $form = $this->createForm(new ProfileEditType(), $user);

        $form->bind($request);
        $path =  $this->container->getParameter('upload_tmp_dir');
        $code = $user->upload($path);
        if($code == $this->container->getParameter('init_one')){
            $code =  $this->container->getParameter('upload_img_type');
        }
        if($code == $this->container->getParameter('init_two')){
            $code =  $this->container->getParameter('upload_img_size');
        }

        $this->get('login.listener')->updateInfoSession($user);
        return new Response(json_encode($code));

    }
}
