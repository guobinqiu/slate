<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\ApiBundle\Entity\User;
use JMS\JobQueueBundle\Entity\Job;

class SignupController extends Controller 
{

    /**
     * @Route("/confirmRegister/register_key/{register_key}", name="_signup_confirm_register",requirements={"_scheme"="https"})
     * @Method("GET")
     */
    public function confirmRegisterAction($register_key )
    {
       $em = $this->getDoctrine()->getManager();
       $passwordToken = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByValidatedToken( $register_key );

        if( !$passwordToken  ) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }

       $user = $em->getRepository('JiliApiBundle:User')
           ->findOneById($passwordToken->getUserId());

       // user  not found!
       if( ! $user ) {
           return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
       }

       $user->setLastLoginDate(new \Datetime());
       $user->setLastLoginIp($this->getRequest()->getClientIp());
       $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED );

       $passwordToken->setToUnavailable();

       // transaction
       $em->getConnection()->beginTransaction(); // suspend auto-commit
       try {
           $em->persist($user);
           $em->persist($passwordToken);
           $em->flush();
           $em->getConnection()->commit();

       } catch (Exception $e) {
           $em->getConnection()->rollBack();
           $this->get('logger')->emerg($e->getMessage()  );
       }

       // send register Success eamil 
       $args = array( '--campaign_id=1',# 91wenwen-signup
           '--group_id=83',# signup-completed-recipients
           '--mailing_id=2411',# 91wenwen-signup
           '--email='. $user->getEmail(),
           '--title=',
           '--name='. $user->getNick());
       $job = new Job('webpower-mailer:signup-confirm',$args,  true, '91wenwen_signup');
       $em->persist($job);
       $em->flush($job);

       $this->get('login.listener')->initSession($user);
       // The user was insert when regAction
       $this->get('login.listener')->log($user);
        return new RedirectResponse('_home');
    } 
}

