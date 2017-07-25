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
use Wenwen\FrontendBundle\Services\AuthService;

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

        $authService = $this->get('app.auth_service');
        $rtn = $authService->sendPasswordResetEmail($user->getEmail());

        if(AuthService::STATUS_SUCCESS == $rtn[AuthService::KEY_STATUS]){
            return new JsonResponse(array(
                'error' => false,
                'message' => '邮件已发送',
            ), 200);
        } else {
            return new JsonResponse(array(
                'error' => true,
                'message' => '邮件不存在',
            ), 404);
        }
    }

    /**
     * @Route("/resetPass", name="_user_resetPass", methods={"GET", "POST"})
     */
    public function resetPassAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        $authService = $this->get('app.auth_service');
        $rtn = $authService->confirmPasswordReset($resetPasswordToken);

        if(AuthService::STATUS_FAILURE == $rtn[AuthService::KEY_STATUS]){
            return new JsonResponse(array(
                'error' => true,
                'message' => 'Invalid request',
            ), 400);
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
                $rtn = $authService->resetPassword($resetPasswordToken, $form->get('password')->getData());
                if(AuthService::STATUS_SUCCESS == $rtn[AuthService::KEY_STATUS]){
                    return $this->redirect($this->generateUrl('_reset_success'));
                }
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

}