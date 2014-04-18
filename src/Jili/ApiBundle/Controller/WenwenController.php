<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\setPasswordCode;

/**
 * @Route("/api/91wenwen")
 */
class WenwenController extends Controller {

	/**
	 * @Route("/register", name="_api_91wenwen_register");
	 * @Method({"POST"});
	 */
	public function registerAction() {
		$em = $this->getDoctrine()->getManager();
		$email = $this->get('request')->get('email');
		$signature = $this->get('request')->get('signature');
		$uniqkey = $this->get('request')->get('uniqkey');

		$result = $this->check($email, $signature, $uniqkey);
		if ($result['status'] != 1) {
			$resp = new Response(json_encode($result));
			$resp->headers->set('Content-Type', 'text/plain');
			return $resp;
		}

		//存db
		$user = new User();
		$user->setEmail($email);
		$user->setPoints(0);
		$user->setIsInfoSet(0);
		$user->setRewardMultiple(1);
		$user->setIsFromWenwen($this->container->getParameter('is_from_wenwen_register')); //和91问问同时注册 2
		$user->setUniqkey($uniqkey);
		$em->persist($user);
		$em->flush();
		$str = 'jilifirstregister';
		$code = md5($user->getId() . str_shuffle($str));

		//发送激活邮件
		$wenwen_api_url = $this->container->getParameter('91wenwen_api_url');
		$url = $wenwen_api_url . '/user/setPassFromWenwen/' . $code . '/' . $user->getId();
		$mailLister = $this->get('mail.listener');
		$send_email = $mailLister->sendMailForWenWenRegister($this->get('mailer'), $url,$email);
		if ($send_email) {
			$setPasswordCode = new setPasswordCode();
			$setPasswordCode->setUserId($user->getId());
			$setPasswordCode->setCode($code);
			$setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
			$em->persist($setPasswordCode);
			$em->flush();

			$result['status'] = '1';
			$result['message'] = 'success';
		} else {
			$result['status'] = '0';
			$result['message'] = 'send mail fail';
		}

		$logger = $this->get('logger');
		$logger->info('{WenwenController:registerAction}' . json_encode($result));

		$resp = new Response(json_encode($result));
		$resp->headers->set('Content-Type', 'text/plain');
		return $resp;

	}

	private function check($email, $signature, $uniqkey) {
		$result['status'] = 1;

		//email is null
		if (!$email) {
			$result['status'] = '0';
			$result['message'] = 'missing email';
			return $result;
		}

		//signature is null
		if (!$signature) {
			$result['status'] = '0';
			$result['message'] = 'missing signature';
			return $result;
		}

		//uniqkey is null
		if (!$uniqkey) {
			$result['status'] = '0';
			$result['message'] = 'missing uniqkey';
			return $result;
		}

		//signature error
		if ($this->getToken($email) != $signature) {
			$result['status'] = '0';
			$result['message'] = 'access error ';
			return $result;
		}

		//email valid check
		if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
			$result['status'] = '0';
			$result['message'] = 'email error';
			return $result;
		}

		//email exist check
		$em = $this->getDoctrine()->getManager();
		$is_email = $em->getRepository('JiliApiBundle:User')->getWenwenUser($email);
		if ($is_email) {
			$result['status'] = '2';
			$result['message'] = 'already exist';
			return $result;
		}

		return $result;
	}

	private function getToken($email) {
		$seed = "ADF93768CF";
		$hash = sha1($email . $seed);
		for ($i = 0; $i < 5; $i++) {
			$hash = sha1($hash);
		}
		return $hash;
	}
}