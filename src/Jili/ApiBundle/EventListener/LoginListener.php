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

	public function __construct(EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 *
	 * @param  $email
	 * @param  $password
	 *
	 */
	public function login(Request $request, $email, $password) {
		$em = $this->em;
		$code = '';
		if ($request->getMethod() != 'POST') {
			return $code;
		} 

		if (!$email) {
			$code = $this->getParameter('login_en_mail');
			return $code;
		}
		if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
			$code = $this->getParameter('login_wr_mail');
			return $code;
		}

		$user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
		if (!$user) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		// $id = $em_email[0]->getId();
		// $user = $em->getRepository('JiliApiBundle:User')->find($id);
        // $user= $em_email[0];
        $this->checkNewbie($user);
		if ($user->getDeleteFlag() == 1) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		if ($user->pw_encode($password) != $user->getPwd()) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		if ($request->get('remember_me') == '1') {
			setcookie("jili_uid", $user->getId(), time() + 3600 * 24 * 365, '/');
			setcookie("jili_nick", $user->getNick(), time() + 3600 * 24 * 365, '/');
		}

        $cur_dt = date_create(date('Y-m-d H:i:s'));

		$request->getSession()->set('uid', $user->getId() );
		$request->getSession()->set('nick', $user->getNick());
		$request->getSession()->set('points', $user->getPoints());

		$user->setLastLoginDate($cur_dt);
		$user->setLastLoginIp($request->getClientIp());
		$em->flush();

		$loginlog = new Loginlog();
		$loginlog->setUserId($user->getId() );
		$loginlog->setLoginDate($cur_dt);
		$loginlog->setLoginIp($request->getClientIp());
		$em->persist($loginlog);
		$em->flush();

		$code = 'ok';
		return $code;
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

        return   ;
    }

    public function isNewbie() {
        return  $this->container_->get('request')->getSession()->get('is_newbie', false);
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

}
