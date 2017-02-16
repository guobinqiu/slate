<?php

namespace Wenwen\FrontendBundle\Controller;

use Doctrine\ORM\EntityManager;
use Wenwen\FrontendBundle\Entity\User;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user")
 */
class PasswordController extends BaseController
{
    /**
     * @Route("/resetPwd", name="_user_resetPwd", methods={"GET"})
     */
    public function resetPwdAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('uid')) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $builder = $this->createFormBuilder();
        $builder->add('email', 'text');
        $builder->add('captcha', 'captcha', array(
            'label' => '验证码',
            'invalid_message' => '验证码无效',
        ));
        $form = $builder->getForm();

        return $this->render('WenwenFrontendBundle:User:resetPwdEmail.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Ajax call
     *
     * @Route("/reset", name="_user_reset", options={"expose"=true}, methods={"GET"})
     */
    public function resetAction(Request $request)
    {
        $formData = $request->query->get('form');
        $email = $formData['email'];
//        $input_captcha = $formData['captcha'];
//        $session_captcha = $request->getSession()->get('gcb_captcha')['phrase'];
//        if ($input_captcha != $session_captcha) {
//            return new JsonResponse(array(
//                'error' => true,
//                'message' => '验证码有误',
//            ), 404);
//        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return new JsonResponse(array(
                'error' => true,
                'message' => '邮件不存在',
            ), 404);
        }

        $user->setResetPasswordToken(md5(uniqid(rand(), true)));
        $user->setResetPasswordTokenExpiredAt(new \DateTime('+ 24 hour'));

        $em->flush();

        $this->send_reset_password_email($user);

        return new JsonResponse(array(
            'error' => false,
            'message' => '邮件已发送',
        ), 200);
    }

    /**
     * @Route("/resetPass", name="_user_resetPass", methods={"GET", "POST"})
     */
    public function resetPassAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');
        if ($resetPasswordToken == null) {
            throw new \Exception('无效链接');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            throw new \Exception('无效链接');
        }

        if ($user->isResetPasswordTokenExpired()) {
            throw new \Exception('验证码已过期');
        }

        $form = $this->createFormBuilder()
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => '两次输入的密码不一致',
                'first_options' => array('label' => '新密码'),
                'second_options' => array('label' => '重复密码'),
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入您的密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                ),
            ))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $user->setPwd($form->get('password')->getData());
                $user->setResetPasswordToken(null);
                $user->setResetPasswordTokenExpiredAt(null);
                $em->flush();

                return $this->redirect($this->generateUrl('_reset_success'));
            }
        }

        return $this->render('WenwenFrontendBundle:User:resetPwd.html.twig', array(
            'form' => $form->createView(),
            'reset_password_token' => $resetPasswordToken,
        ));
    }

    /**
     * @Route("/reset_success", name="_reset_success", methods={"GET"})
     */
    public function resetSuccess()
    {
        return $this->render('WenwenFrontendBundle:User:resetSuccess.html.twig');
    }

    private function send_reset_password_email(User $user)
    {
        $args = array(
            '--subject=91问问-帐号密码重置',
            '--email='.$user->getEmail(),
            '--name='.$user->getNick(),
            '--reset_password_token='.$user->getResetPasswordToken(),
        );
        $job = new Job('mail:reset_password', $args, true, '91wenwen_reset', Job::PRIORITY_HIGH);
        $job->setMaxRetries(3);
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
    }
}