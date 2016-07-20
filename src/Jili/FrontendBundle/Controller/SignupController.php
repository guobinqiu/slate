<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;
use Jili\ApiBundle\Entity\AdCategory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

use JMS\JobQueueBundle\Entity\Job;

class SignupController extends Controller 
{
    /**
     * @Route("/confirmRegister/register_key/{register_key}", name="_signup_confirm_register",requirements={"_scheme"="https"})
     * @Route("/signup/confirmRegister/register_key/{register_key}", name="_signup_confirm_register_2",requirements={"_scheme"="https"})
     * @Method("GET")
     */
    public function confirmRegisterAction($register_key)
    {
        $this->container->get('logger')->debug(__METHOD__ . ' - START - register_key=' . $register_key);
        $clientIp = $this->getRequest()->getClientIp();
        // 1. Validation
        $passwordToken = $this->validateRegisterKey($register_key);
        if ( !$passwordToken ){
            $this->container->get('logger')->debug(__METHOD__ . ' - passwordToken is not setted - ');
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }

        // 2. Update register and user information
        $user = $this->updateRegisterInformations($passwordToken, $clientIp);
        if ( !$user ){
            $this->container->get('logger')->debug(__METHOD__ . ' - user info update failed - ');
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
        }
        
        // 3. Send register success email to user 
        //$rtn = $this->sendRegisterCompleteEmail($user);
        //Todo error handling

        // 4. Record the campaign tracking infomation of recruiting to log file
        $rtn = $this->recordRecruitingInformation($user);

        // 5. Login this user 
        $rtn = $this->loginUser($user);

        $user_id = $user->getId();

        // 6. Get sop's profiling survey infos
        $sop_profiling_info = $this->getSopProfilingSurveyInfo($user_id);

        $this->container->get('logger')->debug(__METHOD__ . ' - END - ');
        return $this->render('WenwenFrontendBundle:User:regSuccess.html.twig', $sop_profiling_info);
    } 
    
    /**
    * @param string $register_key
    * @return object
    */
    private function validateRegisterKey( $register_key){
        $this->container->get('logger')->debug(__METHOD__ . ' - START - ');
        $em = $this->getDoctrine()->getManager();
        $passwordToken = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByValidatedToken( $register_key );
        $this->container->get('logger')->debug(__METHOD__ . ' - END - ' . isset($passwordToken));
        return $passwordToken;
    }

    /**
    * update 
    *     user.last_login_date => current_time
    *     user.last_login_ip => ip address used by user at this login
    *     user.is_email_confirmed => User::EMAIL_CONFIRMED
    *     user.register_complete_date => current_time
    *     user.points => current points + User::POINT_SIGNUP
    * update
    *     set_password_code.is_available => 0
    * create 
    *     point_history0x.user_id => user.id
    *     point_history0x.point_change_num => User::POINT_SIGNUP
    *     point_history0x.reason => CategoryType::SIGNUP
    * create
    *     task_history0x.user_id => user.id
    *     task_history0x.order_id => 0
    *     task_history0x.ocd_create_date => current_time
    *     task_history0x.category_type => CategoryType::SIGNUP
    *     task_history0x.task_type => TaskType::RENTENTION
    *     task_history0x.task_name => '完成注册'
    *     task_history0x.date => current_time
    *     task_history0x.point => User::POINT_SIGNUP
    *     task_history0x.status => 1
    * @param object $passwordToken
    * @param string $clientIp
    * @return object
    */    
    private function updateRegisterInformations(SetPasswordCode $passwordToken, $clientIp){
        $this->container->get('logger')->debug(__METHOD__ . ' - START - ');
        $em = $this->getDoctrine()->getManager();
        
        $user_id = $passwordToken->getUserId();

        $user = $em->getRepository('JiliApiBundle:User')->findOneById($user_id);
        if( ! $user ) {
            // Can not find user_id in user table.
            // Todo This is a system error which need log or throw exception
            return $user;
        }
        $signupTime = new \DateTime();

        // Update user
        $user->setLastLoginDate($signupTime);
        $user->setLastLoginIp($clientIp);
        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
        $user->setRegisterCompleteDate($signupTime);
        $user->setPoints(intval($user->getPoints() + User::POINT_SIGNUP));
        $user->setLastGetPointsAt();

        $passwordToken->setToUnavailable();

        // Create new object of point_history0x
        $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ( $user_id % 10);
        $pointHistory = new $classPointHistory();
        $pointHistory->setUserId($user_id);
        $pointHistory->setPointChangeNum(User::POINT_SIGNUP);
        $pointHistory->setReason(CategoryType::SIGNUP);

        // Create new object of task_history0x
        $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ( $user_id % 10);
        $taskHistory = new $classTaskHistory();
        $taskHistory->setUserid($user_id);
        $taskHistory->setOrderId(0);
        $taskHistory->setOcdCreatedDate($signupTime);
        $taskHistory->setCategoryType(CategoryType::SIGNUP);
        $taskHistory->setTaskType(TaskType::RENTENTION);
        $taskHistory->setTaskName('完成注册');
        $taskHistory->setDate($signupTime);
        $taskHistory->setPoint(User::POINT_SIGNUP);
        $taskHistory->setStatus(1);

        // transaction
        
        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $em->persist($user);
            $em->persist($passwordToken);
            $em->persist($pointHistory);
            $em->persist($taskHistory);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            //Todo improve the log message to clarify the error status
            $this->get('logger')->error($e->getMessage());
            return NULL;
        }
        $this->container->get('logger')->debug(__METHOD__ . ' - END - ');
        return $user;
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
            '--mailing_id=3254',# 91wenwen-signup
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

    /**
    * @param string $user_id
    * @return array $sop_profiling_info
    */ 
    private function getSopProfilingSurveyInfo($user_id) {
        $this->container->get('logger')->debug(__METHOD__ . ' - START - ');
        $surveyService = $this->get('app.survey_service');
        $env = $this->container->get('kernel')->getEnvironment();
        if (in_array($env, array('dev','test'))) {
            // for dummy mode (won't access sop's server at dev or test mode)
            // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
            $surveyService->setDummy(true);
        }
        $sop_profiling_info = $surveyService->getSopProfilingSurveyInfo($user_id);
        $this->container->get('logger')->debug(__METHOD__ . ' - END - ');
        return $sop_profiling_info;
    }
}

