<?php
namespace Jili\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\LoginLog;

/**
 *
 **/
class LoginListener {
	private $em;
	private $task_list;
	private $my_task_list;

	public function __construct(EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * @param  $request
	 */
	public function login(Request $request) {

		$code = '';
		if ($request->getMethod() != 'POST') {
			return $code;
		}

        $email = $request->request->get('email');
		if (!$email ) {
			$code = $this->getParameter('login_en_mail');
			return $code;
		}
		if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
			$code = $this->getParameter('login_wr_mail');
			return $code;
		}

		$em = $this->em;
		$em_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
		if (!$em_email) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		$user = $em_email[0];//->getRepository('JiliApiBundle:User')->find($id);
		$id = $user->getId();
		if ($user->getDeleteFlag() == 1) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

        $password = $request->request->get('pwd');
		if ($user->pw_encode($password) != $user->getPwd()) {
			$code = $this->getParameter('login_wr');
			return $code;
		}
#        $logger = $this->container_ ->get('logger');
#         $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'remember_me','')).
#            var_export($request->request->get('remember_me'), true) );


        $this->initSession($user);
        $this->checkNewbie( $user);
        
		$user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
		$user->setLastLoginIp($request->getClientIp());
		$em->flush();

        $this->log( $user);
		$code = 'ok';
		return $code;
	}
    public function updateInfoSession(User $user ) {
        $session = $this->container_->get('session');


        $icon_path = $user->getIconPath() ;
        if( ! empty($icon_path) ) {
            $session->set('icon_path', $icon_path);
        } else {
            if( $session->has('icon_path')) {
                $session->remove('icon_path');
            }
        }
        $session->set('points', $user->getPoints());
    }
    /**
     *
     */
    public function initSession( User  $user)
    {
        $session = $this->container_->get('session');
        $session->set('uid', $user->getId() );
        $session->set('nick', $user->getNick());

        $this->updateInfoSession($user);
        // init the task_list & my_task_list when first login. 
        // some session will be kept when logout, but not this.
        $this->task_list->remove(array('alive'));
        $this->my_task_list->remove(array('alive'));
    }

    /**
     * update is_newbie in session
     * $user the Entity User Instance
     */
    public function checkNewbie( User  $user ) {
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

    public function isNewbie() {
        return  $this->container_->get('request')->getSession()->get('is_newbie', false);
    }

    public function log($user) {
        $em = $this->em;
        $request = $this->container_->get('request');

        $loginlog = new LoginLog();
        $loginlog->setUserId($user->getId()  );
        $loginlog->setLoginDate($user->getLastLoginDate() );
        $loginlog->setLoginIp($request->getClientIp());
        $em->persist($loginlog);
        return $em->flush();
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }
    /**
     * @param: $tl the task_list service 
     */
    public function setTaskList( $tl) {
        $this->task_list= $tl;
    }
    /**
     * @param: $mtl the my_task_list service 
     */
    public function setMyTaskList( $mtl) {
        $this->my_task_list = $mtl;
    }

}
