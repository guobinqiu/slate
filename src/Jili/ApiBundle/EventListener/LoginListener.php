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

		$em_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
		if (!$em_email) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

        if($em_email[0]->getRegisterDate() == $em_email[0]->getLastLoginDate() ) {
            $request->getSession()->set('is_newbie', true);
            $request->getSession()->set('is_newbie_passed', false);
        } else {
            $request->getSession()->set('is_newbie', false);
        }

		$id = $em_email[0]->getId();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if ($user->getDeleteFlag() == 1) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		if ($user->pw_encode($password) != $user->getPwd()) {
			$code = $this->getParameter('login_wr');
			return $code;
		}

		if ($request->get('remember_me') == '1') {
			setcookie("jili_uid", $id, time() + 3600 * 24 * 365, '/');
			setcookie("jili_nick", $user->getNick(), time() + 3600 * 24 * 365, '/');
		}

        $cur_dt = date_create(date('Y-m-d H:i:s'));

		$request->getSession()->set('uid', $id);
		$request->getSession()->set('nick', $user->getNick());
		$request->getSession()->set('points', $user->getPoints());
		$user->setLastLoginDate($cur_dt);
		$user->setLastLoginIp($request->getClientIp());
		$em->flush();

		$loginlog = new Loginlog();
		$loginlog->setUserId($id);
		$loginlog->setLoginDate($cur_dt);
		$loginlog->setLoginIp($request->getClientIp());
		$em->persist($loginlog);
		$em->flush();

		$code = 'ok';
		return $code;
	}

    public function setNewbie() {
        $request = $this->container_->get('request');
        return   $request->getSession()->set('is_newbie', true);
    }

    public function isNewbie() {

        $request = $this->container_->get('request');
        $is_newbie = $request->getSession()->get('is_newbie', false);
        return $is_newbie;
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

}
