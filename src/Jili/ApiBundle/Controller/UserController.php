<?php
namespace Jili\ApiBundle\Controller;
use Gregwar\CaptchaBundle\GregwarCaptchaBundle;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Jili\ApiBundle\Form\FirstRegType;
use Jili\ApiBundle\Form\forgetPassType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Form\CaptchaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\setPasswordCode;
use Gregwar\Captcha\CaptchaBuilder;

class UserController extends Controller
{
	/**
	 * @Route("/checkFlag/{id}", name="_user_checkFlag")
	 */
	public function checkFlagAction($id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		return new Response($user->getIsInfoSet());
// 		exit;
	}
	
	/**
	 * @Route("/checkPwd", name="_user_checkPwd")
	 */
	public function checkPwdAction(){
		$request = $this->get('request');
		$pwd = $request->query->get('pwd');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
	    if($user->pw_encode($pwd) == $user->getPwd())
			$code = $this->container->getParameter('init');
		else
			$code = $this->container->getParameter('init_one');
		return new Response($code);
	}
	
	
	/**
	 * @Route("/updatePwd", name="_user_updatePwd")
	 */
	public function updatePwdAction(){
		return $this->render('JiliApiBundle:User:changePwd.html.twig');
	}
	
	/**
	 * @Route("/changePwd", name="_user_changePwd")
	 */
	public function changePwdAction(){
		$arr['codeflag'] = $this->container->getParameter('init');
		$request = $this->get('request');
		$pwd = $request->request->get('pwd');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if ($request->getMethod() == 'POST') {
    		$user->setPwd($pwd);
    		$em->flush();
    		$arr['codeflag'] = $this->container->getParameter('init_one');
		}
		return $this->render('JiliApiBundle:User:changePwd.html.twig',$arr);
	}
	
	
	/**
	 * @Route("/info", name="_user_info")
	 */
	public function infoAction()
	{
		$code = '';
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$form  = $this->createForm(new RegType(), $user);
		$adtaste = $em->getRepository('JiliApiBundle:AdwOrder');
		$option = array('daytype'=>1,'offset'=>0,'limit'=>10);
		$adtaste = $adtaste->getUseradtaste($id,$option);
		$adtasteNum = count($adtaste);
		$exchange = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $exchange->getUserExchange($id,$option);
		
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$em = $this->getDoctrine()->getManager();
// 			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			$form  = $this->createForm(new RegType(), $user);
			$form->bind($request);
			$path =  $this->container->getParameter('upload_tmp_dir');
			$code = $user->upload($path);
			if(!$code)
                $em->flush();
		}
		return $this->render('JiliApiBundle:User:info.html.twig',array( 
				'form' => $form->createView(),
				'form_upload' =>$form->createView(),
				'user' => $user,
				'adtaste' => $adtaste,
				'exchange' => $exchange,
				'code' => $code,
				'adtasteNum'=>$adtasteNum,
				));
	}
	
	/**
	 * @Route("/upload", name="_user_upload")
	 */
	public function uploadAction(){
	

		return $this->redirect($this->generateUrl('_user_info',array('code'=>$code)));
	}
	
	/**
	 * @Route("/update/{id}", name="_user_update")
	 */
	public function updateAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
// 		$form  = $this->createForm(new RegType(), $user);
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
	    	$user->setNick($request->request->get('nick'));
	    	$user->setSex($request->request->get('sex'));
	    	$user->setBirthday($request->request->get('birthday'));
	    	$user->setTel($request->request->get('tel'));
	    	$user->setCity($request->request->get('city'));
	    	$user->setEducation($request->request->get('education'));
	    	$user->setProfession($request->request->get('profession'));
	    	$user->setHobby($request->request->get('hobby'));
	    	$user->setPersonalDes($request->request->get('personalDes'));
	    	$user->setFlag($this->container->getParameter('init_one'));
	    	$em->persist($user);
	    	$em->flush();
		}
		return $this->render('JiliApiBundle:User:update.html.twig',array('user' => $user));
	}
	
	/**
	 * @Route("/loginOut", name="_user_loginOut")
	 */
	public function loginOutAction(){
        $request = $this->get('request');
        $cookies = $request->cookies;
        if ($cookies->has('jili_uid'))
        {
        	$response = new Response();
        	$response->headers->clearCookie('jili_uid','/');
        	$response->headers->clearCookie('jili_nick','/');
        	$response->send();
        }
		$this->get('request')->getSession()->remove('uid');
		$this->get('request')->getSession()->remove('nick');
		
		return $this->redirect($this->generateUrl('_default_index'));
	}
	
	/**
	 * @Route("/resetPwd", name="_user_resetPwd")
	 */
	public function resetPwdAction(){
		return $this->render('JiliApiBundle:User:resetPwd.html.twig');
		
	}
	
	/**
	 * @Route("/pwdCheck", name="_user_pwdCheck")
	 */
	public function pwdCheckAction(){
		$request = $this->get('request');
		$email = $request->query->get('email');
		$pwd = $request->query->get('pwd');
		$em_email = $this->getDoctrine()
		->getRepository('JiliApiBundle:User')
		->findByEmail($email);
		if(!$em_email){
			$code = $this->container->getParameter('init_one');
		}else{
			$id = $em_email[0]->getId();
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			if($user->pw_encode($pwd) != $user->getPwd()){
				$code = $this->container->getParameter('init_one');
			}else{
				$code = $this->container->getParameter('init');
			}
		}
		return new Response($code);
	}
	
	

	/**
	 * @Route("/login", name="_user_login")
	 */
	public function loginAction(){
		$code = $this->container->getParameter('init');
		$request = $this->get('request');
		$email = $request->request->get('email');
		$pwd = $request->request->get('pwd');
		if ($request->getMethod() == 'POST'){
			if($email){
				if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$email)){
					$code = $this->container->getParameter('init_two');
				}else{
					$em_email = $this->getDoctrine()
					->getRepository('JiliApiBundle:User')
					->findByEmail($email);
					if(!$em_email){
						$code = $this->container->getParameter('init_one');
						// 		    		echo 'email is unexist!';
					}else{
						$id = $em_email[0]->getId();
						$em = $this->getDoctrine()->getEntityManager();
						$user = $em->getRepository('JiliApiBundle:User')->find($id);
						if($user->pw_encode($pwd) != $user->getPwd()){
							// 		    			echo 'pwd is error!';
							$code = $this->container->getParameter('init_one');
						}else{
							if($request->request->get('remember_me')=='1'){
								$response = new Response();
								$response->headers->setCookie(new Cookie('jili_uid', $id,(time() + 3600 * 24 * 365), '/'));
								$response->headers->setCookie(new Cookie('jili_nick', $user->getNick(),(time() + 3600 * 24 * 365), '/'));
// 								$response->send();
// 								$request = $this->get('request');
// 								$cookies = $request->cookies;
// 								if ($cookies->has('uid'))
// 								{
// 									var_dump($cookies->get('uid'));
// 								}
							}
// 							$session = new Session();
// 							$session->start();
// 							$session->set('uid', $id);
// 							$session->set('nick', $user->getNick());
							$this->get('request')->getSession()->set('uid',$id);
							$this->get('request')->getSession()->set('nick',$user->getNick());
							
							$user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
							$user->setLastLoginIp($this->get('request')->getClientIp());
							$em->flush();
							$em = $this->getDoctrine()->getManager();
							$loginlog = new Loginlog();
							$loginlog->setUserId($id);
							$loginlog->setLoginDate(date_create(date('Y-m-d H:i:s')));
							$loginlog->setLoginIp($this->get('request')->getClientIp());
							$em->persist($loginlog);
							$em->flush();
							return $this->redirect($this->generateUrl('_default_index'));
						}
					}
			    }
			}else{
			    $code = $this->container->getParameter('init_three');
		    }
			
	    }
		return $this->render('JiliApiBundle:User:login.html.twig',array('code'=>$code));
	}
	
	/**
	 * @Route("/checkReg/{id}", name="_user_checkReg")
	 */
	public function checkRegAction($id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['gotoEmial'] = $user->gotomail($user->getEmail());
		return $this->render('JiliApiBundle:User:checkReg.html.twig',$arr);
	}
	
	/**
	 * @Route("/checkCaptcha", name="_user_checkCaptcha")
	 */
	public function checkCaptchaAction(){
		$request = $this->get('request');
		if($this->get('request')->getSession()->get('phrase') != $request->query->get('captcha'))
			$code = $this->container->getParameter('init_one');
		else{
			$this->get('request')->getSession()->remove('phrase');
			$code = $this->container->getParameter('init');
		}
		return new Response($code);
	}
	
	/**
	 * @Route("/checkEmail", name="_user_checkEmail")
	 */
	public function checkEmailAction(){
		$request = $this->get('request');
		$email = $request->query->get('email');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
	    if(empty($user))
	    	$code = $this->container->getParameter('init_one');
	    else 
	    	$code = $this->container->getParameter('init');
		return new Response($code);
	}
	
	/**
	 * @Route("/checkNick", name="_user_checkNick")
	 */
	public function checkNickAction(){
		$request = $this->get('request');
		$nick = $request->query->get('nick');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
		if(empty($user))
			$code = $this->container->getParameter('init_one');
		else
			$code = $this->container->getParameter('init');
		return new Response($code);
	}
	
	/**
	 * @Route("/reset", name="_user_reset")
	 */
	public function resetAction(){
		$code = $this->container->getParameter('init');
		$request = $this->get('request');
		$email = $request->query->get('email');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
		if(empty($user)){
			$code = $this->container->getParameter('init');
		}else{
			$nick = $user[0]->getNick();
			$id = $user[0]->getId();
			$passCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findByUserId($id);
			$url = $this->generateUrl('_user_resetPass',array('code'=>$passCode[0]->getCode(),'id'=>$id),true);
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			if($this->sendMail_reset($url, $email,$nick)){
				$passCode[0]->setIsAvailable($this->container->getParameter('init_one'));
				$passCode[0]->setCreateTime(date_create(date('Y-m-d H:i:s')));
				$em->flush();
				$code = $this->container->getParameter('init_one');
			}
		}
		return new Response($code);
	}
	
	
	/**
	 * @Route("/resetPass/{code}/{id}", name="_user_resetPass")
	 */
	public function resetPassAction($code,$id){
		$arr['codeflag'] = $this->container->getParameter('init');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findOneByUserId($id);
		if($setPasswordCode->getIsAvailable()==0){
			return $this->render('JiliApiBundle::error.html.twig');
		}
		$arr['pwdcode'] = $setPasswordCode;
		$time = $setPasswordCode->getCreateTime();
		if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24){
			return $this->render('JiliApiBundle::error.html.twig');
		}else{
			if($setPasswordCode->getCode() == $code){
				$request = $this->get('request');
				if ($request->getMethod() == 'POST'){
						if($request->request->get('pwd') == $request->request->get('newPwd')){
							$this->get('request')->getSession()->set('uid',$id);
							$this->get('request')->getSession()->set('nick',$user->getNick());
							$user->setPwd($request->request->get('pwd'));
							$setPasswordCode->setIsAvailable($this->container->getParameter('init'));
							$em->persist($user);
							$em->persist($setPasswordCode);
							$em->flush();
							$arr['codeflag'] = $this->container->getParameter('init_one');
						}else{
							echo 'check pwd same';
						}
				}
				return $this->render('JiliApiBundle:User:resetPass.html.twig',$arr);
			}
		}
	}
	
	
	/**
	 * @Route("/reg", name="_user_reg")
	 */
	public function regAction(){
		$user = new User();
		$error = '';
		$form = $this->createForm(new CaptchaType(), array());
		$request = $this->get('request');
		if ($request->getMethod() == 'POST'){
			// 			$form->bind($request);
// 			    if($this->get('request')->getSession()->get('phrase') != $request->request->get('captcha')){
// 			    	$this->get('request')->getSession()->remove('phrase');
// 			    	$error = 'captcha is error!';
// 			    }else{
			    	$this->get('request')->getSession()->remove('phrase');
			    	$em = $this->getDoctrine()->getManager();
			    	$user->setNick($request->request->get('nick'));
			    	$user->setEmail($request->request->get('email'));
			    	$user->setPoints($this->container->getParameter('init'));
			    	$user->setIsInfoSet($this->container->getParameter('init'));
			    	$em->persist($user);
			    	$em->flush();
			    	$str = 'jilifirstregister';
			    	$code = md5($user->getId().str_shuffle($str));
			    	$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user->getId()),true);
			    	if($this->sendMail($url, $user->getEmail(),$user->getNick())){
			    		$setPasswordCode = new setPasswordCode();
			    		$setPasswordCode->setUserId($user->getId());
			    		$setPasswordCode->setCode($code);
			    		$setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
			    		$em->persist($setPasswordCode);
			    		$em->flush();
			    		// 					echo 'success';
			    		return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user->getId()),true));
			    	}
			    	
// 			    }
		}
		return $this->render('JiliApiBundle:User:reg.html.twig',array(
				'form' => $form->createView(),
				'error'=>$error
				));
	}
	
	/**
	 * @Route("/agreement", name="_user_agreement")
	 */
	public function agreementAction(){
		return $this->render('JiliApiBundle:User:agreement.html.twig');
	}

	/**
	 * @Route("/captcha", name="_user_captcha")
	 */
	public function captchaAction(){
	    $builder = new CaptchaBuilder;
	    $builder->build();
	    header('Content-type: image/jpeg');
	    $builder->output();
	    $session = new Session();
	    $session->start();
	    $session->set('phrase', $builder->getPhrase());
	    exit;
	}
	
	/**
	 * @Route("/exchange/{type}", name="_user_exchange")
	 */
	public function exchangeAction($type){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:PointsExchange');
		$option = array('daytype' => $type ,'offset'=>'','limit'=>'');
		$exchange = $repository->getUserExchange($id,$option);
		$arr['exchange'] = $exchange;
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$paginator = $this->get('knp_paginator');
		$arr['pagination'] = $paginator
		        ->paginate($exchange,
				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		return $this->render('JiliApiBundle:User:exchange.html.twig',$arr);
	}
	
	/**
	 * @Route("/adtaste/{type}", name="_user_adtaste")
	 */
	public function adtasteAction($type){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:AdwOrder');
		$option = array('daytype' => $type ,'offset'=>'','limit'=>'');
		$adtaste = $repository->getUseradtaste($id,$option);
		$arr['adtaste'] = $adtaste;
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$paginator = $this->get('knp_paginator');
		$arr['pagination'] = $paginator
		->paginate($adtaste,
				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		return $this->render('JiliApiBundle:User:adtaste.html.twig',$arr);
	}
	
	/**
	 * @Route("/regSuccess", name="_user_regSuccess")
	 */
	public function regSuccessAction(){
		return $this->render('JiliApiBundle:User:regSuccess.html.twig');
	}
	
	
	
	
	/**
	 * @Route("/forgetPass/{code}/{id}", name="_user_forgetPass")
	 */
	public function forgetPassAction($code,$id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findOneByUserId($id);
		if($setPasswordCode->getIsAvailable()==0){
			return $this->render('JiliApiBundle::error.html.twig');
		}
		$arr['pwdcode'] = $setPasswordCode;
		$time = $setPasswordCode->getCreateTime();
        if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24){
        	return $this->render('JiliApiBundle::error.html.twig');
        }else{
        	if($setPasswordCode->getCode() == $code){
        		$request = $this->get('request');
        		if ($request->getMethod() == 'POST'){
        			if($request->request->get('ck')=='1'){
        				if($request->request->get('pwd') == $request->request->get('que_pwd')){
//         					$pwd_flag = $user->getPwd();
        					$this->get('request')->getSession()->set('uid',$id);
        					$this->get('request')->getSession()->set('nick',$user->getNick());
        					$user->setPwd($request->request->get('pwd'));
        					$setPasswordCode->setIsAvailable($this->container->getParameter('init'));
        					$em->persist($user);
        					$em->persist($setPasswordCode);
        					$em->flush();
//         					if($user->getPwd()==''){
//         						echo '第一次注册';
//         					}else{
//         						echo '重置密码';
//         					}
        					return $this->render('JiliApiBundle:User:regSuccess.html.twig',$arr);
        				}else{
        					echo 'check pwd same';
        				}
        			}else{
        				echo 'choose agree';
        			}
        		}
        		return $this->render('JiliApiBundle:User:forgetPass.html.twig',$arr);
        	}
        }
	}
	    
	/**
	* @Route("/mission/{id}", name="_user_mission")
	*/
	public function missionAction($id){
//         $id =1;
		$str = 'jiliforgetpassword';
		$code = md5($id.str_shuffle($str));
// 		$request = $this->get('request');
		$email = '278583642@qq.com';
		$nick = '';
		$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($this->sendMail($url, $email,$nick)){
			$setPasswordCode = new setPasswordCode();
			$setPasswordCode->setUserId($user->getId());
			$setPasswordCode->setCode($code);
			$em->persist($setPasswordCode);
		    $em->flush();
			echo 'success';
		}

    	return $this->render('JiliApiBundle:User:mission.html.twig');
	}
	
	//reset pwd send mail
	public function sendMail_reset($url,$email,$nick){
		$message = \Swift_Message::newInstance()
		->setSubject('亲爱的#'.$nick."#")
		->setFrom('admin@91jili.com')
		->setTo($email)
		->setBody(
				'<html>' .
				' <head></head>' .
				' <body>' .
				'  我们收到您因为忘记密码，要求重置积粒网帐号密码的申请，请点击以下链接重置您的密码。</br><a href='.$url.'>'.$url.'</a></br>' .
				'  如果您并未提交重置密码的申请，请忽略本邮件，并关注您的账号安全，因为可能有其他人试图登录您的账户。</br>积粒网运营中心' .
				' </body>' .
				'</html>',
				'text/html'
		);
		$flag = $this->get('mailer')->send($message);
		if($flag===1){
			return true;
		}else{
			return false;
		}
	
	}
	
	

	public function sendMail($url,$email,$nick){
		$message = \Swift_Message::newInstance()
		->setSubject('亲爱的#'.$nick."#")
		->setFrom('signup@91jili.com')
		->setTo($email)
		->setBody(
				        '<html>' .
						' <head></head>' .
						' <body>' .
						'  感谢您注册成为“积粒网”会员！请点击以下链接，立即激活您的帐户！</br><a href='.$url.'>'.$url.'</a></br>' .
						'  积粒网，一站式积分媒体！</br>赚米粒，攒米粒，花米粒，一站搞定！' .
						' </body>' .
						'</html>',
						'text/html'
		);
		$flag = $this->get('mailer')->send($message);
		if($flag===1){
			return true;
		}else{
			return false;
		}
	
	}
	
}
