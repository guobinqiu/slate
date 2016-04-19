<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;
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
        // 1. Validation
        $passwordToken = $this->validateRegisterKey($register_key);
        if ( !$passwordToken ){
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }

        // 2. Update register and user information
        $user = $this->updateRegisterInformations($passwordToken);
        if ( !$user ){
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }

        // 3. Send register success email to user 
        $rtn = $this->sendRegisterCompleteEmail($user);
        //Todo error handling

        // 4. Record the campaign tracking infomation of recruiting to log file
        $rtn = $this->recordRecruitingInformation($user);

        // 5. Login this user 
        $rtn = $this->loginUser($user);
        
        return $this->render('WenwenFrontendBundle:User:regSuccess.html.twig');
    } 
    
    /**
    * @param string $register_key
    * @return object
    */
    private function validateRegisterKey( $register_key){
        $this->get('logger')->emerg("Start of validateRegisterKey");
        $em = $this->getDoctrine()->getManager();
        $passwordToken = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByValidatedToken( $register_key );
        $this->get('logger')->emerg("End of validateRegisterKey");
        return $passwordToken;
    }

    /**
    * @param object $passwordToken
    * @return object
    */    
    private function updateRegisterInformations(SetPasswordCode $passwordToken){
        $em = $this->getDoctrine()->getManager();
    
        $user = $em->getRepository('JiliApiBundle:User')
            ->findOneById($passwordToken->getUserId());
        if( ! $user ) {
            // Can not find user_id in user table.
            // Todo This is a system error which need log or throw exception
            return $user;
        }
        $datetime = new \DateTime();
        $user->setLastLoginDate($datetime);
        $user->setLastLoginIp($this->getRequest()->getClientIp());
        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED );
        $user->setRegisterCompleteDate($datetime);

        $passwordToken->setToUnavailable();

        // send out register  points, insert point_history
        $points_for_register = \Jili\ApiBundle\Entity\User::POINT_SIGNUP;

        $points_params = array (
            'userid' => $user->getId(),
            'point' => $points_for_register,
            'type' => AdCategory::ID_SINGUP
        );

        //更新task_history表分数
        $task_params = array (
            'userid' => $user->getId(),
            'orderId' => 0,
            'taskType' => 0,
            'categoryType' => AdCategory::ID_SINGUP,//9:完善资料
            'task_name' => '完成注册',
            'point' => $points_for_register,
            'date' => date_create(date('Y-m-d H:i:s')),
            'status' => 1
        );

        $user->setPoints(intval($user->getPoints()+$points_for_register));

        // transaction
        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $this->get('general_api.point_history')->get($points_params);
            $taskLister = $this->get('general_api.task_history');
            $taskLister->init($task_params);
            $em->persist($user);
            $em->persist($passwordToken);
            $em->flush();
            $em->getConnection()->commit();
            return $user;
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            //Todo improve the log message to clarify the error status
            $this->get('logger')->emerg($e->getMessage()  );
            return NULL;
        }
    }
    
    /**
    * @param object $user
    * @return boolean
    */ 
    private function sendRegisterCompleteEmail(User $user){
        $em = $this->getDoctrine()->getManager();
        $args = array( 
            '--campaign_id=1',# 91wenwen-signup
            '--group_id=83',# signup-completed-recipients
            '--mailing_id=2411',# 91wenwen-signup
            '--email='. $user->getEmail(),
            '--title=先生/女士',
            '--name='. $user->getNick());
        $job = new Job('webpower-mailer:signup-confirm',$args,  true, '91wenwen_signup');
        //Todo Should be a try catch here?
        $em->persist($job);
        $em->flush($job);
        return true;
    }
    
    /**
    * @param object $user
    * @return boolean
    */ 
    private function recordRecruitingInformation($user){
        $logger = $this->get('campaign_code.tracking');
        $logger->track( array(
           'md5_sessionid' => md5($this->get('session')->getId()),
           'campaign_code'=> $user->getCampaignCode(),
           'module' => 'JiliFrontendBundle::SignupController', 
           'action' =>'confirmRegisterAction',
           'logged_at' => date('Y-m-d H:i:s P')

       ));
       return true;
    }

    /**
    * @param object $user
    * @return boolean
    */    
    private function loginUser($user){
        $this->get('login.listener')->initSession($user);
        // The user was insert when regAction
        $this->get('login.listener')->log($user);
        $this->get('session')->getFlashBag()->clear();
        return true;
    }
    
}

