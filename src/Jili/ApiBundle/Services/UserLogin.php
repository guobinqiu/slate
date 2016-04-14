<?php
namespace Jili\ApiBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\LoginLog;

/**
 *
 **/
class UserLogin
{
    private $em;
    private $task_list;
    private $user_config;
    //#	private $session_points;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param  $request
     */
    public function login(Request $request)
    {
        $request_params = $this->getRequestParams($request);
        return $this->doLogin( $request_params );
    }
    
    /**
     * @param Request $request
     * @return array $request_params
     */
    public function getRequestParams(Request $request){
        $request_params =  array( 
            'email'=>trim($request->request->get('email')),
            'pwd'=>$request->request->get('pwd'),
            'client_ip'=>$request->getClientIp(),
            'method'=>$request->getMethod(),
        );
        return $request_params;
    }

    /**
     * doLogin 
     * 
     * @param mixed $params 
     * @access public
     * @return void
     */
    public function doLogin($params)
    {
        $code = '';
        if ($params['method'] != 'POST') {
            return $code;
        }

        $email = $params['email'];

        if (!$email ) {
            $code = $this->getParameter('login_en_mail');
            return $code;
        }

        if ( ! preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
            $code = $this->getParameter('login_wr_mail');
            return $code;
        }

        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        if (! $user) {
            $code = $this->getParameter('login_wr');
            return $code;
        }

        $id = $user->getId();
        if ($user->getDeleteFlag() == 1 || $user->emailIsConfirmed() == 0) {
            $code = $this->getParameter('login_wr');
            return $code;
        }

        //  checking password 
        $password = $params['pwd'];

        if( $user->isPasswordWenwen() ) {

            // using wenwen password 
            $wenwenLogin = $em->getRepository('JiliApiBundle:UserWenwenLogin')->findOneByUser($user);
            if(! $wenwenLogin || ! $wenwenLogin->getLoginPasswordCryptType() ) {
                return $this->getParameter('login_wr'); 
            }

            // using jili  password 
            if(! $wenwenLogin->isPwdCorrect($password) ) {
                    return $this->getParameter('login_wr'); 
            }

             $em->getRepository('JiliApiBundle:User')
                 ->migrateUserWenwenLogin( $password , $user->getId());
        } else {
            // check jili password 
            if (! $user->isPwdCorrect($password) ) {
                $code = $this->getParameter('login_wr');
                return $code;
            }
        }

        $this->doAfterLogin($user, $params);
        return 'ok';
    }

    /**
     * @param: $user
     */
    public function afterLogin(User $user, Request $request)
    {
        $request_params = array(
            'client_ip'=>$request->getClientIp(),
        );

        return $this->doAfterLogin($user, $request_params);
    }

    public function doAfterLogin(User $user, $params)
    {
        if( $user) {
            $em = $this->em;
            $this->resetTasksSession();

            $this->initSession( $user);
            $user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
            $user->setLastLoginIp($params['client_ip']);
            $em->flush();

            $this->log( $user, $params['client_ip']);
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function updateSession()
    {
        $session = $this->container_->get('session');
        if( $session->has('uid')){
            $em = $this->em;
            $user = $em->getRepository('JiliApiBundle:User')->find($session->get('uid'));
            $this->initSession($user);
            $this->updateInfoSession($user);
            $this->user_config->updateCheckinOpMethod();
        }
    }

    public function updateInfoSession(User $user)
    {
        $session = $this->container_->get('session');
        $icon_path = $user->getIconPath() ;
        if( ! empty($icon_path) ) {
            $session->set('icon_path', $icon_path);
        } else {
            if( $session->has('icon_path')) {
                $session->remove('icon_path');
            }
        }
        $this->updatePoints( $user->getPoints());
        //#todo: update the confirmPoinsts
    }

    public function updatePoints($points)
    {
        $this->container_->get('session')->set('points',$points );
        return $this;
    }

    public function initSession(User  $user)
    {
        $session = $this->container_->get('session');
        $session->set('uid', $user->getId() );
        $session->set('nick', $user->getNick());
        return true;
    }

    /**
     *
     */
    public function resetTasksSession()
    {
        // init the task_list & my_task_list when first login.
        // some session will be kept when logout, but not this.
        $this->task_list->remove(array('alive'));
        #        $this->my_task_list->remove(array('alive'));
    }

    /**
     * update is_newbie in session
     * $user the Entity User Instance
     */
    public function checkNewbie(User  $user)
    {
        $request = $this->container_->get('request');
        // 从wenwen来的用户已经在landingAction登录过，并且registerDate与lastLogDate是一样的。
        $is_newbie = false;
        if($user->getRegisterDate()->getTimestamp() === $user->getLastLoginDate()->getTimestamp() ) {
            if( $user->getIsFromWenwen() === $this->getParameter('init_one')  ) {
                // check the the login log
                $em = $this->em;
                $loginLog = $em->getRepository('JiliApiBundle:LoginLog')->findOneByUserId($user->getId());
                if( ! $loginLog) {
                    $is_newbie = true ;
                }
            } else {
                $is_newbie = true;
            }
        }

        if( $is_newbie === false ) {
            $request->getSession()->set('is_newbie', false);
        } else {
            $request->getSession()->set('is_newbie', true);
            $request->getSession()->set('is_newbie_passed', false);
        }

        return  true;
    }

    public function isNewbie()
    {
        return  $this->container_->get('request')->getSession()->get('is_newbie', false);
    }

    public function log($user, $client_ip = null)
    {
        $em = $this->em;
        if(! $client_ip) {
            $request = $this->container_->get('request');
            $client_ip = $request->getClientIp();
        }

        $loginlog = new LoginLog();
        $loginlog->setUserId($user->getId()  );
        $loginlog->setLoginDate($user->getLastLoginDate() );
        $loginlog->setLoginIp($client_ip);
        $em->persist($loginlog);
        return $em->flush();
    }

    public function getParameter($key)
    {
        return $this->container_->getParameter($key);
    }

    public function setContainer($c)
    {
        $this->container_ = $c;
    }
    /**
     * @param: $uc the user_config service
     */
    public function setUserConfig($uc)
    {
        $this->user_config = $uc;
    }
    /**
     * @param: $tl the task_list service
     */
    public function setTaskList($tl)
    {
        $this->task_list= $tl;
    }

    /**
     * for remember me function.
     * @param: $user = array( 'emaril'=> '', '');
     */
    public function buildToken($user)
    {
        $try = 5;
        $token = '';

        // insert token
        // read uid from session.
        $session = $this->container_->get('session');
        if( $session->has('uid') ) {
            $uid = $session->get('uid');
            if( ! empty( $uid ) ) {
                $em = $this->em;
                // get signned in user
                $entity = $em->getRepository('JiliApiBundle:User')->findOneById($uid);
                if( $entity) {
                    do {
                        $token = $this->generateToken($user);
                        // check the token is unique.
                        $exists = $em->getRepository('JiliApiBundle:User')->findByValidateToken($token);
                        if ( $exists   ) {

                            if( count($exists) == 1  ) {
                                $exist = $exists[0];
                                if(  $exist->getId() == $uid ) {
                                    $entity->setTokenCreatedAt( new \Datetime('now') );
                                    $em->flush();
                                    break;
                                }
                            }

                            if( $try-- > 0 ) {
                                $logger = $this->container_->get('logger');
                                continue;
                            }
                        }
                        $entity->setToken($token);
                        $entity->setTokenCreatedAt( new \Datetime('now') );
                        $em->flush();
                        break;
                    } while ($try-- > 0);
                }
            }
        }

        return $token;
    }

    /**
     * @param: $user = array( 'email'=> '', 'pwd'=>);
     */
    private function generateToken($user)
    {
        // gen token of 32 chars
        $token = implode('|',$user).$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }

    /**
     * find the token from database.
     */
    public function byToken($token)
    {
        if( empty($token) ) {
            return false;
        }
        $em  = $this->em;
        $exists = $em->getRepository('JiliApiBundle:User')->findByValidateToken($token);

        if( $exists && count($exists) === 1 ) {
            $entity = $exists [0];
            return  $entity;
        }
        return false;
    }

}
