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
}
