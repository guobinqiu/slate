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
		$token = $this->get('request')->get('secret_token');

		$email = "";
		$signature = "";
		$uniqkey = "";
		$params = json_decode(base64_decode(strtr($token, '-_', '+/')));
		if ($params) {
			$email = $params->email;
			$signature = $params->signature;
			if (isset ($params->uniqkey)) {
				$uniqkey = $params->uniqkey;
			}
		}

		$result = $this->check($email, $signature);
		if ($result['status'] != 1) {
			$resp = new Response(json_encode($result));
			$resp->headers->set('Content-Type', 'text/plain');
			return $resp;
		}

		//存db
		$user = new User();
		//$user->setNick();
		$user->setEmail($email);
		$user->setPoints(0);
		$user->setIsInfoSet(0);
		$user->setRewardMultiple(1);
		$user->setIsFromWenwen(2); //和91问问同时注册
		if ($uniqkey) {
			$user->setUniqkey($uniqkey);
		}
		$em->persist($user);
		$em->flush();
		$str = 'jilifirstregister';
		$code = md5($user->getId() . str_shuffle($str));

		//TODO  user/forgetPass  建立新的页面后，需要修改
		$wenwen_api_url = $this->container->getParameter('91wenwen_api_url');
		$url = $wenwen_api_url . '/user/forgetPass/' . $code . '/' . $user->getId();

		//发送激活邮件
		if ($this->sendMail($url, $user->getEmail())) {
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

	private function check($email, $signature) {
		$result['status'] = 1;
		if (!$email || !$signature) {
			$result['status'] = '0';
			$result['message'] = 'secret token error';
			return $result;
		}

		if ($this->getToken($email) != $signature) {
			$result['status'] = '0';
			$result['message'] = 'secret token error';
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

	private function sendMail($url, $email) {
		$message = \ Swift_Message :: newInstance()->setSubject('积粒网-注册激活邮件-从91问问网站注册')->setFrom(array (
			'account@91jili.com' => '积粒网'
		))->setTo($email)->setBody('<html>' .
		' <head></head>' .
		' <body>' .
		'亲爱的' . $email . '<br/>' .
		'<br/>' .
		'  感谢您注册91问问网站的同时注册“积粒网”！<br/>请点击<a href=' . $url . ' target="_blank">这里</a>，立即激活您的帐户！<br/><br/>' .
		'  积粒网，一站式积分媒体！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
		' </body>' .
		'</html>', 'text/html');
		$flag = $this->get('mailer')->send($message);
		if ($flag === 1) {
			return true;
		} else {
			return false;
		}
	}

}