<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\AdCategory;
use JMS\JobQueueBundle\Entity\Job;

class SignupController extends Controller 
{

    /**
     * @Route("/confirmRegister/register_key/{register_key}", name="_signup_confirm_register",requirements={"_scheme"="https"})
     * @Route("/signup/confirmRegister/register_key/{register_key}", name="_signup_confirm_register_2",requirements={"_scheme"="https"})
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

       // send out register  points, insert point_history
       $points_for_register = 10;

       $points_params = array (
           'userid' => $user->getId(),
           'point' => $points_for_register,
           'type' => AdCategory::ID_SINGUP
       );

       $user->setPoints(intval($user->getPoints()+$points_for_register));

       // transaction
       $em->getConnection()->beginTransaction(); // suspend auto-commit
       try {
           $this->get('general_api.point_history')->get($points_params);
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
           '--title=先生/女士',
           '--name='. $user->getNick());
       $job = new Job('webpower-mailer:signup-confirm',$args,  true, '91wenwen_signup');
       $em->persist($job);
       $em->flush($job);

        // campaign logging
       $logger = $this->get('campaign_code.tracking');
       $logger->track( array(
           'md5_sessionid' => md5($this->get('session')->getId()),
           'campaign_code'=> $user->getCampaignCode(),
           'module' => 'JiliFrontendBundle::SignupController', 
           'action' =>'confirmRegisterAction',
           'logged_at' => date('Y-m-d H:i:s P')

       ));


       $this->get('login.listener')->initSession($user);
       // The user was insert when regAction
       $this->get('login.listener')->log($user);
        return $this->render('WenwenFrontendBundle:User:regSuccess.html.twig');
    } 
}

