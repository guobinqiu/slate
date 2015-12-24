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
     * @Route("/edit", name="_profile_edit",requirements={"_scheme"="https"})
     */
    public function editAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $this->generateUrl('_profile_edit'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        // user not exist
        if (!$user) {
            // 跳转到网站首页
            return $this->container->getParameter('wenwen_frontend_home_home');
        }

        $form = $this->createForm(new ProfileEditType(), $user);
        $arr['form'] = $form->createView();
        $arr['user'] = $user;

        $arr['province'] = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();

        $income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
        unset($income[0]);
        unset($income[1]);
        unset($income[2]);
        unset($income[3]);
        $arr['income'] = $income;

        $arr['hobbyList'] = $em->getRepository('JiliApiBundle:HobbyList')->findAll();

        if ($user->getHobby()) {
            $arr['userProHobby'] = explode(",", $user->getHobby());
        }

        //职业
        $arr['profession'] = $this->container->getParameter('job_code');
        $arr['industry_code'] = $this->container->getParameter('industry_code');
        $arr['work_section_code'] = $this->container->getParameter('work_section_code');
        $arr['education'] = $this->container->getParameter('graduation_code');

        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', $arr);
    }

    /**
     * @Route("/editCommit", name="_profile_edit_commit",requirements={"_scheme"="https"})
     */
    public function editCommitAction(Request $request)
    {
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
        if (ValidateUtil::validatePeriod('2015-09-01', '2015-09-01')) {
            $birthday_error = $this->container->getParameter('birthday_error');
        }

        if (empty($birthday_error) && $form->isValid()) {
            // set other user value
            $values->setProvince($params['province']);
            $values->setCity($params['city']);
            $values->setIncome($params['income']);
            $values->setProfession($params['profession']);
            $values->setIndustryCode($params['industry_code']);
            $values->setWorkSectionCode($params['work_section_code']);
            $values->setEducation($params['education']);

            // update user info
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('_profile_edit', array (
                'completed' => 1
            )));
        }

        //form invalid
        $arr['birthday_error'] = $birthday_error;
        return $this->render('WenwenFrontendBundle:Profile:profile.html.twig', $arr);
    }
}
