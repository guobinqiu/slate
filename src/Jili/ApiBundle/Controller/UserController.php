<?php
namespace Jili\ApiBundle\Controller;
use Gregwar\CaptchaBundle\GregwarCaptchaBundle;
use Symfony\Component\HttpFoundation\Session\Session;
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
// 		$id = 48;
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
		$request = $this->get('request');
		$pwd = $request->query->get('pwd');
		$id = $this->get('request')->getSession()->get('uid');
// 		$id = 48;
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
// 		if ($request->getMethod() == 'POST') {
        if($pwd){
    		$user->setPwd($pwd);
    		$em->flush();
    		$code = $this->container->getParameter('init_one');
        }else{
        	$code = $this->container->getParameter('init');
        }
		return new Response($code);
// 		}
// 		return $this->render('JiliApiBundle:User:changePwd.html.twig');
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
		$this->get('request')->getSession()->remove('uid');
		$this->get('request')->getSession()->remove('nick');
		return $this->redirect($this->generateUrl('_default_index'));
	}
	

	/**
	 * @Route("/login", name="_user_login")
	 */
	public function loginAction(){
		$request = $this->get('request');
		$email = $request->request->get('email');
		$pwd = $request->request->get('pwd');
		$em_email = $this->getDoctrine()
		->getRepository('JiliApiBundle:User')
		->findByEmail($email);
		$request = $this->get('request');
		if ($request->getMethod() == 'POST'){
		    if($this->get('request')->getSession()->get('phrase') != $request->request->get('captcha')){
		    	echo 'captcha is error!';
		    }else{
		    	$this->get('request')->getSession()->remove('phrase');
		    	if(!$em_email){
		    		echo 'email is unexist!';
		    	}else{
		    		$id = $em_email[0]->getId();
		    		$em = $this->getDoctrine()->getEntityManager();
		    		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		    		if($user->pw_encode($pwd) != $user->getPwd()){
		    			echo 'pwd is error!';
		    		}else{
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
		}
		return $this->render('JiliApiBundle:User:login.html.twig',array(
// 				'form' => $form->createView(),
				));
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
        					if($user->getPwd()==''){
        						echo '第一次注册';
        					}else{
        						echo '重置密码';
        					}
        					$this->get('request')->getSession()->set('uid',$id);
        					$this->get('request')->getSession()->set('nick',$user->getNick());
        					$user->setPwd($request->request->get('pwd'));
        					$setPasswordCode->setIsAvailable($this->container->getParameter('init'));
        					$em->persist($user);
        					$em->persist($setPasswordCode);
        					$em->flush();
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
