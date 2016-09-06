<?php

namespace Jili\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\User;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user")
 */
class PasswordController extends Controller
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

        return $this->render('WenwenFrontendBundle:User:resetPwdEmail.html.twig');
    }

    /**
     * Ajax call
     *
     * @Route("/reset", name="_user_reset", options={"expose"=true}, methods={"GET"})
     */
    public function resetAction(Request $request)
    {
        $email = $request->query->get('email');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return new JsonResponse(array('error' => true, 'message' => '邮件不存在'), 404);
        }

        $resetPasswordToken = md5($user->getId() . $user->getEmail() . $user->getPwd());
        $user->setResetPasswordToken($resetPasswordToken);
        $user->setResetPasswordTokenExpiredAt(new \DateTime('+ 24 hour'));
        $em->flush();

        $this->send_reset_password_email($user, $em);

        return new JsonResponse(array('error' => false, 'message' => '邮件已发送'), 200);
    }

    /**
     * @Route("/resetPass", name="_user_resetPass", methods={"GET", "POST"})
     */
    public function resetPassAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');
        if ($resetPasswordToken == null) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '无效链接'));
        }

        $user = $this->getDoctrine()->getRepository('JiliApiBundle:User')->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '无效链接'));
        }

        if ($user->isResetPasswordTokenExpired()) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '验证码已过期'));
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
                $em = $this->getDoctrine()->getManager();
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

    private function send_reset_password_email(User $user, EntityManager $em)
    {
        $args = array(
            '--subject=91问问-帐号密码重置',
            '--email='.$user->getEmail(),
            '--name='.$user->getNick(),
            '--reset_password_token='.$user->getResetPasswordToken(),
        );
        $job = new Job('mail:reset_password', $args, true, '91wenwen_reset', Job::PRIORITY_HIGH);
        $em->persist($job);
        $em->flush();
    }
}